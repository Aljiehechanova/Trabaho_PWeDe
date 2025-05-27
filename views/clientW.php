<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

// Fetch workshops for the client
try {
    $stmt = $conn->prepare("SELECT * FROM workshop WHERE user_id = ?");
    $stmt->execute([$client_id]);
    $workshops = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Fetch client info
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workshop Volunteers</title>
    <link rel="stylesheet" href="../assets/css/dashboardstyle.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/clientlist.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="clientD.php">
      <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
      <span class="fw-bold">TrabahoPWeDe</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <a href="clientP.php" class="d-flex align-items-center text-decoration-none me-3">
        <img src="<?= htmlspecialchars($client['img']) ?>" alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit: cover; margin-right: 10px;">
        <span class="fw-semibold text-dark"><?= htmlspecialchars($client['fullname']) ?></span>
      </a>
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
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

<div class="layout-container">
  <div class="sidebar">
    <ul>
      <li><a href="clientL.php">View Job List</a></li>
      <li class="active"><a href="clientW.php">View Workshop Volunteer</a></li>
      <li><a href="listofapplicant.php">View List of Applicants</a></li>
      <li><a href="posting.php">Posting</a></li>
      <li><a href="clientD.php">Analytic Dashboard</a></li>
      <li><a href="clientM.php">Inbox</a></li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Posted Workshops</h1>

    <?php if (!empty($workshops)): ?>
      <?php foreach ($workshops as $workshop): ?>
        <div class="job-card">
          <h3><?= htmlspecialchars($workshop['work_title']) ?></h3>
          <p><strong>Location:</strong> <?= htmlspecialchars($workshop['location']) ?></p>
          <p><strong>Date:</strong> <?= htmlspecialchars($workshop['entry_date']) ?> to <?= htmlspecialchars($workshop['end_date']) ?></p>
          <p><strong>Host:</strong> <?= htmlspecialchars($workshop['hostname']) ?></p>
          <button class="btn btn-sm btn-outline-primary" onclick="loadVolunteers(<?= $workshop['workshop_id'] ?>)">View Volunteer</button>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No workshops found.</p>
    <?php endif; ?>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="volunteerModal" tabindex="-1" aria-labelledby="volunteerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Workshop Volunteers</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="volunteerModalBody">
        Loading...
      </div>
    </div>
  </div>
</div>
<!-- Volunteer Details Modal -->
<div class="modal fade" id="volunteerDetailsModal" tabindex="-1" aria-labelledby="volunteerDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Volunteer Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="volunteerDetailsBody">
        Loading...
      </div>
    </div>
  </div>
</div>


<script>
function loadVolunteers(workshopId) {
  fetch(`get_volunteers.php?workshop_id=${workshopId}`)
    .then(response => response.text())
    .then(html => {
      document.getElementById('volunteerModalBody').innerHTML = html;
      const modal = new bootstrap.Modal(document.getElementById('volunteerModal'));
      modal.show();
    })
    .catch(() => {
      document.getElementById('volunteerModalBody').innerHTML = 'Error loading data.';
    });
}
</script>
<script>
function viewVolunteerDetails(userId) {
  fetch(`get_volunteer_profile.php?user_id=${userId}`)
    .then(response => response.text())
    .then(html => {
      document.getElementById('volunteerDetailsBody').innerHTML = html;
      const detailsModal = new bootstrap.Modal(document.getElementById('volunteerDetailsModal'));
      detailsModal.show();
    })
    .catch(() => {
      document.getElementById('volunteerDetailsBody').innerHTML = 'Error loading profile.';
    });
}
</script>

</body>
</html>
