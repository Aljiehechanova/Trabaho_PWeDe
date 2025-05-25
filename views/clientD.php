<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

// Fetch client details
try {
    $stmt = $conn->prepare("SELECT fullname, img FROM users WHERE user_id = ?");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        die("Client not found.");
    }
} catch (PDOException $e) {
    die("Client fetch error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Client Dashboard</title>
  <link rel="stylesheet" href="../assets/css/dashboardstyle.css">
  <link rel="stylesheet" href="../assets/css/global.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../assets/js/chart.js"></script>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="clientD.php">
      <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
      <span class="fw-bold">TrabahoPWeDe</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <a href="#" class="d-flex align-items-center text-decoration-none me-3">
        <img src="<?= htmlspecialchars($client['img']) ?>" alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit: cover; margin-right: 10px;">
        <span class="fw-semibold text-dark"><?= htmlspecialchars($client['fullname']) ?></span>
      </a>
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          Settings
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">Edit Profile</a></li>
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
      <li><a href="clientL.php">View Job List</a></li>
      <li><a href="posting.php">Posting</a></li>
      <li class="active"><a href="clientD.php">Analytic Dashboard</a></li>
      <li><a href="clientM.php">Inbox</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Metrics Section -->
    <div class="section-box">
      <div class="row text-center">
        <div class="col-md-6">
          <div class="dashboard-card">
            <h4>Total Jobs Posted</h4>
            <p id="totalJobsPosted" class="fs-3 fw-bold text-primary">Loading...</p>
          </div>
        </div>
      </div>
    </div>

    <div class="section-box">
      <div class="row text-center">
        <div class="col-md-6">
          <h4 class="text-center mb-3">Hiring Pipeline Summary</h4>
          <table class="table table-bordered text-center">
            <thead class="table-light">
              <tr>
                <th>Applicants</th>
                <th>Interviews</th>
                <th>Hired</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td id="totalApplicants">Loading...</td>
                <td id="totalInterviews">Loading...</td>
                <td id="totalHired">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Pie Chart Section -->
    <div class="section-box">
      <h5 class="text-center">Applicants by Disability Type (Pie Chart)</h5>
      <div class="chart-container d-flex justify-content-center">
        <canvas id="applicantPieChart" height="300"></canvas>
      </div>
    </div>

    <!-- Bar Chart Section -->
    <div class="section-box">
      <h5 class="text-center">Applicants by Disability Type (Bar Chart)</h5>
      <div class="chart-container d-flex justify-content-center">
        <canvas id="applicantBarChart" height="300"></canvas>
      </div>
    </div>
  </div>
</div>