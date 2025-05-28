<?php
require_once '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit("Invalid request method.");
}

// Validate inputs
$userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
$jobId = isset($_POST['job_id']) ? intval($_POST['job_id']) : null;
$action = $_POST['action'] ?? null;

if (!$userId || !$jobId || !$action) {
    exit("Error: Missing required data (user_id, job_id, action).");
}

// Determine status and message
$statusMap = [
    'hire' => ['Hired', 'Congratulations! You have been hired for a job you matched with.'],
    'onhold' => ['On Hold', 'Your application is on hold for a job you matched with.'],
];

if (!isset($statusMap[$action])) {
    exit("Error: Invalid action.");
}

$status = $statusMap[$action][0];
$message = $statusMap[$action][1];

try {
    // Check if the jobstage already exists
    $stmt = $conn->prepare("SELECT 1 FROM jobstages WHERE user_id = ? AND job_id = ?");
    $stmt->execute([$userId, $jobId]);

    if ($stmt->fetchColumn()) {
        // Update existing jobstage
        $stmt = $conn->prepare("UPDATE jobstages SET status = ?, date_updated = NOW() WHERE user_id = ? AND job_id = ?");
        $stmt->execute([$status, $userId, $jobId]);
    } else {
        // Insert new jobstage
        $stmt = $conn->prepare("INSERT INTO jobstages (user_id, job_id, status) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $jobId, $status]);
    }

    // Insert notification
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$userId, $message]);

    echo "<p>Status updated to <strong>$status</strong>. Notification sent.</p>";
    echo "<script>setTimeout(() => window.location.href = document.referrer || 'listofapplicant.php', 1500);</script>";

} catch (PDOException $e) {
    http_response_code(500);
    echo "Database Error: " . htmlspecialchars($e->getMessage());
}
