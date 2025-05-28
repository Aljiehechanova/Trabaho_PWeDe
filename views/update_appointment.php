<?php
require_once '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobpost_id = $_POST['jobpost_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    try {
        $stmt = $conn->prepare("UPDATE job_appointments SET appointment_date = ?, appointment_time = ?, status = 'Scheduled' WHERE jobpost_id = ?");
        $stmt->execute([$date, $time, $jobpost_id]);
        header("Location: adapp.php");
    } catch (PDOException $e) {
        echo "Update failed: " . $e->getMessage();
    }
}
?>
