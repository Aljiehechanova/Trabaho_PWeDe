<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

try {
    // Fetch all pending job posts regardless of appointment status
    $stmt = $conn->prepare("
        SELECT jp.*, ja.appointment_id, ja.appointment_date, ja.appointment_time, ja.status AS appointment_status
        FROM jobpost jp
        LEFT JOIN job_appointments ja ON jp.jobpost_id = ja.jobpost_id
        WHERE jp.status = 'Pending'
    ");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Appointment - Trabaho PWeDe</title>
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
            <span class="fw-bold">Trabaho<span class="text-primary fw-bold">PWeDe</span></span>
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
            <li><a href="approve_job.php">Approval Job List</a></li>
            <li class="active"><a href="adapp.php">Appointment</a></li>
            <li><a href="addash.php">Admin Dashboard</a></li>
            <li><a href="adme.php">Messages</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Admin Appointment</h2>
        <p>Manage interview appointments.</p>

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
                    <tr>
                        <td><?= htmlspecialchars($job['jobpost_title']) ?></td>
                        <td><?= htmlspecialchars($job['disability_requirement']) ?></td>
                        <td><?= htmlspecialchars($job['skills_requirement']) ?></td>
                        <td>
                            <?php if (!empty($job['appointment_date']) && !empty($job['appointment_time'])): ?>
                                <?= htmlspecialchars($job['appointment_date']) ?> at <?= htmlspecialchars($job['appointment_time']) ?>
                            <?php else: ?>
                                <span class="text-muted">No appointment set</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($job['appointment_status'] ?? $job['status']) ?>
                        </td>
                        <td>
                            <?php if (!empty($job['appointment_id']) && $job['appointment_status'] !== 'Completed'): ?>
                                <form action="mark_complete.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="appointment_id" value="<?= $job['appointment_id'] ?>">
                                    <button class="btn btn-warning btn-sm mt-1" type="submit">Mark Complete</button>
                                </form>
                            <?php elseif (empty($job['appointment_id'])): ?>
                                <a href="set_appointment.php?jobpost_id=<?= $job['jobpost_id'] ?>" class="btn btn-success btn-sm mt-1">Set Appointment</a>
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
