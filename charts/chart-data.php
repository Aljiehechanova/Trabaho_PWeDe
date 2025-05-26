<?php
require_once '../config/db_connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
    // === Donut Chart: Users per disability type with workshop participation ===
    $stmt = $conn->query("
        SELECT u.disability, COUNT(DISTINCT u.user_id) AS user_count
        FROM users u
        INNER JOIN activitylog a ON u.user_id = a.user_id
        WHERE u.disability IS NOT NULL AND u.disability != ''
        GROUP BY u.disability
    ");

    $disabilityLabels = [];
    $disabilityCounts = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $disabilityLabels[] = $row['disability'];
        $disabilityCounts[] = (int)$row['user_count'];
    }
    $clientDisabilityLabels = [];
    $clientDisabilityCounts = [];
    
    $clientStmt = $conn->query("
        SELECT disability, COUNT(*) AS count
        FROM users
        WHERE disability IS NOT NULL AND disability != ''
        GROUP BY disability
    ");
    
    while ($row = $clientStmt->fetch(PDO::FETCH_ASSOC)) {
        $clientDisabilityLabels[] = $row['disability'];
        $clientDisabilityCounts[] = (int)$row['count'];
    }
    // === Bar Chart: Workshop activity per month (based on entry_date) ===
    $monthlyLabels = [];
    $monthlyCounts = [];

    $monthMap = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
    ];

    $monthQuery = $conn->query("
        SELECT MONTH(entry_date) as month, COUNT(*) as total
        FROM activitylog
        WHERE YEAR(entry_date) = YEAR(CURRENT_DATE())
        GROUP BY MONTH(entry_date)
        ORDER BY MONTH(entry_date)
    ");

    $year = date('Y');

    $insertStmt = $conn->prepare("
        INSERT INTO monthly_activity_summary (year, month, month_name, total_entries)
        VALUES (:year, :month, :month_name, :total_entries)
        ON DUPLICATE KEY UPDATE total_entries = :total_entries
    ");

    while ($row = $monthQuery->fetch(PDO::FETCH_ASSOC)) {
        $monthNum = (int)$row['month'];
        $monthlyLabels[] = $monthMap[$monthNum];
        $monthlyCounts[] = (int)$row['total'];

        $insertStmt->execute([
            ':year' => $year,
            ':month' => $monthNum,
            ':month_name' => $monthMap[$monthNum],
            ':total_entries' => (int)$row['total']
        ]);
    }

    // === Hiring pipeline counts ===
    $available = $conn->query("SELECT COUNT(*) FROM activitylog WHERE available = 1")->fetchColumn();
    $offered   = $conn->query("SELECT COUNT(*) FROM activitylog WHERE offered = 1")->fetchColumn();
    $completed = $conn->query("SELECT COUNT(*) FROM activitylog WHERE complete = 1")->fetchColumn();

    // === Most common job post ===
    $commonJobStmt = $conn->query("
        SELECT jobpost_title, COUNT(*) AS total
        FROM jobpost
        GROUP BY jobpost_title
        ORDER BY total DESC
        LIMIT 1
    ");
    $mostCommonJobRow = $commonJobStmt->fetch(PDO::FETCH_ASSOC);
    $mostCommonJob = $mostCommonJobRow['jobpost_title'] ?? 'N/A';

    // === Total workshops ===
    $workshopTotal = $conn->query("SELECT COUNT(*) FROM workshop")->fetchColumn();

    // === Most Common Disability & Total Applicants (for clientD.php) ===
    $stmt = $conn->query("SELECT disability, COUNT(*) AS count FROM users WHERE disability IS NOT NULL AND disability != '' GROUP BY disability");
    $disabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $mostCommonDisability = 'N/A';
    $totalApplicants = 0;
    $maxCount = 0;

    foreach ($disabilities as $row) {
        $count = (int)$row['count'];
        $totalApplicants += $count;
        if ($count > $maxCount) {
            $maxCount = $count;
            $mostCommonDisability = $row['disability'];
        }
    }

    echo json_encode([
        'disabilityLabels' => $disabilityLabels,
        'disabilityCounts' => $disabilityCounts,
        'monthlyLabels' => $monthlyLabels,
        'monthlyCounts' => $monthlyCounts,
        'pipeline' => [
            'available' => $available,
            'completed' => $completed,
            'offered' => $offered
        ],
        'mostCommonJob' => $mostCommonJob,
        'totalWorkshops' => $workshopTotal,
        'most_common_disability' => $mostCommonDisability,
        'total_applicants' => $totalApplicants
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
