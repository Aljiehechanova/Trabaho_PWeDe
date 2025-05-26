<?php
require_once '../config/db_connection.php';

if (!isset($_GET['jobpost_id'])) {
    echo "Invalid request.";
    exit;
}

$jobpost_id = $_GET['jobpost_id'];

$stmt = $conn->prepare("
    SELECT 
        j.jobpost_title,
        j.disability_requirement,
        j.skills_requirement,
        j.optional_skills,
        u.location,
        u.company
    FROM jobpost j
    INNER JOIN users u ON j.user_id = u.user_id
    WHERE j.jobpost_id = ?
");
$stmt->execute([$jobpost_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if ($job) {
    echo "<h4>" . htmlspecialchars($job['jobpost_title']) . "</h4>";
    echo "<p><strong>Disability Requirement:</strong> " . htmlspecialchars($job['disability_requirement']) . "</p>";
    echo "<p><strong>Skills Requirement:</strong> " . htmlspecialchars($job['skills_requirement']) . "</p>";
    echo "<p><strong>Optional Skills:</strong> " . nl2br(htmlspecialchars($job['optional_skills'])) . "</p>";
    echo "<p><strong>Location:</strong> " . htmlspecialchars($job['location']) . "</p>";
    echo "<p><strong>Company:</strong> " . htmlspecialchars($job['company']) . "</p>";
} else {
    echo "Job not found.";
}
?>
