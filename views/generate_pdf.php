<?php
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize basic fields
    function safe($data) {
        return htmlspecialchars(trim($data));
    }

    $fullName = safe($_POST['fullName'] ?? '');
    $email = safe($_POST['email'] ?? '');
    $phone = safe($_POST['phone'] ?? '');
    $address = safe($_POST['address'] ?? '');
    $summary = safe($_POST['summary'] ?? '');
    $skills = safe($_POST['skills'] ?? '');

    // Handle photo upload and convert to base64
    $photoPath = '';
    if (!empty($_FILES['photo']['tmp_name']) && $_FILES['photo']['error'] == 0) {
        $fileTmp = $_FILES['photo']['tmp_name'];
        $fileType = mime_content_type($fileTmp);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (in_array($fileType, $allowedTypes)) {
            $imageData = base64_encode(file_get_contents($fileTmp));
            $ext = explode('/', $fileType)[1];
            $photoPath = "data:$fileType;base64,$imageData";
        }
    }

    // Start building HTML
    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 1cm; }
        body { font-family: Helvetica, Arial, sans-serif; line-height: 1.4; color: #333; font-size: 11pt; }
        .resume { width: 100%; }
        .header { border-bottom: 2px solid #3498db; padding-bottom: 15px; margin-bottom: 20px; }
        .profile { float: left; width: 70%; }
        .photo { float: right; width: 25%; text-align: right; }
        .photo img { width: 100px; height: 100px; border: 3px solid #3498db; }
        h1 { font-size: 24pt; color: #2c3e50; margin: 0 0 5px 0; }
        .contact-info { color: #7f8c8d; font-size: 10pt; }
        .section { clear: both; margin-bottom: 20px; }
        .section-title { font-size: 14pt; color: #2c3e50; border-bottom: 1px solid #3498db; margin-bottom: 10px; }
        .item-title { float: left; font-weight: bold; }
        .item-date { float: right; color: #7f8c8d; }
        .item-subtitle { clear: both; font-style: italic; margin-bottom: 5px; }
        .item-description { clear: both; color: #555; margin-top: 5px; }
        .skill-tag { display: inline-block; background: #3498db; color: white; padding: 3px 8px; margin: 2px; font-size: 9pt; }
        .summary { background: #f8f9fa; padding: 12px; border-left: 3px solid #3498db; }
        .clear { clear: both; }
    </style>
</head>
<body>
<div class="resume">
    <div class="header">
        <div class="profile">
            <h1>{$fullName}</h1>
            <div class="contact-info">{$email} | {$phone}<br>{$address}</div>
        </div>
HTML;

    if (!empty($photoPath)) {
        $html .= '<div class="photo"><img src="' . $photoPath . '" alt="Profile Photo"></div>';
    }

    $html .= '<div class="clear"></div></div>';

    // Summary
    if (!empty($summary)) {
        $html .= '<div class="section"><div class="summary">' . $summary . '</div></div>';
    }

    // Work Experience
    if (!empty($_POST['position'])) {
        $html .= '<div class="section"><h2 class="section-title">Work Experience</h2>';
        foreach ($_POST['position'] as $i => $pos) {
            $position = safe($pos);
            $company = safe($_POST['company'][$i]);
            $start = date_create($_POST['start_date'][$i]);
            $end = !empty($_POST['end_date'][$i]) ? date_create($_POST['end_date'][$i]) : null;
            $current = isset($_POST['current_job'][$i]);
            $description = safe($_POST['description'][$i]);

            $dateRange = $start ? $start->format("F Y") : '';
            $dateRange .= ' - ' . ($current ? 'Present' : ($end ? $end->format("F Y") : ''));

            $html .= <<<EXP
<div class="experience-item">
    <div class="item-title">{$position}</div>
    <div class="item-date">{$dateRange}</div>
    <div class="clear"></div>
    <div class="item-subtitle">{$company}</div>
    <div class="item-description">{$description}</div>
</div>
EXP;
        }
        $html .= '</div>';
    }

    // Education
    if (!empty($_POST['education'])) {
        $html .= '<div class="section"><h2 class="section-title">Education</h2>';
        foreach ($_POST['education'] as $i => $degree) {
            $degree = safe($degree);
            $school = safe($_POST['school'][$i]);
            $year = safe($_POST['year'][$i]);
            $desc = safe($_POST['education_description'][$i] ?? '');

            $html .= <<<EDU
<div class="education-item">
    <div class="item-title">{$degree}</div>
    <div class="item-date">{$year}</div>
    <div class="clear"></div>
    <div class="item-subtitle">{$school}</div>
    <div class="item-description">{$desc}</div>
</div>
EDU;
        }
        $html .= '</div>';
    }

    // Projects
    if (!empty($_POST['project_name'])) {
        $html .= '<div class="section"><h2 class="section-title">Projects</h2>';
        foreach ($_POST['project_name'] as $i => $name) {
            $name = safe($name);
            $url = safe($_POST['project_url'][$i] ?? '');
            $desc = safe($_POST['project_description'][$i]);

            $html .= <<<PROJ
<div class="project-item">
    <div class="item-title">{$name}</div>
PROJ;
            if (!empty($url)) {
                $html .= "<div class='item-subtitle'><a href='{$url}'>{$url}</a></div>";
            }
            $html .= "<div class='item-description'>{$desc}</div></div>";
        }
        $html .= '</div>';
    }

    // Skills
    if (!empty($skills)) {
        $html .= '<div class="section"><h2 class="section-title">Skills</h2>';
        foreach (explode(',', $skills) as $skill) {
            $skill = safe($skill);
            if (!empty($skill)) {
                $html .= "<span class='skill-tag'>{$skill}</span>";
            }
        }
        $html .= '</div>';
    }

    $html .= '</div></body></html>';

    // Generate PDF
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Stream to browser
    $dompdf->stream("resume.pdf", ["Attachment" => true]);
    exit;
} else {
    header("Location: userPE.php");
    exit;
}
?>
