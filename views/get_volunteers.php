<?php
require_once '../config/db_connection.php';

if (!isset($_GET['workshop_id'])) {
    echo "Workshop ID missing.";
    exit;
}

$workshop_id = $_GET['workshop_id'];

try {
    $stmt = $conn->prepare("
        SELECT u.user_id, u.fullname, u.email, u.img
        FROM workshop_volunteers wv
        JOIN users u ON wv.user_id = u.user_id
        WHERE wv.workshop_id = ?
    ");
    $stmt->execute([$workshop_id]);
    $volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($volunteers)) {
        echo "<p>No volunteers found.</p>";
    } else {
        echo "<ul class='list-group'>";
        foreach ($volunteers as $v) {
            echo "<li class='list-group-item d-flex align-items-center justify-content-between'>";
            echo "<div class='d-flex align-items-center'>";
            echo "<img src='" . htmlspecialchars($v['img']) . "' alt='Profile' class='rounded-circle me-3' width='40' height='40' style='object-fit: cover;'>";
            echo "<div><strong>" . htmlspecialchars($v['fullname']) . "</strong><br><small>" . htmlspecialchars($v['email']) . "</small></div>";
            echo "</div>";
            echo "<button class='btn btn-sm btn-outline-info' onclick='viewVolunteerDetails(" . $v['user_id'] . ")'>View Details</button>";
            echo "</li>";
        }
        echo "</ul>";
    }
} catch (PDOException $e) {
    echo "Error fetching volunteers: " . $e->getMessage();
}
