<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

// Fetch all job posts
try {
  $stmt = $conn->prepare("SELECT * FROM jobpost WHERE status = 'Pending'");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
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
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="settingsMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    Settings
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsMenu">
                    <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="layout-container">
    <div class="sidebar">
        <ul>
            <li class="active"><a href="approve_job.php">Approval Job List</a></li>
            <li><a href="adapp.php">Appointment</a></li>
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
                    // Fetch appointment info
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
                                <?= htmlspecialchars($appointment['appointment_date']) ?>
                                <?= htmlspecialchars($appointment['appointment_time']) ?>
                            <?php else: ?>
                                <form action="set_appointment.php" method="POST" class="d-flex flex-column gap-1">
                                    <input type="hidden" name="jobpost_id" value="<?= $job['jobpost_id'] ?>">
                                    <input type="hidden" name="client_id" value="<?= $job['user_id'] ?>">
                                    <input type="date" name="appointment_date" required>
                                    <input type="time" name="appointment_time" required>
                                    <button class="btn btn-primary btn-sm mt-1" type="submit">Set</button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td><?= $appointment['status'] ?? htmlspecialchars($job['status']) ?></td>
                        <td>
                            <?php if ($appointment && $appointment['status'] !== 'Completed'): ?>
                                <form action="mark_complete.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
                                    <button class="btn btn-warning btn-sm mt-1" type="submit">Complete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
