<?php
session_start();
include '../config/db_connection.php';

// Fetch user details
$user_id = 5;  // Based on the database entry provided
$stmt = $conn->prepare("SELECT disability FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$disability = $user['disability'] ?? '';

// Fetch recommended jobs based on disability and skills
$query = "SELECT jobpost_id, jobpost_title, disability_requirement, skills_requirement 
          FROM jobpost 
          WHERE disability_requirement = ?";

$stmt = $conn->prepare($query);
$stmt->execute([$disability]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Matching - Trabaho PWeDe</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/job_matching.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container-fluid">
  <a class="navbar-brand d-flex align-items-center" href="UserD.php">
    <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
    <span class="fw-bold">TrabahoPWeDe</span>
  </a>
    <div class="ms-auto">
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="settingsMenu" data-bs-toggle="dropdown" aria-expanded="false">
          Settings
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsMenu">
          <li><a class="dropdown-item" href="#">Edit Profile</a></li>
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
          <li><a href="userM.php">Messages</a></li>
      </ul>
  </div>
  <div class="job-matching-wrapper"></div>
    <div class="main-content">
        <h1>Recommended Jobs for You</h1>

        <div class="job-list">
            <?php if (!empty($jobs)) : ?>
                <?php foreach ($jobs as $job) : ?>
                    <div class="job-card">
                        <h3><?php echo htmlspecialchars($job['jobpost_title'] ?? 'N/A'); ?></h3>
                        <p><strong>Disability Requirement:</strong> <?php echo htmlspecialchars($job['disability_requirement'] ?? 'N/A'); ?></p>
                        <p><strong>Skills Requirement:</strong> <?php echo htmlspecialchars($job['skills_requirement'] ?? 'N/A'); ?></p>
                        <a href="job_details.php?jobpost_id=<?php echo $job['jobpost_id'] ?? '#'; ?>" 
                          class="btn btn-primary">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No recommended jobs found for your profile.</p>
            <?php endif; ?>
        </div>
    </div>
  </div>
</div>

</body>
</html>
