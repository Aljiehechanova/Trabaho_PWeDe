<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch user details, including email for message retrieval
    $stmt = $conn->prepare("SELECT fullname, img, email FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }

    $user_email = $user['email']; // ✅ Now defined

    // Fetch messages addressed to the user
    $stmt = $conn->prepare("
        SELECT m.sender_email, m.subject, m.message 
        FROM messages m
        WHERE m.receiver_email = ?
        ORDER BY m.messages_id DESC
    ");
    $stmt->execute([$user_email]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch notifications
    $notif_stmt = $conn->prepare("SELECT message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $notif_stmt->execute([$user_id]);
    $notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard</title>
  
  <link rel="stylesheet" href="../assets/css/global.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../assets/js/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="UserD.php">
      <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
      <span class="fw-bold">Trabaho</span><span class="fw-bold" style="color: blue">PWeDe</span>
    </a>

    <div class="ms-auto d-flex align-items-center">

      <!-- Notification bell -->
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
          <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notif): ?>
              <li class="px-3 py-2 border-bottom">
                <small class="text-muted"><?= htmlspecialchars($notif['created_at']) ?></small><br>
                <?= htmlspecialchars($notif['message']) ?>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="dropdown-item text-muted">No new notifications</li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Profile and settings -->
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

<!-- Layout Wrapper -->
<div class="layout-container">
  <!-- Sidebar -->
  <div class="sidebar">
    <ul>
      <li><a href="userPE.php">Profile Enhancer</a></li>
      <li><a href="JM.php">Job Matching</a></li>
      <li class="active"><a href="userD.php">Analytic Dashboard</a></li>
      <li><a href="userM.php">Inbox</a></li>
    </ul>
  </div>

  <!-- Main Content -->
<div class="main-content">

<div class="section-box">
  <div class="row text-center">
    <div class="col-md-6 mb-3">
      <div class="dashboard-card">
        <h4>Most Common Job Offered</h4>
        <p id="mostCommonJob" class="fs-5 fw-bold text-primary">Loading...</p>
      </div>
    </div>

    <div class="col-md-6 mb-3">
      <div class="dashboard-card">
        <h4>Total Workshops Available</h4>
        <p id="totalWorkshops" class="fs-5 fw-bold text-success">Loading...</p>
      </div>
    </div>
  </div>
</div>
<div class="section-box center">
  <div class="row text-center">
    <div class="col-md-6">
      <h4 class="text-center mb-3">Pipeline Hiring Summary</h4>
      <table class="table table-bordered text-center">
        <thead class="table-light">
          <tr>
            <th>Available</th>
            <th>Completed</th>
            <th>Total Offered</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td id="jobsAvailable">Loading...</td>
            <td id="jobsCompleted">Loading...</td>
            <td id="jobsOffered">Loading...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="section-box mt-4">
  <h4 class="text-center mb-3">Top Skills Required by Companies</h4>
  <canvas id="skillsBarChart" width="400" height="200"></canvas>
</div>


<div class="section-box mt-4">
  <h4 class="text-center mb-3">Most Companies Hiring PWDs</h4>
  <ul id="companyList" class="list-group list-group-flush px-3">
    <li>Loading...</li>
  </ul>
</div>

</div>
</div>

</body>
</html>
