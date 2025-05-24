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
</head>
<body>
<div class="top-navbar">
  <button onclick="location.href='userD.php'">User</button>
  <button onclick="location.href='clientD.php'">Client</button>
  <button onclick="location.href='addash.php'">Admin</button>
</div>
<div class="sidebar">
    <div class="logo">
        <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Trabaho PWeDe">
    </div>
    <ul>
        <li><a href="userPE.php">Profile Enhancer</a></li>
        <li class="active"><a href="JM.php">Job Matching</a></li>
        <li><a href="userD.php">Analytic Dashboard</a></li>
        <li><a href="userM.php">Messages</a></li>
    </ul>
</div>

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

</body>
</html>
