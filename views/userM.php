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
  $stmt = $conn->prepare("SELECT fullname, img, email FROM users WHERE user_id = ?");
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
  die("Message fetch error: " . $e->getMessage());
}
$notif_stmt = $conn->prepare("SELECT message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$notif_stmt->execute([$user_id]);
$notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Chatbox</title>
    <link rel="stylesheet" href="../assets/css/defaultstyle.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="UserD.php">
                <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
                <span class="fw-bold">TrabahoPWeDe</span>
            </a>

            <div class="ms-auto d-flex align-items-center">

                <!-- Notification bell -->
                <div class="dropdown me-3">
                    <button class="btn btn-light position-relative" type="button" id="notifDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
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
                    <img src="<?= htmlspecialchars($user['img']) ?>" alt="Profile" class="rounded-circle" width="40"
                        height="40" style="object-fit: cover; margin-right: 10px;">
                    <span class="fw-semibold text-dark"><?= htmlspecialchars($user['fullname']) ?></span>
                </a>

                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="settingsMenu"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Settings
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsMenu">
                        <li><a class="dropdown-item" href="userP.php">Edit Profile</a></li>
                        <li><a class="dropdown-item" href="#">Change Password</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="login.php">Logout</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </nav>
    <div class="layout-container">
        <div class="sidebar">
            <ul>
                <li><a href="userPE.php">Profile Enhancer</a></li>
                <li><a href="JM.php">Job Matching</a></li>
                <li><a href="userD.php">Analytic Dashboard</a></li>
                <li class="active"><a href="userM.php">Inbox</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>Inbox</h1>

            <button class="open-modal-btn" onclick="document.getElementById('sendModal').style.display='block'">Compose
                Message</button>

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
                <form action="USM.php" method="POST">
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
    // Close modal on outside click
    window.onclick = function(event) {
        var modal = document.getElementById('sendModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
</body>

</html>