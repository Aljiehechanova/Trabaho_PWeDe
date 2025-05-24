<?php
session_start();
include '../config/db_connection.php';
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Client Profile - Trabaho PWeDe</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Client Profile</h2>
        <p><strong>Full Name:</strong> <?= htmlspecialchars($client['fullname']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($client['email']) ?></p>
        <p><strong>Company:</strong> <?= htmlspecialchars($client['company_name'] ?? 'N/A') ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($client['address'] ?? 'N/A') ?></p>

        <a href="edit_client_profile.php" class="btn btn-warning">Edit Profile</a>
    </div>
</body>
</html>