<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized', 'labels' => [], 'counts' => []]);
    exit;
}

try {
    require_once '../config/db_connection.php';
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $response = [];

    // Total job posts
    $stmt = $conn->query("SELECT COUNT(*) FROM jobpost");
    $response['totalJobs'] = (int) $stmt->fetchColumn();

    // Disability type distribution
    $labels = [];
    $counts = [];

    $stmt = $conn->query("SELECT disability_requirement, COUNT(*) AS count FROM jobpost GROUP BY disability_requirement");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $labels[] = $row['disability_requirement'];
        $counts[] = (int)$row['count'];
    }

    $response['labels'] = $labels;
    $response['counts'] = $counts;

    // Most common disability
    if (!empty($counts)) {
        $maxCount = max($counts);
        $maxIndex = array_search($maxCount, $counts);
        $response['mostCommon'] = $labels[$maxIndex];
    } else {
        $response['mostCommon'] = "N/A";
    }

    // Pipeline summary
    $response['pipeline'] = [
        'available' => 0,
        'completed' => 0,
        'offered' => 0,
    ];

    $pipelineStmt = $conn->query("SELECT status, COUNT(*) as count FROM jobpost GROUP BY status");
    while ($row = $pipelineStmt->fetch(PDO::FETCH_ASSOC)) {
        switch (strtolower($row['status'])) {
            case 'available':
                $response['pipeline']['available'] = (int)$row['count'];
                break;
            case 'completed':
                $response['pipeline']['completed'] = (int)$row['count'];
                break;
            case 'offered':
                $response['pipeline']['offered'] = (int)$row['count'];
                break;
        }
    }

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
