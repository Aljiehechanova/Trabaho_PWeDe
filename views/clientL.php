<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

// Optional: Enforce that only clients can access this page
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
    die("Access denied: Only clients can access this page.");
}

try {
    // ✅ Fix: Changed client_id to user_id
    $stmt = $conn->prepare("SELECT * FROM jobpost WHERE user_id = ?");
    $stmt->execute([$client_id]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

try {
    $stmt = $conn->prepare("SELECT fullname, img FROM users WHERE user_id = ?");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        die("Client not found.");
    }
} catch (PDOException $e) {
    die("Client fetch error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Job List</title>
    <link rel="stylesheet" href="../assets/css/dashboardstyle.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/clientlist.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="clientD.php">
      <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
      <span class="fw-bold">TrabahoPWeDe</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <a href="clientP.php" class="d-flex align-items-center text-decoration-none me-3">
        <img src="<?= htmlspecialchars($client['img']) ?>" alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit: cover; margin-right: 10px;">
        <span class="fw-semibold text-dark"><?= htmlspecialchars($client['fullname']) ?></span>
      </a>
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          Settings
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">Edit Profile</a></li>
          <li><a class="dropdown-item" href="#">Change Password</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="login.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<div class="layout-container">
    <div class="sidebar">
        <ul>
            <li class="active"><a href="clientL.php">View Job List</a></li>
            <li><a href="clientW.php">View Workshop Volunteer</a></li>
            <li><a href="listofapplicant.php">View List of Applicants</a></li>
            <li><a href="posting.php">Posting</a></li>
            <li><a href="clientD.php">Analytic Dashboard</a></li>
            <li><a href="clientM.php">Inbox</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Posted Jobs</h1>

        <?php if (!empty($jobs)): ?>
            <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <h3><?= htmlspecialchars($job['jobpost_title']) ?></h3>
                    <p><strong>Disability Type:</strong> <?= htmlspecialchars($job['disability_requirement']) ?></p>
                    <p><strong>Required Skills:</strong> <?= htmlspecialchars($job['skills_requirement']) ?></p>
                    <p><strong>Optional Skills:</strong> <?= htmlspecialchars($job['optional_skills'] ?? 'N/A') ?></p>
                    <button class="match-btn" onclick="viewMatches(<?= $job['jobpost_id'] ?>)">View Matching Applicants</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No job posts found.</p>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="matchingModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <div id="modal-body">
            </div>
        </div>
    </div>
</div>

<script>
function viewMatches(jobId) {
    fetch(`fmu.php?job_id=${jobId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('modal-body').innerHTML = data;
            document.getElementById('matchingModal').style.display = 'flex';
        })
        .catch(error => {
            document.getElementById('modal-body').innerHTML = "Error fetching data.";
            document.getElementById('matchingModal').style.display = 'flex';
        });
}

function closeModal() {
    document.getElementById('matchingModal').style.display = 'none';
}
</script>

</body>
</html>
