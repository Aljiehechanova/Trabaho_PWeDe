<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM jobpost");
    $stmt->execute();
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
    <title>Admin Approval - Trabaho PWeDe</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/dashboardstyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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

      <!-- Notification bell -->
      <div class="dropdown me-3">
        <button class="btn btn-light position-relative" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-bell fs-5"></i>
          <?php if (!empty($notifications)): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              <?= count($notifications) ?>
            </span>
          <?php endif; ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown" style="width: 300px;">
          <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notif): ?>
              <li class="px-3 py-2 border-bottom">
                <small class="text-muted"><?= htmlspecialchars($notif['created_at']) ?></small><br>
                <?= htmlspecialchars($notif['message']) ?>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="dropdown-item text-muted">No new notifications</li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Profile and settings -->

      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="settingsMenu" data-bs-toggle="dropdown" aria-expanded="false">
          Settings
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsMenu">
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
            <li><a href="approve_job.php">Approval Job List</a></li>
            <li class="active"><a href="adapp.php">Appointment</a></li>
            <li><a href="addash.php">Admin Dashboard</a></li>
            <li><a href="adme.php">Messages</a></li>
        </ul>
    </div>
    <div class="main-content">
    <h2>Admin Approval</h2>
    <p>Approve or reject job postings and manage interview appointments.</p>

    <table class="table table-bordered mt-4">
        <thead class="table-light">
            <tr>
                <th>Job Title</th>
                <th>Disability Requirement</th>
                <th>Required Skills</th>
                <th>Appointment</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($jobs as $job): ?>
            <?php
            // Fetch appointment info for each job
            $stmt = $conn->prepare("SELECT * FROM job_appointments WHERE jobpost_id = ?");
            $stmt->execute([$job['jobpost_id']]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <tr>
                <td><?= htmlspecialchars($job['jobpost_title']) ?></td>
                <td><?= htmlspecialchars($job['disability_requirement']) ?></td>
                <td><?= htmlspecialchars($job['skills_requirement']) ?></td>
                <td>
                    <?php if ($appointment): ?>
                        <?= htmlspecialchars($appointment['appointment_date'] ?? 'N/A') ?> 
                        <?= htmlspecialchars($appointment['appointment_time'] ?? '') ?>
                    <?php else: ?>
                        <em>No appointment</em>
                    <?php endif; ?>
                </td>
                <td><?= $appointment['status'] ?? 'Pending' ?></td>
                <td>
                    <form action="approve_job.php" method="POST" style="display:inline-block;">
                        <input type="hidden" name="jobpost_id" value="<?= $job['jobpost_id'] ?>">
                        <button class="btn btn-success btn-sm" name="action" value="approve">Approve</button>
                    </form>
                    <form action="approve_job.php" method="POST" style="display:inline-block;">
                        <input type="hidden" name="jobpost_id" value="<?= $job['jobpost_id'] ?>">
                        <button class="btn btn-danger btn-sm" name="action" value="reject">Reject</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>