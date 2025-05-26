<?php
require_once '../config/db_connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
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

    $available = $conn->query("SELECT COUNT(*) FROM activitylog WHERE available = 1")->fetchColumn();
    $offered   = $conn->query("SELECT COUNT(*) FROM activitylog WHERE offered = 1")->fetchColumn();
    $completed = $conn->query("SELECT COUNT(*) FROM activitylog WHERE complete = 1")->fetchColumn();

    $commonJobStmt = $conn->query("
        SELECT jobpost_title, COUNT(*) AS total
        FROM jobpost
        GROUP BY jobpost_title
        ORDER BY total DESC
        LIMIT 1
    ");
    $mostCommonJobRow = $commonJobStmt->fetch(PDO::FETCH_ASSOC);
    $mostCommonJob = $mostCommonJobRow['jobpost_title'] ?? 'N/A';

    $workshopTotal = $conn->query("SELECT COUNT(*) FROM workshop")->fetchColumn();

    // Calculate most common disability and total applicants
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

    // Volunteer data
    $volunteerLabels = [];
    $volunteerCounts = [];
    $volunteerQuery = $conn->query("
        SELECT MONTH(volunteered_at) AS month, COUNT(*) AS total
        FROM workshop_volunteers
        WHERE YEAR(volunteered_at) = YEAR(CURRENT_DATE())
        GROUP BY MONTH(volunteered_at)
        ORDER BY MONTH(volunteered_at)
    ");
    $monthMap = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
    ];
    while ($row = $volunteerQuery->fetch(PDO::FETCH_ASSOC)) {
        $monthNum = (int)$row['month'];
        $volunteerLabels[] = $monthMap[$monthNum];
        $volunteerCounts[] = (int)$row['total'];
    }

    // Top Skills Analysis
    $skillsData = [];
    $skillsStmt = $conn->query("SELECT skills_requirement FROM jobpost WHERE skills_requirement IS NOT NULL AND skills_requirement != ''");
    while ($row = $skillsStmt->fetch(PDO::FETCH_ASSOC)) {
        $skills = explode(',', strtolower($row['skills_requirement']));
        foreach ($skills as $skill) {
            $skill = trim($skill);
            if ($skill) {
                $skillsData[$skill] = ($skillsData[$skill] ?? 0) + 1;
            }
        }
    }
    arsort($skillsData);
    $topSkills = array_slice($skillsData, 0, 10, true);
    $topSkillLabels = array_keys($topSkills);
    $topSkillCounts = array_values($topSkills);

    // Top Companies Hiring PWDs
    $companyStmt = $conn->query("
    SELECT 
        u.company, 
        COUNT(*) AS matching_disability_count
    FROM jobpost j
    INNER JOIN users u ON j.user_id = u.user_id -- company posting the job
    INNER JOIN users seeker ON seeker.disability = j.disability_requirement AND seeker.user_type = 'job_seeker'
    WHERE u.company IS NOT NULL AND u.company != ''
    GROUP BY u.company
    ORDER BY matching_disability_count DESC
    LIMIT 10
    ");

    $topCompanyNames = [];
    $topCompanyCounts = [];

    while ($row = $companyStmt->fetch(PDO::FETCH_ASSOC)) {
        $topCompanyNames[] = $row['company'];
        $topCompanyCounts[] = (int)$row['matching_disability_count'];
    }



    // Output JSON
    echo json_encode([
        'volunteerLabels' => $volunteerLabels,
        'volunteerCounts' => $volunteerCounts,
        'pipeline' => [
            'available' => $available,
            'completed' => $completed,
            'offered' => $offered
        ],
        'mostCommonJob' => $mostCommonJob,
        'totalWorkshops' => $workshopTotal,
        'mostCommonDisability' => $mostCommonDisability,
        'totalApplicants' => $totalApplicants,
        'topSkillLabels' => $topSkillLabels,
        'topSkillCounts' => $topSkillCounts,
        'topCompanyNames' => $topCompanyNames,
        'topCompanyCounts' => $topCompanyCounts,
        'clientDisabilityLabels' => $clientDisabilityLabels,
        'clientDisabilityCounts' => $clientDisabilityCounts
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
