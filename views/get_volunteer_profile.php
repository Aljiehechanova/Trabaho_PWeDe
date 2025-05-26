<?php
require_once '../config/db_connection.php';

if (!isset($_GET['user_id'])) {
    echo "User ID missing.";
    exit;
}

$user_id = $_GET['user_id'];

try {
    $stmt = $conn->prepare("SELECT fullname, email, img, description, contact_number FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<p>Volunteer not found.</p>";
    } else {
        echo "<div class='text-center'>";
        echo "<img src='" . htmlspecialchars($user['img']) . "' alt='Profile Picture' class='rounded-circle mb-3' width='100' height='100' style='object-fit: cover;'>";
        echo "<h4>" . htmlspecialchars($user['fullname']) . "</h4>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
        echo "<p><strong>Contact:</strong> " . htmlspecialchars($user['contact_number']) . "</p>";
        echo "<p><strong>Description:</strong><br>" . nl2br(htmlspecialchars($user['description'])) . "</p>";
        echo "</div>";
    }
} catch (PDOException $e) {
    echo "Error loading profile: " . $e->getMessage();
}
