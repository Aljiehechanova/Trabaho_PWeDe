<?php
require_once '../config/db_connection.php';

if (!isset($_GET['job_id'])) {
    http_response_code(400);
    echo "Missing job_id parameter.";
    exit;
}

$jobId = intval($_GET['job_id']);

try {
    // Get the disability requirement for the job
    $stmt = $conn->prepare("SELECT disability_requirement FROM jobpost WHERE jobpost_id = :job_id");
    $stmt->bindParam(':job_id', $jobId);
    $stmt->execute();
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        echo "<p>Job not found.</p>";
        exit;
    }

    $disabilityRequirement = $job['disability_requirement'];

    $stmt = $conn->prepare("SELECT user_id, fullname, email, disability, resume FROM users WHERE disability = :disability");
    $stmt->bindParam(':disability', $disabilityRequirement);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($users)) {
        echo "<p>No matching applicants found.</p>";
    } else {
        // Styles for layout
        echo "<style>
            .applicant-card {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 20px;
                padding: 15px;
                border: 1px solid #cce5cc;
                border-radius: 10px;
                margin-bottom: 15px;
                background-color: #ffffff;
            }
            .applicant-info {
                flex-grow: 1;
            }
            .resume-preview {
                width: 400px;
                height: 500px;
                overflow: hidden;
                border: 1px solid #ccc;
                border-radius: 10px;
                background-color: #f8f9fa;
            }
            .btn-action {
                margin-top: 10px;
            }
            iframe {
                width: 100%;
                height: 100%;
                border: none;
            }
        </style>";

        foreach ($users as $user) {
            echo "<div class='applicant-card'>";

            echo "<div class='applicant-info'>";
            echo "<h4>" . htmlspecialchars($user['fullname']) . "</h4>";
            echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
            echo "<p>Disability: " . htmlspecialchars($user['disability']) . "</p>";

            echo "<form method='POST' action='hire_user.php' class='d-flex gap-2 flex-wrap'>";
            echo "<input type='hidden' name='user_id' value='" . $user['user_id'] . "'>";
            echo "<input type='hidden' name='job_id' value='" . $jobId . "'>";
            echo "<button type='submit' name='action' value='hire' class='btn btn-primary btn-sm'>Hire</button>";
            echo "<button type='submit' name='action' value='onhold' class='btn btn-warning btn-sm'>On Hold</button>";
            echo "</form>";

            // ✅ Show the View Resume button only if resume path exists
            if (!empty($user['resume'])) {
                $resumePath = htmlspecialchars($user['resume']);
                echo "<a href='../../$resumePath' target='_blank' class='btn btn-success btn-sm mt-2'>View Resume</a>";
            }

            echo "</div>"; // .applicant-info
            echo "</div>"; // .applicant-card
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
}
?>
