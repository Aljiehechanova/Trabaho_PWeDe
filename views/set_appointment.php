<?php
session_start();
require_once '../config/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $jobpost_id = $_POST['jobpost_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Get the user_id of the job owner
    $stmt = $conn->prepare("SELECT user_id FROM jobpost WHERE jobpost_id = ?");
    $stmt->execute([$jobpost_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        die("Job not found.");
    }

    $user_id = $job['user_id'];

    // Insert into job_appointments
    $stmt = $conn->prepare("INSERT INTO job_appointments (jobpost_id, appointment_date, appointment_time, status, created_at) VALUES (?, ?, ?, 'Scheduled', NOW())");
    $stmt->execute([$jobpost_id, $appointment_date, $appointment_time]);

    // Insert into notifications
    $message = "You have an interview scheduled on $appointment_date at $appointment_time.";
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $message]);

    header("Location: adapp.php");
    exit;
}
?>
