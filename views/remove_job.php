<?php
session_start();
require_once '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jobpost_id'])) {
    $jobpost_id = $_POST['jobpost_id'];

    try {
        // Start transaction
        $conn->beginTransaction();

        // Delete from apply table
        $stmt1 = $conn->prepare("DELETE FROM apply WHERE jobpost_id = ?");
        $stmt1->execute([$jobpost_id]);

        // Delete from jobpost table
        $stmt2 = $conn->prepare("DELETE FROM jobpost WHERE jobpost_id = ?");
        $stmt2->execute([$jobpost_id]);

        $conn->commit();

        // Redirect back to the job list page for clients
        header("Location: clientL.php?deleted=1");
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        die("Error removing job post: " . $e->getMessage());
    }
} else {
    // Invalid access
    header("Location: clientL.php");
    exit;
}
?>
