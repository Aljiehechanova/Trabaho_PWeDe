<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard</title>
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

<!-- Layout Wrapper -->
<div class="layout-container">
  <!-- Sidebar -->
  <div class="sidebar">
    <ul>
      <li><a href="userPE.php">Profile Enhancer</a></li>
      <li><a href="JM.php">Job Matching</a></li>
      <li class="active"><a href="userD.php">Analytic Dashboard</a></li>
      <li><a href="userM.php">Messages</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <!-- Main Content -->
<div class="main-content">

<!-- Metrics Section -->
<div class="section-box">
  <div class="row text-center">
    <div class="col-md-6">
      <div class="dashboard-card">
        <h4>Total Jobs Available/Hiring</h4>
        <p id="totalJobs" class="fs-3 fw-bold text-primary">Loading...</p>
      </div>
    </div>
</div>

<div class="section-box">
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
<!-- Pie Chart Section -->
<div class="section-box">
  <h5 class="text-center">Disability Types Distribution (Pie Chart)</h5>
  <div class="chart-container d-flex justify-content-center">
    <canvas id="disabilityPie" height="300"></canvas>
  </div>
</div>

<!-- Bar Chart Section -->
<div class="section-box">
  <h5 class="text-center">Disability Types (Bar Chart)</h5>
  <div class="chart-container d-flex justify-content-center">
    <canvas id="disabilityBar" height="300"></canvas>
  </div>
</div>

</div> <!-- End main-content -->


<!-- Chart Script -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    fetch("charts/chart-data.php")
      .then((res) => res.json())
      .then((data) => {
        document.getElementById("totalJobs").textContent = data.totalJobs;
        document.getElementById("mostCommonDisability").textContent = data.mostCommon;

        const pieCtx = document.getElementById("disabilityPie").getContext("2d");
        const pieLabels = Object.keys(data.disabilityCounts);
        const pieData = Object.values(data.disabilityCounts);

        new Chart(pieCtx, {
          type: "pie",
          data: {
            labels: pieLabels,
            datasets: [{
              data: pieData,
              backgroundColor: [
                "#4B61D1", "#F07C46", "#9D4EDD",
                "#00B8A9", "#F76C6C", "#6C5B7B"
              ],
              borderColor: "#fff",
              borderWidth: 2
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: "bottom" },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    const value = context.raw;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percent = ((value / total) * 100).toFixed(1);
                    return `${context.label}: ${value} (${percent}%)`;
                  }
                }
              }
            }
          }
        });

        const barCtx = document.getElementById("disabilityBar").getContext("2d");
        new Chart(barCtx, {
          type: "bar",
          data: {
            labels: pieLabels,
            datasets: [{
              label: "Jobs",
              data: pieData,
              backgroundColor: "#36A2EB"
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      })
      .catch((err) => {
        console.error("Chart data fetch error:", err);
      });
  });
</script>

</body>
</html>
