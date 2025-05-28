<?php
session_start();
include '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        die("User not found.");
    }

    // Required fields (resume is optional)
    $requiredFields = ['fullname', 'location', 'disability', 'contact'];
    $missingFields = [];

    foreach ($requiredFields as $field) {
        if (empty($user[$field])) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        echo "<script>
            alert('Please complete your profile before accessing this page. Missing: " . implode(', ', $missingFields) . "');
            window.location.href = 'edit_profile.php';
        </script>";
        exit;
    }

    $loggedInEmail = $user['email'];

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

$notif_stmt = $conn->prepare("SELECT message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$notif_stmt->execute([$user_id]);
$notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile - Trabaho PWeDe</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/user_profile.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<div class="container mt-5">
    <div class="mb-3">
        <a href="UserD.php" class="btn btn-outline-primary">&larr; Go to Dashboard</a>
    </div>
    <div class="profile-card text-center">
        <img src="<?= htmlspecialchars($user['img']) ?>" alt="Profile Picture" onerror="this.onerror=null;this.src='../assets/images/alterprofile.png';" class="profile-img" data-bs-toggle="modal" data-bs-target="#editPhotoModal">
        <h2>User Profile</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['fullname']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Contact:</strong> <?= htmlspecialchars($user['contact'] ?? 'N/A') ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($user['description'] ?? 'N/A') ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($user['location'] ?? 'N/A') ?></p>
        <p><strong>Disability:</strong> <?= htmlspecialchars($user['disability'] ?? 'N/A') ?></p>

        <div class="d-flex justify-content-center gap-2 mt-3">
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>

            <?php if (!empty($user['resume'])): ?>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#resumeModal">View Resume</button>

                <!-- Resume Modal -->
                <div class="modal fade" id="resumeModal" tabindex="-1" aria-labelledby="resumeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="resumeModalLabel">View Resume</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <?php
                                    $resumePath = "../" . htmlspecialchars($user['resume']);
                                    $ext = strtolower(pathinfo($resumePath, PATHINFO_EXTENSION));

                                    if (file_exists($resumePath)) {
                                        if (in_array($ext, ['pdf', 'html', 'htm'])) {
                                            echo "<iframe id='resumeFrame' src='$resumePath' width='100%' height='600px' style='border: none;'></iframe>";
                                        } else {
                                            echo "<p>Unsupported resume format. <a href='$resumePath' download>Download it here</a>.</p>";
                                        }
                                    } else {
                                        echo "<p class='text-danger'>Resume file not found. Please regenerate it.</p>";
                                    }
                                ?>
                            </div>
                            <div class="modal-footer">
                                <?php if (file_exists($resumePath)): ?>
                                    <a href="<?= $resumePath ?>" class="btn btn-success" download>Download</a>
                                    <button type="button" class="btn btn-primary" onclick="printResume()">Print</button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-muted mt-3">You have not generated a resume yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Webcam and Print Scripts -->
<script>
function capture() {
    const canvas = document.getElementById('canvas');
    const video = document.getElementById('video');
    const ctx = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    canvas.style.display = 'block';
    video.style.display = 'none';
    document.getElementById('webcam_image').value = canvas.toDataURL('image/png');
}

document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('video');
    if (navigator.mediaDevices?.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
                video.play();
            })
            .catch(error => console.warn("Webcam access denied or not available:", error));
    }
});

function printResume() {
    const iframe = document.querySelector('#resumeModal iframe');
    if (iframe) {
        iframe.focus();
        iframe.contentWindow.print();
    } else {
        alert("Resume is not in a printable format.");
    }
}
</script>

</body>
</html>
