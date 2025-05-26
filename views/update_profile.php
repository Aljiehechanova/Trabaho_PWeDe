<?php
session_start();
include '../config/db_connection.php'; // Provides $conn (PDO instance)
include '../controllers/UserController.php';

// Input fields
$user_id = $_POST['user_id'] ?? null;
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$description = $_POST['description'] ?? '';
$location = $_POST['location'] ?? '';
$disability = $_POST['disability'] ?? '';

// FIX: Pass the PDO connection directly to the controller
$userController = new UserController($conn);

// Update profile using the controller
if ($userController->updateProfile($user_id, $fullname, $email, $description, $location, $disability)) {
    echo "<script>alert('Profile updated successfully!'); window.location.href = 'userP.php';</script>";
} else {
    echo "<script>alert('Failed to update profile.'); window.location.href = 'userP.php';</script>";
}
?>
