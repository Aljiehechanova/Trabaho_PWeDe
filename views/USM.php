<?php
require '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender = $_POST['sender_email'];
    $receiver = $_POST['receiver_email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (sender_email, receiver_email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$sender, $receiver, $subject, $message]);

    header("Location: clientM.php?success=1");
    exit;
}
?>
