<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['jobpost_id'])) {
    die("Missing job post ID.");
}

$jobpost_id = $_GET['jobpost_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    if (!$appointment_date || !$appointment_time) {
        $error = "Both date and time are required.";
    } else {
        try {
            // Check if appointment already exists for the jobpost
            $stmt = $conn->prepare("SELECT appointment_id FROM job_appointments WHERE jobpost_id = ?");
            $stmt->execute([$jobpost_id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Update existing appointment
                $stmt = $conn->prepare("UPDATE job_appointments SET appointment_date = ?, appointment_time = ?, status = 'Scheduled' WHERE appointment_id = ?");
                $stmt->execute([$appointment_date, $appointment_time, $existing['appointment_id']]);
            } else {
                // Insert new appointment
                $stmt = $conn->prepare("INSERT INTO job_appointments (jobpost_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, 'Scheduled')");
                $stmt->execute([$jobpost_id, $appointment_date, $appointment_time]);
            }

            header("Location: adapp.php");
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Set Appointment - Trabaho PWeDe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <h2>Set Interview Appointment</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="appointment_date" class="form-label">Appointment Date</label>
                <input type="date" name="appointment_date" id="appointment_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="appointment_time" class="form-label">Appointment Time</label>
                <input type="time" name="appointment_time" id="appointment_time" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Set Appointment</button>
            <a href="adapp.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
