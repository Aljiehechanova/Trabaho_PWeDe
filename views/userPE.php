<?php
require '../config/db_connection.php';

try {
    $stmt = $conn->query("SELECT work_title, entry_date, end_date, location, hostname FROM workshop ORDER BY entry_date DESC");
    $workshops = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Enhancer</title>
    <link rel="stylesheet" href="../assets/css/defaultstyle.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/proen.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Trabaho PWeDe">
        </div>
        <ul>
            <li class="active"><a href="userPE.php">Profile Enhancer</a></li>
            <li><a href="JM.php">Job Matching</a></li>
            <li><a href="userD.php">Analytic Dashboard</a></li>
            <li><a href="userM.php">Messages</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="action-buttons">
            <button onclick="location.href='RG.php'">Resume Builder</button>
            <button onclick="openModal()">Upload Resume</button>
        </div>

        <div class="workshop-list">
            <h2>List of Workshops</h2>
            <?php if (!empty($workshops)): ?>
                <?php foreach ($workshops as $workshop): ?>
                    <div class="workshop-item">
                        <h4><?= htmlspecialchars($workshop['title']) ?></h4>
                        <small>
                            <?= date('F j, Y', strtotime($workshop['entry_date'])) ?> 
                            to 
                            <?= date('F j, Y', strtotime($workshop['end_date'])) ?> 
                            — <?= htmlspecialchars($workshop['location']) ?>
                        </small>
                        <div><strong>Host:</strong> <?= htmlspecialchars($workshop['hostname']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No workshops found.</p>
            <?php endif; ?>
        </div>
    </div>
    <div id="resumeModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3>Upload Your Resume</h3>
        <form id="resumeForm" method="POST" enctype="multipart/form-data">
            <input type="file" name="resume" accept=".pdf,.doc,.docx" required>
            <button type="submit">Upload</button>
        </form>
        <div id="uploadStatus"></div>
    </div>
</div>
<script>
function openModal() {
    document.getElementById("resumeModal").style.display = "flex";
}
function closeModal() {
    document.getElementById("resumeModal").style.display = "none";
}
</script>
</body>
</html>
