<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

if ($_SESSION['user_type'] !== 'client') {
    header("Location: login.php");
    exit;
}

// Fetch client info
try {
    $stmt = $conn->prepare("SELECT fullname, img FROM users WHERE user_id = ?");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Client fetch error: " . $e->getMessage());
}

// Fetch all Hired and on-hold applicants for this client
try {
    $stmt = $conn->prepare("
        SELECT js.job_id, jp.jobpost_title, u.user_id, u.fullname, u.img, js.status 
        FROM jobstages js
        JOIN users u ON js.user_id = u.user_id
        JOIN jobpost jp ON js.job_id = jp.jobpost_id
        WHERE jp.user_id = ? AND js.status IN ('Hired', 'on-hold')
        ORDER BY js.date_updated DESC
    ");
    $stmt->execute([$client_id]);
    $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Applicant fetch error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Applicants</title>
    <link rel="stylesheet" href="../assets/css/dashboardstyle.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/clientlist.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

<div class="layout-container">
    <div class="sidebar">
        <ul>
            <li><a href="clientL.php">View Job List</a></li>
            <li><a href="clientW.php">View Workshop Volunteer</a></li>
            <li class="active"><a href="listofapplicant.php">View List of Applicants</a></li>
            <li><a href="posting.php">Posting</a></li>
            <li><a href="clientD.php">Analytic Dashboard</a></li>
            <li><a href="clientM.php">Inbox</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>All Applicants (Hired & On-Hold)</h1>

        <?php if (!empty($applicants)): ?>
            <?php foreach ($applicants as $applicant): ?>
                <div class="job-card d-flex align-items-center justify-content-between mb-3 p-3 border rounded shadow-sm">
                    <div class="d-flex align-items-center">
                        <img src="<?= htmlspecialchars($applicant['img']) ?>" alt="Profile" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                        <div>
                            <h5 class="mb-1"><?= htmlspecialchars($applicant['fullname']) ?></h5>
                            <p class="mb-0"><strong>Job Title:</strong> <?= htmlspecialchars($applicant['jobpost_title']) ?>)</p>
                            <p class="mb-0"><strong>Status:</strong> 
                                <?= htmlspecialchars($applicant['status'] === 'on-hold' ? 'On Hold' : $applicant['status']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hired or on-hold applicants found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
    