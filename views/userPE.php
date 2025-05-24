<?php
session_start();
require '../config/db_connection.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT fullname, img FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }
} catch (PDOException $e) {
    die("User fetch error: " . $e->getMessage());
}

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="UserD.php">
      <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
      <span class="fw-bold">TrabahoPWeDe</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <a href="userP.php" class="d-flex align-items-center text-decoration-none me-3">
        <img src="<?= htmlspecialchars($user['img']) ?>" alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit: cover; margin-right: 10px;">
        <span class="fw-semibold text-dark"><?= htmlspecialchars($user['fullname']) ?></span>
      </a>
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="settingsMenu" data-bs-toggle="dropdown" aria-expanded="false">
          Settings
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsMenu">
          <li><a class="dropdown-item" href="userP.php">Edit Profile</a></li>
          <li><a class="dropdown-item" href="#">Change Password</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="login.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>
    <div class="sidebar">
        <ul>
            <li class="active"><a href="userPE.php">Profile Enhancer</a></li>
            <li><a href="JM.php">Job Matching</a></li>
            <li><a href="userD.php">Analytic Dashboard</a></li>
            <li><a href="userM.php">Messages</a></li>
        </ul>
    </div>
    <div class="content-wrapper">
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
