<?php
require_once '../config/db_connection.php';
header('Content-Type: application/json');

$disability = $_GET['disability'] ?? '';

if (!$disability) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT fullname FROM users WHERE disability = ?");
    $stmt->execute([$disability]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
    