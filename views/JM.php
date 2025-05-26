<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT disability FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$disability = $user['disability'] ?? '';

$query = "SELECT jobpost_id, jobpost_title, disability_requirement, skills_requirement 
          FROM jobpost 
          WHERE disability_requirement = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$disability]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

try {
  $stmt = $conn->prepare("SELECT fullname, img FROM users WHERE user_id = ?");
  $stmt->execute([$user_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$user) {
      die("User not found.");
  }
} catch (PDOException $e) {
  die("User fetch error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Job Matching - Trabaho PWeDe</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="../assets/css/job_matching.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="UserD.php">
      <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
      <span class="fw-bold">TrabahoPWeDe</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <a href="userP.php" class="d-flex align-items-center text-decoration-none me-3">
        <img src="<?= htmlspecialchars($user['img']) ?>" alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit: cover; margin-right: 10px;">
        <span class="fw-semibold text-dark"><?= htmlspecialchars($user['fullname']) ?></span>
      </a>
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="settingsMenu" data-bs-toggle="dropdown" aria-expanded="false">
          Settings
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsMenu">
          <li><a class="dropdown-item" href="userP.php">Edit Profile</a></li>
          <li><a class="dropdown-item" href="#">Change Password</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="login.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<div class="d-flex" style="margin-top: 70px;">
  <div class="sidebar">
    <ul>
      <li><a href="userPE.php">Profile Enhancer</a></li>
      <li class="active"><a href="JM.php">Job Matching</a></li>
      <li><a href="userD.php">Analytic Dashboard</a></li>
      <li><a href="userM.php">Inbox</a></li>
    </ul>
  </div>
  <div class="job-matching-wrapper">
    <div class="main-content">
      <h1>Recommended Jobs for You</h1>
      <div class="job-list">
        <?php if (!empty($jobs)) : ?>
          <?php foreach ($jobs as $job) : ?>
            <div class="job-card">
              <h3><?= htmlspecialchars($job['jobpost_title']) ?></h3>
              <p><strong>Disability Requirement:</strong> <?= htmlspecialchars($job['disability_requirement']) ?></p>
              <p><strong>Skills Requirement:</strong> <?= htmlspecialchars($job['skills_requirement']) ?></p>
              <button class="btn btn-primary view-details-btn" data-id="<?= $job['jobpost_id'] ?>">View Details</button>
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <p>No recommended jobs found for your profile.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="jobDetailsModal" tabindex="-1" aria-labelledby="jobDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="jobDetailsModalLabel">Job Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="jobDetailsContent">
        <p>Loading...</p>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const buttons = document.querySelectorAll(".view-details-btn");
  buttons.forEach(button => {
    button.addEventListener("click", function () {
      const jobpostId = this.getAttribute("data-id");
      fetch(`fjd.php?jobpost_id=${jobpostId}`)
        .then(response => response.text())
        .then(data => {
          document.getElementById("jobDetailsContent").innerHTML = data;
          const modal = new bootstrap.Modal(document.getElementById('jobDetailsModal'));
          modal.show();
        })
        .catch(error => {
          console.error("Error fetching job details:", error);
          document.getElementById("jobDetailsContent").innerHTML = "<p>Error loading details.</p>";
        });
    });
  });
});
</script>
</body>
</html>
