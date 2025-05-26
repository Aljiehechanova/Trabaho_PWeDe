<?php
// Include Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

// Import the necessary classes
use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullName = htmlspecialchars($_POST['fullName']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);
    $summary = htmlspecialchars($_POST['summary']);
    $skills = htmlspecialchars($_POST['skills']);

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
            // Convert image to base64 for embedding in PDF
            $imageData = base64_encode(file_get_contents($targetPath));
            $photoPath = 'data:image/' . pathinfo($targetPath, PATHINFO_EXTENSION) . ';base64,' . $imageData;
        }
    }

    // Create HTML content
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            @page {
                margin: 1cm;
            }
            body {
                font-family: Helvetica, Arial, sans-serif;
                line-height: 1.4;
                color: #333;
                margin: 0;
                padding: 0;
                font-size: 11pt;
            }
            .resume {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .header {
                width: 100%;
                margin-bottom: 20px;
                border-bottom: 2px solid #3498db;
                padding-bottom: 15px;
            }
            .header-content {
                width: 100%;
            }
            .profile {
                float: left;
                width: 70%;
            }
            .photo {
                float: right;
                width: 25%;
                text-align: right;
            }
            .photo img {
                width: 100px;
                height: 100px;
                border: 3px solid #3498db;
            }
            h1 {
                font-size: 24pt;
                color: #2c3e50;
                margin: 0 0 5px 0;
                font-weight: bold;
            }
            .contact-info {
                color: #7f8c8d;
                margin-bottom: 10px;
                font-size: 10pt;
            }
            .section {
                clear: both;
                margin-bottom: 20px;
            }
            .section-title {
                font-size: 14pt;
                color: #2c3e50;
                border-bottom: 1px solid #3498db;
                padding-bottom: 3px;
                margin-bottom: 10px;
                font-weight: bold;
            }
            .experience-item, .education-item, .project-item {
                margin-bottom: 15px;
            }
            .item-header {
                width: 100%;
                margin-bottom: 3px;
            }
            .item-title {
                float: left;
                font-weight: bold;
                color: #2c3e50;
                font-size: 11pt;
            }
            .item-date {
                float: right;
                color: #7f8c8d;
                font-size: 10pt;
            }
            .item-subtitle {
                clear: both;
                color: #34495e;
                font-style: italic;
                margin-bottom: 3px;
                font-size: 10pt;
            }
            .item-description {
                clear: both;
                font-size: 10pt;
                color: #555;
                margin-top: 5px;
            }
            .skills-list {
                width: 100%;
            }
            .skill-tag {
                display: inline-block;
                background: #3498db;
                color: white;
                padding: 3px 8px;
                margin: 2px;
                font-size: 9pt;
            }
            .summary {
                background: #f8f9fa;
                padding: 12px;
                margin-bottom: 20px;
                font-size: 10pt;
                border-left: 3px solid #3498db;
            }
            .clear {
                clear: both;
            }
        </style>
    </head>
    <body>
        <div class="resume">
            <div class="header">
                <div class="header-content">
                    <div class="profile">
                        <h1>' . $fullName . '</h1>
                        <div class="contact-info">
                            ' . $email . ' | ' . $phone . '<br>
                            ' . $address . '
                        </div>
                    </div>';
    
    if (!empty($photoPath)) {
        $html .= '
                    <div class="photo">
                        <img src="' . $photoPath . '" alt="Profile Photo">
                    </div>';
    }
    
    $html .= '
                    <div class="clear"></div>
                </div>
            </div>';

    // Summary
    if (!empty($summary)) {
        $html .= '
            <div class="section">
                <div class="summary">
                    ' . $summary . '
                </div>
            </div>';
    }

    // Experience
    if (isset($_POST['position'])) {
        $html .= '
            <div class="section">
                <h2 class="section-title">Work Experience</h2>';
        
        for ($i = 0; $i < count($_POST['position']); $i++) {
            $position = htmlspecialchars($_POST['position'][$i]);
            $company = htmlspecialchars($_POST['company'][$i]);
            $startDate = new DateTime($_POST['start_date'][$i]);
            $endDate = !empty($_POST['end_date'][$i]) ? new DateTime($_POST['end_date'][$i]) : null;
            $isCurrentJob = isset($_POST['current_job'][$i]) && $_POST['current_job'][$i] == 'on';
            $description = htmlspecialchars($_POST['description'][$i]);

            $html .= '
                <div class="experience-item">
                    <div class="item-header">
                        <div class="item-title">' . $position . '</div>
                        <div class="item-date">' . $startDate->format('F Y') . ' - ' . 
                        ($isCurrentJob ? 'Present' : ($endDate ? $endDate->format('F Y') : '')) . '</div>
                        <div class="clear"></div>
                    </div>
                    <div class="item-subtitle">' . $company . '</div>
                    <div class="item-description">' . $description . '</div>
                </div>';
        }
        
        $html .= '
            </div>';
    }

    // Education
    if (isset($_POST['education'])) {
        $html .= '
            <div class="section">
                <h2 class="section-title">Education</h2>';
        
        for ($i = 0; $i < count($_POST['education']); $i++) {
            $degree = htmlspecialchars($_POST['education'][$i]);
            $school = htmlspecialchars($_POST['school'][$i]);
            $year = htmlspecialchars($_POST['year'][$i]);
            $description = htmlspecialchars($_POST['education_description'][$i] ?? '');

            $html .= '
                <div class="education-item">
                    <div class="item-header">
                        <div class="item-title">' . $degree . '</div>
                        <div class="item-date">' . $year . '</div>
                        <div class="clear"></div>
                    </div>
                    <div class="item-subtitle">' . $school . '</div>';
            
            if (!empty($description)) {
                $html .= '
                    <div class="item-description">' . $description . '</div>';
            }
            
            $html .= '
                </div>';
        }
        
        $html .= '
            </div>';
    }

    // Projects
    if (isset($_POST['project_name'])) {
        $html .= '
            <div class="section">
                <h2 class="section-title">Projects</h2>';
        
        for ($i = 0; $i < count($_POST['project_name']); $i++) {
            $name = htmlspecialchars($_POST['project_name'][$i]);
            $url = htmlspecialchars($_POST['project_url'][$i] ?? '');
            $description = htmlspecialchars($_POST['project_description'][$i]);

            $html .= '
                <div class="project-item">
                    <div class="item-title">' . $name . '</div>';
            
            if (!empty($url)) {
                $html .= '
                    <div class="item-subtitle"><a href="' . $url . '">' . $url . '</a></div>';
            }
            
            $html .= '
                    <div class="item-description">' . $description . '</div>
                </div>';
        }
        
        $html .= '
            </div>';
    }

    // Skills
    if (!empty($skills)) {
        $skillsArray = explode(',', $skills);
        $html .= '
            <div class="section">
                <h2 class="section-title">Skills</h2>
                <div class="skills-list">';
        
        foreach ($skillsArray as $skill) {
            $skill = trim($skill);
            if (!empty($skill)) {
                $html .= '
                    <span class="skill-tag">' . $skill . '</span>';
            }
        }
        
        $html .= '
                </div>
            </div>';
    }

    $html .= '
        </div>
    </body>
    </html>';

    // Initialize Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');
    $options->set('defaultMediaType', 'print');
    $options->set('isFontSubsettingEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output PDF
    $dompdf->stream('resume.pdf', array('Attachment' => true));
} else {
    header("Location: userPE    .php");
    exit();
}
?> 