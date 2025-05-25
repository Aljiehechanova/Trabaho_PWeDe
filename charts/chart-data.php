<?php
session_start();
require_once '../config/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$stmt = $conn->query("SELECT COUNT(*) AS total FROM jobpost");
$data['totalJobs'] = $stmt->fetch()['total'] ?? 0;

$data['disabilityCounts'] = [];
$stmt = $conn->query("SELECT disability_requirement, COUNT(*) AS count FROM jobpost GROUP BY disability_requirement");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data['disabilityCounts'][$row['disability_requirement']] = $row['count'];
}

if (!empty($data['disabilityCounts'])) {
    arsort($data['disabilityCounts']);
    reset($data['disabilityCounts']);
    $data['mostCommon'] = key($data['disabilityCounts']);
} else {
    $data['mostCommon'] = "N/A";
}

echo json_encode($data);
