<?php
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $work_title = $_POST['work_title'] ?? '';
    $entry_date = $_POST['entry_date'] ?? '';
    $host = $_POST['host'] ?? '';

    try {
        // Find the matching workshop ID
        $stmt = $conn->prepare("SELECT workshop_id FROM workshop WHERE work_title = ? AND entry_date = ? AND hostname = ?");
        $stmt->execute([$work_title, $entry_date, $host]);
        $workshop = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($workshop) {
            $workshop_id = $workshop['workshop_id'];

            // Check if already volunteered
            $check = $conn->prepare("SELECT * FROM workshop_volunteers WHERE workshop_id = ? AND user_id = ?");
            $check->execute([$workshop_id, $user_id]);
            if ($check->rowCount() === 0) {
                // Insert into volunteers table
                $insert = $conn->prepare("INSERT INTO workshop_volunteers (workshop_id, user_id) VALUES (?, ?)");
                $insert->execute([$workshop_id, $user_id]);
                $_SESSION['message'] = "Successfully volunteered for the workshop.";
            } else {
                $_SESSION['message'] = "You have already volunteered for this workshop.";
            }
        } else {
            $_SESSION['message'] = "Workshop not found.";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
    }
}

header("Location: userPE.php");
exit;
