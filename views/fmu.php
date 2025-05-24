<?php
require_once '../config/db_connection.php';

if (!isset($_GET['job_id'])) {
    http_response_code(400);
    echo "Missing job_id parameter.";
    exit;
}

$jobId = intval($_GET['job_id']);

try {
    $stmt = $conn->prepare("SELECT disability_requirement FROM jobpost WHERE jobpost_id = :job_id");
    $stmt->bindParam(':job_id', $jobId);
    $stmt->execute();
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        echo "<p>Job not found.</p>";
        exit;
    }

    $disabilityRequirement = $job['disability_requirement'];

    $stmt = $conn->prepare("SELECT user_id, fullname, email, disability FROM users WHERE disability = :disability");
    $stmt->bindParam(':disability', $disabilityRequirement);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($users)) {
        echo "<p>No matching applicants found.</p>";
    } else {
        foreach ($users as $user) {
            echo "<div class='applicant-card'>";
            echo "<h4>" . htmlspecialchars($user['fullname']) . "</h4>";
            echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
            echo "<p>Disability: " . htmlspecialchars($user['disability']) . "</p>";
            echo "<form method='POST' action='hire_user.php'>";
            echo "<input type='hidden' name='user_id' value='" . $user['user_id'] . "'>";
            echo "<input type='hidden' name='job_id' value='" . $jobId . "'>";
            echo "<button type='submit'>Hire</button>";
            echo "</form>";
            echo "</div>";
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
}
