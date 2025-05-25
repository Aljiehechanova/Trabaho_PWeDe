<?php
require_once '../config/db_connection.php';

try {
    $stmt = $conn->prepare("SELECT * FROM jobpost");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client List</title>
    <link rel="stylesheet" href="../assets/css/dashboardstyle.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/clientlist.css">
</head>
<body>
<div class="sidebar">
    <ul>
        <li class="active"><a href="clientL.php">View Job List</a></li>
        <li><a href="posting.php">Posting</a></li>
        <li><a href="clientD.php">Analytic Dashboard</a></li>
        <li><a href="clientM.php">Messages</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Posted Jobs</h1>

    <?php if (!empty($jobs)): ?>
        <?php foreach ($jobs as $job): ?>
            <div class="job-card">
                <h3><?= htmlspecialchars($job['jobpost_title']) ?></h3>
                <p><strong>Disability Type:</strong> <?= htmlspecialchars($job['disability_requirement']) ?></p>
                <p><strong>Required Skills:</strong> <?= htmlspecialchars($job['skills_requirement']) ?></p>
                <p><strong>Optional Skills:</strong> <?= htmlspecialchars($job['optional_skills'] ?? 'N/A') ?></p>
                <button class="match-btn" onclick="viewMatches(<?= $job['jobpost_id'] ?>)">View Matching Applicants</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No job posts found.</p>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="matchingModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div id="modal-body">
        </div>
    </div>
</div>

<script>
function viewMatches(jobId) {
    fetch(`fmu.php?job_id=${jobId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('modal-body').innerHTML = data;
            document.getElementById('matchingModal').style.display = 'flex';
        })
        .catch(error => {
            document.getElementById('modal-body').innerHTML = "Error fetching data.";
            document.getElementById('matchingModal').style.display = 'flex';
        });
}

function closeModal() {
    document.getElementById('matchingModal').style.display = 'none';
}
</script>

</body>
</html>
