<?php
session_start();
require '../config/db_connection.php';

// Ensure client is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

// Fetch client info
$stmt = $conn->prepare("SELECT fullname, email, img FROM users WHERE user_id = ?");
$stmt->execute([$client_id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    die("Client not found.");
}

$loggedInEmail = $client['email'];

// Fetch messages
$stmt = $conn->prepare("SELECT sender_email, subject, message FROM messages WHERE receiver_email = ? ORDER BY messages_id DESC");
$stmt->execute([$loggedInEmail]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Client Chatbox</title>
  <link rel="stylesheet" href="../assets/css/defaultstyle.css">
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="../assets/css/dashboardstyle.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="clientD.php">
      <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
      <span class="fw-bold">TrabahoPWeDe</span>
    </a>
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          Settings
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="login.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>
<div class="layout-container">
  <!-- Sidebar -->
  <div class="sidebar">
    <ul>
      <li><a href="approve_job.php">Approval Job List</a></li>
      <li><a href="adapp.php">Appointment</a></li>
      <li><a href="addash.php">Admin Dashboard</a></li>
      <li class="active><a href="adme.php">Messages</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Inbox</h1>

    <button class="open-modal-btn" onclick="document.getElementById('sendModal').style.display='block'">Compose Message</button>

    <?php if (!empty($messages)): ?>
      <?php foreach ($messages as $msg): ?>
        <div class="message-card">
          <p><strong>From:</strong> <?= htmlspecialchars($msg['sender_email']) ?></p>
          <p><strong>Subject:</strong> <?= htmlspecialchars($msg['subject']) ?></p>
          <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No messages found.</p>
    <?php endif; ?>
  </div>

  <!-- Modal -->
  <div id="sendModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="document.getElementById('sendModal').style.display='none'">&times;</span>
      <h2>Send Message</h2>
      <form action="send_message.php" method="POST">
        <label for="receiver_email">Send To (Email):</label>
        <input type="email" name="receiver_email" required>

        <label for="subject">Subject:</label>
        <input type="text" name="subject">

        <label for="message">Message:</label>
        <textarea name="message" rows="5" required></textarea>

        <input type="hidden" name="sender_email" value="<?= htmlspecialchars($loggedInEmail) ?>">

        <button type="submit">Send Message</button>
      </form>
    </div>
  </div>
</div>
<script>
  // Close modal when clicking outside of it
  window.onclick = function(event) {
    var modal = document.getElementById('sendModal');
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
</script>
</body>
</html>
