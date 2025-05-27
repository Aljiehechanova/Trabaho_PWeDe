<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user profile and notifications
try {
    $stmt = $conn->prepare("SELECT fullname, img FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $notif_stmt = $conn->prepare("SELECT message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $notif_stmt->execute([$user_id]);
    $notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }
} catch (PDOException $e) {
    die("User fetch error: " . $e->getMessage());
}

// Filters
$filters = [];
$params = [];

// Base query
$query = "
    SELECT 
        jobpost.jobpost_id, 
        jobpost.jobpost_title, 
        jobpost.disability_requirement, 
        jobpost.skills_requirement,
        users.fullname AS employer_name,
        users.img AS employer_img
    FROM jobpost
    JOIN users ON jobpost.user_id = users.user_id
    WHERE 1=1
";


// Apply location filter (barangay)
if (!empty($_GET['location'])) {
    $filters[] = "location = ?";
    $params[] = $_GET['location'];
}

// Apply job title filter
if (!empty($_GET['jobTitle'])) {
    $filters[] = "jobpost_title = ?";
    $params[] = $_GET['jobTitle'];
}

// Append filters to query
if (!empty($filters)) {
    $query .= " AND " . implode(" AND ", $filters);
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Matching - Trabaho PWeDe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="../assets/css/job_matching.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="UserD.php">
      <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
      <span class="fw-bold">Trabaho</span><span class="fw-bold" style="color: blue">PWeDe</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <div class="dropdown me-3">
        <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown">
          <i class="bi bi-bell fs-5"></i>
          <?php if (!empty($notifications)) : ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              <?= count($notifications) ?>
            </span>
          <?php endif; ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
          <?php foreach ($notifications as $notif): ?>
            <li class="px-3 py-2 border-bottom">
              <small class="text-muted"><?= htmlspecialchars($notif['created_at']) ?></small><br>
              <?= htmlspecialchars($notif['message']) ?>
            </li>
          <?php endforeach; ?>
          <?php if (empty($notifications)) : ?>
            <li class="dropdown-item text-muted">No new notifications</li>
          <?php endif; ?>
        </ul>
      </div>

      <a href="userP.php" class="d-flex align-items-center text-decoration-none me-3">
        <img src="<?= htmlspecialchars($user['img']) ?>" alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit: cover; margin-right: 10px;">
        <span class="fw-semibold text-dark"><?= htmlspecialchars($user['fullname']) ?></span>
      </a>

      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
          Settings
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="userP.php">Edit Profile</a></li>
          <li><a class="dropdown-item" href="#">Change Password</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="login.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<div class="layout-container">
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

      <form method="GET" class="row g-3 mb-4">
        <div class="col-md-6">
          <label for="location" class="form-label">Barangay in Bacolod City</label>
          <select id="location" name="location" class="form-select">
            <option value="">All</option>
            <?php
              $barangays = [
                "Barangay Alijis", "Barangay Banago", "Barangay Bata", "Barangay 1", "Barangay 2", "Barangay 3", "Barangay 4", "Barangay 5",
                "Barangay 6", "Barangay 7", "Barangay 8", "Barangay 9", "Barangay 10", "Barangay 11", "Barangay 12",
                "Barangay 13", "Barangay 14", "Barangay 15", "Barangay 16", "Barangay 17", "Barangay 18", "Barangay 19",
                "Barangay 20", "Barangay 21", "Barangay 22", "Barangay 23", "Barangay 24", "Barangay 25", "Barangay 26",
                "Barangay 27", "Barangay 28", "Barangay 29", "Barangay 30", "Barangay 31", "Barangay 32", "Barangay 33",
                "Barangay 34", "Barangay 35", "Barangay 36", "Barangay 37", "Barangay 38", "Barangay 39", "Barangay 40",
                "Barangay 41", "Barangay 42", "Barangay 43", "Barangay 44", "Barangay 45", "Barangay 46", "Barangay 47",
                "Barangay 48", "Barangay 49", "Barangay 50", "Barangay Estefania", "Barangay Granada", "Barangay Handumanan", "Barangay Mansilingan",
                "Barangay Montevista", "Barangay Pahanocoy", "Barangay Punta Taytay", "Barangay Singcang-Airport", "Barangay Sum-ag", "Barangay Taculing", "Barangay Tangub", "Barangay Villamonte", "Barangay Vista Alegre"
              ];

              foreach ($barangays as $barangay) {
                  $selected = ($_GET['location'] ?? '') === $barangay ? 'selected' : '';
                  echo "<option value=\"$barangay\" $selected>$barangay</option>";
              }
            ?>
          </select>
        </div>

        <div class="col-md-6">
          <label for="jobTitle" class="form-label">Job Title</label>
          <select id="jobTitle" name="jobTitle" class="form-select">
            <option value="">All</option>
            <option value="Data Encoder" <?= ($_GET['jobTitle'] ?? '') === 'Data Encoder' ? 'selected' : '' ?>>Data Encoder</option>
            <option value="Call Center Agent" <?= ($_GET['jobTitle'] ?? '') === 'Call Center Agent' ? 'selected' : '' ?>>Call Center Agent</option>
            <option value="Graphic Designer" <?= ($_GET['jobTitle'] ?? '') === 'Graphic Designer' ? 'selected' : '' ?>>Graphic Designer</option>
            <option value="Software Developer" <?= ($_GET['jobTitle'] ?? '') === 'Software Developer' ? 'selected' : '' ?>>Software Developer</option>
            <option value="Administrative Assistant" <?= ($_GET['jobTitle'] ?? '') === 'Administrative Assistant' ? 'selected' : '' ?>>Administrative Assistant</option>
            <option value="Freelance Writer" <?= ($_GET['jobTitle'] ?? '') === 'Freelance Writer' ? 'selected' : '' ?>>Freelance Writer</option>
            <option value="Customer Support Representative" <?= ($_GET['jobTitle'] ?? '') === 'Customer Support Representative' ? 'selected' : '' ?>>Customer Support Representative</option>
            <option value="Massage Therapist" <?= ($_GET['jobTitle'] ?? '') === 'Massage Therapist' ? 'selected' : '' ?>>Massage Therapist</option>
            <option value="Other" <?= ($_GET['jobTitle'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
          </select>
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary">Apply Filters</button>
        </div>
      </form>

      <div class="job-list">
        <?php if (!empty($jobs)) : ?>
          <?php foreach ($jobs as $job) : ?>
            <div class="job-card">
                <h3><?= htmlspecialchars($job['jobpost_title']) ?></h3>
                <p><strong>Disability Requirement:</strong> <?= htmlspecialchars($job['disability_requirement']) ?></p>
                <p><strong>Skills Requirement:</strong> <?= htmlspecialchars($job['skills_requirement']) ?></p>
                <button class="btn btn-primary view-details-btn" data-id="<?= $job['jobpost_id'] ?>">View Details</button>
                <form method="POST" action="apply_job.php" class="d-inline">
                  <input type="hidden" name="jobpost_id" value="<?= $job['jobpost_id'] ?>">
                  <button type="submit" class="btn btn-success">Apply</button>
                </form>
              </div>
          <?php endforeach; ?>
        <?php else : ?>
          <p>No recommended jobs found based on current filters.</p>
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
