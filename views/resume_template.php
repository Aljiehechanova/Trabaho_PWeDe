<?php if (!defined('IN_RESUME_GENERATOR')) define('IN_RESUME_GENERATOR', true); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($fullname) ?> - Resume</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/resume-style.css"> <!-- Adjust if needed -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            padding: 30px;
        }
        .resume-container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .left-bar {
            width: 30%;
            float: left;
            text-align: center;
            padding-right: 20px;
            border-right: 1px solid #dee2e6;
        }
        .right-bar {
            width: 70%;
            float: left;
            padding-left: 20px;
        }
        .profile-pic {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        h2, h3 {
            color: #0d6efd;
        }
        .section-title {
            margin-top: 30px;
            margin-bottom: 10px;
            border-bottom: 2px solid #0d6efd;
            display: inline-block;
            padding-bottom: 4px;
        }
        ul {
            padding-left: 20px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .back-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="resume-container">
        <div class="clearfix">
            <div class="left-bar">
                <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture" class="profile-pic">
                <h2><?= htmlspecialchars($fullname) ?></h2>
                <p><?= htmlspecialchars($email) ?></p>
            </div>

            <div class="right-bar">
                <h3 class="section-title">Professional Summary</h3>
                <p><?= nl2br(htmlspecialchars($summary)) ?></p>

                <h3 class="section-title">Education</h3>
                <ul>
                    <?php foreach ($education as $edu): ?>
                        <li>
                            <strong><?= htmlspecialchars($edu['school']) ?></strong> - <?= htmlspecialchars($edu['degree']) ?><br>
                            <small><?= htmlspecialchars($edu['year']) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <h3 class="section-title">Experience</h3>
                <ul>
                    <?php foreach ($experience as $exp): ?>
                        <li>
                            <strong><?= htmlspecialchars($exp['position']) ?></strong> at <?= htmlspecialchars($exp['company']) ?><br>
                            <small><?= htmlspecialchars($exp['duration']) ?></small>
                            <p><?= nl2br(htmlspecialchars($exp['description'])) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <h3 class="section-title">Projects</h3>
                <ul>
                    <?php foreach ($projects as $proj): ?>
                        <li>
                            <strong><?= htmlspecialchars($proj['title']) ?></strong><br>
                            <p><?= nl2br(htmlspecialchars($proj['description'])) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <h3 class="section-title">Skills</h3>
                <p><?= htmlspecialchars($skills) ?></p>

                <div class="back-button">
                    <a href="/Trabaho_PWeDe/Trabaho_PWeDe/views/userPE.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Builder
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
