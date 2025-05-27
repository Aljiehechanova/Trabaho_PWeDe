<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    die("Access denied: Admins only.");
}

$admin_id = $_SESSION['user_id'];

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jobpost_id'], $_POST['action'])) {
    $jobpostId = $_POST['jobpost_id'];
    $action = $_POST['action'];

    if (!in_array($action, ['approve', 'reject'])) {
        die("Invalid action.");
    }

    try {
        $stmt = $conn->prepare("UPDATE jobpost SET status = ?, approved_by = ? WHERE jobpost_id = ?");
        $stmt->execute([$action, $admin_id, $jobpostId]);
        header("Location: approve_job.php");
        exit;
    } catch (PDOException $e) {
        die("Approval error: " . $e->getMessage());
    }
}

// Fetch only pending job posts
try {
    $stmt = $conn->prepare("
        SELECT 
            jp.jobpost_id, jp.jobpost_title, jp.disability_requirement, 
            jp.years_experience, jp.skills_requirement, jp.optional_skills, 
            jp.status, u.company 
        FROM jobpost jp
        JOIN users u ON jp.user_id = u.user_id
        WHERE jp.status = 'pending'
        ORDER BY jp.jobpost_id DESC
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
    <title>Manage Job Post Approvals - Trabaho PWeDe</title>
    <link rel="stylesheet" href="../assets/css/dashboardstyle.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="addash.php">
            <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
            <span class="fw-bold">TrabahoPWeDe</span>
        </a>
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
        <h2>Manage Job Post Approvals</h2>
        <p>Only pending job posts appear below for approval or rejection.</p>

        <table class="table table-bordered mt-4">
            <thead class="table-light">
                <tr>
                    <th>Job Title</th>
                    <th>Disability Requirement</th>
                    <th>Years Experience</th>
                    <th>Skills Required</th>
                    <th>Optional Skills</th>
                    <th>Company</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($jobs as $job): ?>
                <tr>
                    <td><?= htmlspecialchars($job['jobpost_title']) ?></td>
                    <td><?= htmlspecialchars($job['disability_requirement']) ?></td>
                    <td><?= htmlspecialchars($job['years_experience']) ?></td>
                    <td><?= htmlspecialchars($job['skills_requirement']) ?></td>
                    <td><?= htmlspecialchars($job['optional_skills']) ?></td>
                    <td><?= htmlspecialchars($job['company']) ?></td>
                    <td>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="jobpost_id" value="<?= $job['jobpost_id'] ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                        </form>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="jobpost_id" value="<?= $job['jobpost_id'] ?>">
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
