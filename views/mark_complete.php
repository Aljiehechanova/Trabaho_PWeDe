<?php
require_once '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];

    // Get the associated jobpost_id
    $stmt = $conn->prepare("SELECT jobpost_id FROM job_appointments WHERE appointment_id = ?");
    $stmt->execute([$appointment_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($appointment) {
        $jobpost_id = $appointment['jobpost_id'];

        // Mark the appointment as completed
        $stmt = $conn->prepare("UPDATE job_appointments SET status = 'Completed' WHERE appointment_id = ?");
        $stmt->execute([$appointment_id]);

        // ✅ Redirect to the correct page
        header("Location: adapp.php");
        exit;
    } else {
        echo "Invalid appointment.";
    }
} else {
    echo "Invalid request.";
}
