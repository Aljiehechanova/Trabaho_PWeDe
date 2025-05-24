<?php
session_start();
require_once '../config/db_connection.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
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

// Fetch dashboard analytics
try {
    $stmt = $conn->prepare("SELECT disability_requirement FROM jobpost");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $total_jobs = count($jobs);
    $disability_counts = array_count_values($jobs);
    $num_disability_types = count($disability_counts);

    arsort($disability_counts);
    $most_common_disability = array_key_first($disability_counts);
    $most_common_count = $disability_counts[$most_common_disability];

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboardstyle.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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


<!-- Wrapper for Sidebar and Main Content -->
<div class="d-flex" style="margin-top: 70px;"> <!-- Add top margin to avoid overlap with fixed navbar -->
  <div class="sidebar">
    <ul>
      <li><a href="userPE.php">Profile Enhancer</a></li>
      <li><a href="JM.php">Job Matching</a></li>
      <li class="active"><a href="userD.php">Analytic Dashboard</a></li>
      <li><a href="userM.php">Messages</a></li>
    </ul>
  </div>

  <div class="main-content flex-grow-1 p-3">
    <h1>Client Analytics Dashboard</h1>
    <div class="analytics">
      <div class="analytics-box">
        <h3>Total Jobs Available</h3>
        <p><?= $total_jobs ?></p>
      </div>
      <div class="analytics-box">
        <h3>Number of Disability Types</h3>
        <p><?= $num_disability_types ?></p>
      </div>
      <div class="analytics-box">
        <h3>Most Common Disability in Jobs</h3>
        <p><?= htmlspecialchars($most_common_disability) ?> (<?= $most_common_count ?> postings)</p>
      </div>
    </div>
  </div>
</div>

</body>
</html>
