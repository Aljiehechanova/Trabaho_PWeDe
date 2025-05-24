<?php
require_once '../config/db_connection.php';

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
</head>
    <div class="sidebar">
        <div class="logo">
            <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Trabaho PWeDe">
        </div>
        <ul>
            <li><a href="userPE.php">Profile Enhancer</a></li>
            <li><a href="JM.php">Job Matching</a></li>
            <li class="active"><a href="userD.php">Analytic Dashboard</a></li>
            <li><a href="userM.php">Messages</a></li>
        </ul>
    </div>
    <div class="main-content">
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
</body>
</html>