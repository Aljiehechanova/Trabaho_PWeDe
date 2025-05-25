<?php
require_once '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $jobId = $_POST['job_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$userId || !$jobId || !$action) {
        die("Missing required data.");
    }

    // Determine status and message
    $status = '';
    $message = '';

    if ($action === 'hire') {
        $status = 'Hired';
        $message = 'Congratulations! You have been hired for a job you matched with.';
    } elseif ($action === 'onhold') {
        $status = 'On Hold';
        $message = 'Your application is on hold for a job you matched with.';
    } else {
        die("Invalid action.");
    }

    try {
        // Insert or update application
        $stmt = $conn->prepare("SELECT * FROM applications WHERE applicant_id = ? AND job_id = ?");
        $stmt->execute([$userId, $jobId]);

        if ($stmt->rowCount() > 0) {
            $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE applicant_id = ? AND job_id = ?");
            $stmt->execute([$status, $userId, $jobId]);
        } else {
            $stmt = $conn->prepare("INSERT INTO applications (applicant_id, job_id, status) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $jobId, $status]);
        }

        // Send notification
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->execute([$userId, $message]);

        echo "<p>Status updated to <strong>$status</strong>. Notification sent.</p>";
        echo "<script>setTimeout(() => window.history.back(), 1500);</script>";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
