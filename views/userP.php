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

    $requiredFields = ['fullname', 'location', 'disability', 'contact_number'];
    $missingFields = [];

    foreach ($requiredFields as $field) {
        if (empty($user[$field])) {
            $missingFields[] = $field;
        }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<div class="container mt-5">
    <?php if (empty($missingFields)): ?>
        <div class="mb-3">
            <a href="UserD.php" class="btn btn-outline-primary">&larr; Go to Dashboard</a>
        </div>
    <?php else: ?>
        <div class="mb-3">
            <button class="btn btn-outline-secondary" disabled>&larr; Go to Dashboard</button>
        </div>
    <?php endif; ?>

    <div class="profile-card text-center">
        <img src="<?= htmlspecialchars($user['img']) ?>" alt="Profile Picture" onerror="this.onerror=null;this.src='../assets/images/alterprofile.png';" class="profile-img" data-bs-toggle="modal" data-bs-target="#editPhotoModal">
        <h2>User Profile</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['fullname']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Contact:</strong> <?= htmlspecialchars($user['contact_number'] ?? 'N/A') ?></p>
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

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
     data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                    <div class="col-md-6">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email (read-only)</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="contact_number" class="form-label">Contact</label>
                        <input type="text" class="form-control" name="contact_number" value="<?= htmlspecialchars($user['contact_number']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($user['location']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="disability" class="form-label">Disability</label>
                        <input type="text" class="form-control" name="disability" value="<?= htmlspecialchars($user['disability']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"><?= htmlspecialchars($user['description']) ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <label for="img" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" name="img">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Lock Modal If Required Fields Missing -->
<?php if (!empty($missingFields)): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    alert("Please complete your profile before accessing this page. Missing: <?= implode(', ', $missingFields) ?>");
    var modal = new bootstrap.Modal(document.getElementById('editProfileModal'));
    modal.show();
});
</script>
<?php endif; ?>

<script>
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
