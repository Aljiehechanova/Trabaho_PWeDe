<?php 
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
try {
    $stmt = $conn->prepare("SELECT fullname, img, resume, email FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }

    $loggedInEmail = $user['email'];

    // Fetch messages
    $stmt = $conn->prepare("
        SELECT m.sender_email, m.subject, m.message 
        FROM messages m
        WHERE m.receiver_email = ?
        ORDER BY m.messages_id DESC
    ");
    $stmt->execute([$loggedInEmail]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("User fetch error: " . $e->getMessage());
}

// Resume upload handling
$uploadStatus = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resume'])) {
    $uploadDir = '../uploads/resumes/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES['resume']['name']);
    $targetFile = $uploadDir . $fileName;

    $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    if (!in_array($_FILES['resume']['type'], $allowedTypes)) {
        $uploadStatus = "Invalid file type. Only PDF, DOC, and DOCX are allowed.";
    } else {
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $targetFile)) {
            $relativePath = 'uploads/resumes/' . $fileName;

            $update = $conn->prepare("UPDATE users SET resume = ? WHERE user_id = ?");
            $update->execute([$relativePath, $user_id]);

            $uploadStatus = "Resume uploaded successfully.";
            $user['resume'] = $relativePath;
        } else {
            $uploadStatus = "Failed to upload resume.";
        }
    }
}

// Workshops
try {
    $stmt = $conn->query("SELECT work_title, entry_date, end_date, location, hostname FROM workshop ORDER BY entry_date DESC");
    $workshops = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Notifications
$notif_stmt = $conn->prepare("SELECT message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$notif_stmt->execute([$user_id]);
$notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Enhancer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/defaultstyle.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/proen.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="UserD.php">
      <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
      <span class="fw-bold">Trabaho</span><span class="fw-bold" style="color: blue">PWeDe</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <!-- Notifications -->
      <div class="dropdown me-3">
        <button class="btn btn-light position-relative" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-bell fs-5"></i>
          <?php if (!empty($notifications)): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              <?= count($notifications) ?>
            </span>
          <?php endif; ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown" style="width: 300px;">
          <?php foreach ($notifications as $notif): ?>
            <li class="px-3 py-2 border-bottom">
              <small class="text-muted"><?= htmlspecialchars($notif['created_at']) ?></small><br>
              <?= htmlspecialchars($notif['message']) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Profile & Settings -->
      <a href="userP.php" class="d-flex align-items-center text-decoration-none me-3">
        <img src="<?= htmlspecialchars($user['img']) ?>" alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit: cover; margin-right: 10px;">
        <span class="fw-semibold text-dark"><?= htmlspecialchars($user['fullname']) ?></span>
      </a>
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="settingsMenu" data-bs-toggle="dropdown">
          Settings
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="userP.php">Edit Profile</a></li>
          <li><a class="dropdown-item" href="#">Change Password</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="login.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<div class="layout-container">
  <div class="sidebar">
    <ul>
        <li class="active"><a href="userPE.php">Profile Enhancer</a></li>
        <li><a href="JM.php">Job Matching</a></li>
        <li><a href="userD.php">Analytic Dashboard</a></li>
        <li><a href="userM.php">Inbox</a></li>
    </ul>
  </div>

  <div class="main-content">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="userPE.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Builder
        </a>
    </div>

    <!-- Messages -->
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Actions -->
    <div class="action-buttons mb-3">
        <button onclick="location.href='RG.php'" class="btn btn-primary">Resume Builder</button>
        <button onclick="openModal()" class="btn btn-secondary">Upload Resume</button>
    </div>

    <?php if (!empty($uploadStatus)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($uploadStatus) ?></div>
    <?php endif; ?>

    <!-- Resume -->
    <?php if (!empty($user['resume'])): ?>
        <div class="mb-4">
            <h5>Current Resume:</h5>
            <a href="../<?= htmlspecialchars($user['resume']) ?>" target="_blank" class="btn btn-sm btn-primary">View Resume</a>
            <a href="../<?= htmlspecialchars($user['resume']) ?>" download class="btn btn-sm btn-success">Download</a>
            <button class="btn btn-sm btn-secondary" onclick="printResume()">Print</button>
        </div>
    <?php endif; ?>

    <!-- Workshops -->
    <div class="workshop-list">
        <h2>List of Workshops</h2>
        <?php if (!empty($workshops)): ?>
            <?php foreach ($workshops as $workshop): ?>
                <div class="workshop-item mb-3 p-3 border rounded bg-light">
                    <h4><?= htmlspecialchars($workshop['work_title']) ?></h4>
                    <small><?= date('F j, Y', strtotime($workshop['entry_date'])) ?> to <?= date('F j, Y', strtotime($workshop['end_date'])) ?> — <?= htmlspecialchars($workshop['location']) ?></small>
                    <div><strong>Host:</strong> <?= htmlspecialchars($workshop['hostname']) ?></div>
                    <form method="POST" action="volunteer_handler.php" class="mt-2" onsubmit="return confirmVolunteer()">
                        <input type="hidden" name="work_title" value="<?= htmlspecialchars($workshop['work_title']) ?>">
                        <input type="hidden" name="entry_date" value="<?= htmlspecialchars($workshop['entry_date']) ?>">
                        <input type="hidden" name="host" value="<?= htmlspecialchars($workshop['hostname']) ?>">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Volunteer</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No workshops found.</p>
        <?php endif; ?>
    </div>
  </div>
</div>

<!-- Resume Upload Modal -->
<div id="resumeModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h3>Upload Your Resume</h3>
    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="resume" accept=".pdf,.doc,.docx" required>
      <button type="submit" class="btn btn-primary mt-2">Upload</button>
    </form>
  </div>
</div>

<!-- JS -->
<script>
function openModal() {
    document.getElementById("resumeModal").style.display = "flex";
}
function closeModal() {
    document.getElementById("resumeModal").style.display = "none";
}
function printResume() {
    const win = window.open("../<?= htmlspecialchars($user['resume']) ?>", '_blank');
    win.focus();
    win.onload = () => win.print();
}
function confirmVolunteer() {
    return confirm("Are you sure you want to volunteer?");
}
</script>
</body>
</html>
