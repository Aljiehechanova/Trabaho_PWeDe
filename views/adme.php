<?php
require '../config/db_connection.php';

$loggedInEmail = "aljiehechanova@gmail.com";

$stmt = $conn->prepare("SELECT * FROM messages WHERE receiver_email = ?");
$stmt->execute([$loggedInEmail]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Chatbox</title>
    <link rel="stylesheet" href="../assets/css/defaultstyle.css">
    <link rel="stylesheet" href="../assets/css/global.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Trabaho PWeDe">
        </div>
        <ul>
            <li><a href="adapp.php">Approval Job List</a></li>
            <li><a href="appoint.php">Appointment</a></li>
            <li><a href="addash.php">Admin Dashboard</a></li>
            <li class="active"><a href="adme.php">Messages</a></li>
        </ul>
    </div>
    <div class="main-content">
    <h1>Inbox</h1>

    <button class="open-modal-btn" onclick="document.getElementById('sendModal').style.display='block'">Compose Message</button>

    <?php if ($messages): ?>
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