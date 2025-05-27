<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jobpost_id'])) {
    $jobpost_id = $_POST['jobpost_id'];

    // Check if already applied
    $check = $conn->prepare("SELECT * FROM apply WHERE user_id = ? AND jobpost_id = ?");
    $check->execute([$user_id, $jobpost_id]);
    if ($check->rowCount() > 0) {
        $_SESSION['message'] = "You have already applied for this job.";
    } else {
        $stmt = $conn->prepare("INSERT INTO apply (user_id, jobpost_id) VALUES (?, ?)");
        if ($stmt->execute([$user_id, $jobpost_id])) {
            $_SESSION['message'] = "Application submitted successfully!";
        } else {
            $_SESSION['message'] = "Failed to apply for job.";
        }
    }
}

header("Location: JM.php");
exit;
?>
