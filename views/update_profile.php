<?php
session_start();
include '../config/db_connection.php';
include '../controllers/UserController.php';

$user_id = $_POST['user_id'] ?? null;
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$description = $_POST['description'] ?? '';
$location = $_POST['location'] ?? '';
$disability = $_POST['disability'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $user_id) {
    die("Unauthorized profile update.");
}

// Handle optional image upload
$imgPath = null;
if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $imgName = basename($_FILES['img']['name']);
    $targetDir = "../uploads/profile/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    $targetPath = $targetDir . time() . "_" . $imgName;
    if (move_uploaded_file($_FILES['img']['tmp_name'], $targetPath)) {
        $imgPath = substr($targetPath, 3); // remove "../"
    }
}

$userController = new UserController($conn);
$updated = $userController->updateProfile($user_id, $fullname, $email, $description, $location, $disability, $contact_number, $imgPath);

if ($updated) {
    echo "<script>alert('Profile updated successfully!'); window.location.href = 'userP.php';</script>";
} else {
    echo "<script>alert('Failed to update profile.'); window.location.href = 'userP.php';</script>";
}


?>
