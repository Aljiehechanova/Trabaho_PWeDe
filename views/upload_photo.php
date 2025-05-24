<?php
session_start();
include '../config/db_connection.php';

if (!isset($_POST['user_id'])) {
    die("No user ID provided.");
}

$user_id = $_POST['user_id'];
$uploadDir = '../assets/uploads/';
$imgPath = null;

// Handle webcam capture
if (!empty($_POST['webcam_image'])) {
    $data = $_POST['webcam_image'];

    // Extract base64 image data
    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);

    $filename = uniqid('webcam_') . '.png';
    $filePath = $uploadDir . $filename;

    file_put_contents($filePath, $data);
    $imgPath = '../assets/uploads/' . $filename;

// Handle file upload
} elseif (!empty($_FILES['profile_photo']['tmp_name'])) {
    $file = $_FILES['profile_photo'];
    $filename = uniqid('upload_') . '_' . basename($file['name']);
    $filePath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $imgPath = '../assets/uploads/' . $filename;
    } else {
        die("Failed to upload image.");
    }
}

if ($imgPath) {
    $stmt = $conn->prepare("UPDATE users SET img = ? WHERE user_id = ?");
    $stmt->execute([$imgPath, $user_id]);

    $_SESSION['success'] = "Profile picture updated!";
    header("Location: UserP.php");
    exit;
} else {
    die("No image data found.");
}
?>
