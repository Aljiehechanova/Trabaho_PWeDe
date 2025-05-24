<?php
session_start();
include '../config/db_connection.php';
include '../models/UserModel.php';
include '../controllers/UserController.php';

$user_id = $_POST['user_id'] ?? null;
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$description = $_POST['description'] ?? '';
$location = $_POST['location'] ?? '';

$userModel = new UserModel($conn);
$userController = new UserController($userModel);

if ($userController->updateProfile($user_id, $fullname, $email, $description, $location)) {
    echo "<script>alert('Profile updated successfully!'); window.location.href = 'userP.php';</script>";
} else {
    echo "<script>alert('Failed to update profile.'); window.location.href = 'userP.php';</script>";
}
?>
