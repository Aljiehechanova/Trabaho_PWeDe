<?php
require_once '../config/db_connection.php';

// Fetch job stats
try {
    // Total jobs
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_jobs FROM jobpost");
    $stmt->execute();
    $total_jobs = $stmt->fetch(PDO::FETCH_ASSOC)['total_jobs'];

    // Number of unique disability types
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT disability_requirement) AS disability_count FROM jobpost");
    $stmt->execute();
    $disability_count = $stmt->fetch(PDO::FETCH_ASSOC)['disability_count'];

    // Most common disability
    $stmt = $conn->prepare("
        SELECT disability_requirement, COUNT(*) as count
        FROM jobpost
        GROUP BY disability_requirement
        ORDER BY count DESC
        LIMIT 1
    ");
    $stmt->execute();
    $most_common_disability = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboardstyle.css">
    <link rel="stylesheet" href="../assets/css/global.css">
</head>
<body>
<div class="sidebar">
        <div class="logo">
            <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Trabaho PWeDe">
        </div>
        <ul>
            <li><a href="clientL.php">View Job List</a></li>
            <li><a href="posting.php">Posting</a></li>
            <li class="active"><a href="clientD.php">Analytic Dashboard</a></li>
            <li><a href="clientM.php">Messages</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Client Analytic Dashboard</h1>
        <div class="analytics">
            <div class="analytics-box">
                <h3>Total Jobs Available</h3>
                <p><?= $total_jobs ?></p>
            </div>
            <div class="analytics-box">
                <h3>Number of Disability Types</h3>
                <p><?= $disability_count ?></p>
            </div>
            <div class="analytics-box">
                <h3>Most Common Disability in Jobs</h3>
                <p><?= htmlspecialchars($most_common_disability['disability_requirement'] ?? 'N/A') ?></p>
            </div>
        </div>
    </div> 
</body>
</html>