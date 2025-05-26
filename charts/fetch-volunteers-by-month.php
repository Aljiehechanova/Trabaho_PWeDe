<?php
require_once '../config/db_connection.php';
header('Content-Type: application/json');

$monthRaw = $_GET['month'] ?? '';
$month = explode(':', $monthRaw)[0]; // Clean label

if (!$month) {
    echo json_encode(['error' => 'Month is required']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT u.fullname
        FROM workshop_volunteers wv
        JOIN users u ON wv.user_id = u.user_id
        WHERE DATE_FORMAT(wv.volunteered_at, '%b') = ?
    ");
    $stmt->execute([$month]);
    $volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($volunteers);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
