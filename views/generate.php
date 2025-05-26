<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle photo upload
    $photoPath = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['photo']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
            $photoPath = $targetPath;
        }
    }

    // Get form data
    $fullName = htmlspecialchars($_POST['fullName']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);
    $summary = htmlspecialchars($_POST['summary']);
    $skills = htmlspecialchars($_POST['skills']);
    
    // Get education data
    $education = [];
    if (isset($_POST['education'])) {
        for ($i = 0; $i < count($_POST['education']); $i++) {
            $education[] = [
                'degree' => htmlspecialchars($_POST['education'][$i]),
                'school' => htmlspecialchars($_POST['school'][$i]),
                'year' => htmlspecialchars($_POST['year'][$i]),
                'description' => htmlspecialchars($_POST['education_description'][$i] ?? '')
            ];
        }
    }
    
    // Get experience data
    $experience = [];
    if (isset($_POST['position'])) {
        for ($i = 0; $i < count($_POST['position']); $i++) {
            $startDate = new DateTime($_POST['start_date'][$i]);
            $endDate = !empty($_POST['end_date'][$i]) ? new DateTime($_POST['end_date'][$i]) : null;
            $isCurrentJob = isset($_POST['current_job'][$i]) && $_POST['current_job'][$i] == 'on';
            
            $experience[] = [
                'position' => htmlspecialchars($_POST['position'][$i]),
                'company' => htmlspecialchars($_POST['company'][$i]),
                'start_date' => $startDate->format('F Y'),
                'end_date' => $isCurrentJob ? 'Present' : ($endDate ? $endDate->format('F Y') : ''),
                'description' => htmlspecialchars($_POST['description'][$i])
            ];
        }
    }

    // Get projects data
    $projects = [];
    if (isset($_POST['project_name'])) {
        for ($i = 0; $i < count($_POST['project_name']); $i++) {
            $projects[] = [
                'name' => htmlspecialchars($_POST['project_name'][$i]),
                'url' => htmlspecialchars($_POST['project_url'][$i] ?? ''),
                'description' => htmlspecialchars($_POST['project_description'][$i])
            ];
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $fullName; ?> - Resume</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #3b82f6;
            --text-color: #1f2937;
            --light-bg: #f9fafb;
            --border-color: #e5e7eb;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            color: var(--text-color);
            background: var(--light-bg);
        }
        
        .resume-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .header {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .photo-container {
            width: 150px;
            height: 150px;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 2px solid var(--border-color);
            flex-shrink: 0;
        }
        
        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-container i {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #9ca3af;
            background: var(--light-bg);
        }
        
        .header-content {
            flex-grow: 1;
        }
        
        .header h1 {
            color: var(--text-color);
            margin-bottom: 0.5rem;
            font-size: 2.25rem;
            font-weight: 700;
        }
        
        .contact-info {
            color: #6b7280;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .contact-info p {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .summary {
            margin-bottom: 2rem;
            padding: 1rem;
            background: var(--light-bg);
            border-radius: 0.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .section {
            margin-bottom: 2rem;
        }
        
        .section h2 {
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .section h2 i {
            color: var(--primary-color);
        }
        
        .education-item, .experience-item, .project-item {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--light-bg);
            border-radius: 0.5rem;
            position: relative;
        }
        
        .education-item h3, .experience-item h3, .project-item h3 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .date-range {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .skills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .skill {
            background-color: var(--primary-color);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .project-url {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .project-url:hover {
            text-decoration: underline;
        }
        
        .no-print {
            text-align: center;
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        .no-print button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .no-print button:hover {
            background-color: var(--secondary-color);
        }
        
        @media print {
            body {
                padding: 0;
                background: white;
            }
            
            .resume-container {
                box-shadow: none;
                padding: 0;
            }
            
            .no-print {
                display: none;
            }

            .section {
                page-break-inside: avoid;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .photo-container {
                margin: 0 auto;
            }

            .contact-info {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="resume-container">
        <div class="header">
            <div class="photo-container">
                <?php if ($photoPath): ?>
                    <img src="<?php echo $photoPath; ?>" alt="Profile Photo">
                <?php else: ?>
                    <i class="fas fa-user-circle"></i>
                <?php endif; ?>
            </div>
            <div class="header-content">
                <h1><?php echo $fullName; ?></h1>
                <div class="contact-info">
                    <p><i class="fas fa-envelope"></i> <?php echo $email; ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo $phone; ?></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo $address; ?></p>
                </div>
            </div>
        </div>

        <?php if (!empty($summary)): ?>
        <div class="summary">
            <p><?php echo nl2br($summary); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($education)): ?>
        <div class="section">
            <h2><i class="fas fa-graduation-cap"></i> Education</h2>
            <?php foreach ($education as $edu): ?>
            <div class="education-item">
                <h3><?php echo $edu['degree']; ?></h3>
                <p><i class="fas fa-university"></i> <?php echo $edu['school']; ?></p>
                <p><i class="fas fa-calendar"></i> <?php echo $edu['year']; ?></p>
                <?php if (!empty($edu['description'])): ?>
                <p><?php echo nl2br($edu['description']); ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($experience)): ?>
        <div class="section">
            <h2><i class="fas fa-briefcase"></i> Work Experience</h2>
            <?php foreach ($experience as $exp): ?>
            <div class="experience-item">
                <h3><?php echo $exp['position']; ?></h3>
                <p><i class="fas fa-building"></i> <?php echo $exp['company']; ?></p>
                <p class="date-range">
                    <i class="fas fa-clock"></i> 
                    <?php echo $exp['start_date']; ?> - <?php echo $exp['end_date']; ?>
                </p>
                <p><?php echo nl2br($exp['description']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($projects)): ?>
        <div class="section">
            <h2><i class="fas fa-project-diagram"></i> Projects</h2>
            <?php foreach ($projects as $project): ?>
            <div class="project-item">
                <h3><?php echo $project['name']; ?></h3>
                <?php if (!empty($project['url'])): ?>
                <a href="<?php echo $project['url']; ?>" target="_blank" class="project-url">
                    <i class="fas fa-external-link-alt"></i> View Project
                </a>
                <?php endif; ?>
                <p><?php echo nl2br($project['description']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($skills)): ?>
        <div class="section">
            <h2><i class="fas fa-tools"></i> Skills</h2>
            <div class="skills-list">
                <?php
                $skillsArray = explode(',', $skills);
                foreach ($skillsArray as $skill):
                    $skill = trim($skill);
                    if (!empty($skill)):
                ?>
                <span class="skill">
                    <i class="fas fa-check"></i>
                    <?php echo $skill; ?>
                </span>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="no-print">
        <button onclick="window.print()">
            <i class="fas fa-print"></i> Print Resume
        </button>
        <button onclick="window.location.href='userPE.php'">
            <i class="fas fa-arrow-left"></i> Back to Builder
        </button>
    </div>
</body>
</html>
<?php
} else {
    header("Location: userPE.php");
    exit();
}
?> 