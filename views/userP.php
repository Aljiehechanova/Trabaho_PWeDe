<?php
session_start();
include '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile - Trabaho PWeDe</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/user_profile.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo" width="40" height="40" class="me-2">
      <span class="fw-bold">TrabahoPWeDe</span>
    </a>
    <div class="ms-auto">
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="settingsMenu" data-bs-toggle="dropdown" aria-expanded="false">
          Settings
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsMenu">
          <li><a class="dropdown-item" href="#">Edit Profile</a></li>
          <li><a class="dropdown-item" href="#">Change Password</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="../logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <div class="profile-card text-center">
        <!-- Clickable Profile Image -->
        <img src="<?= htmlspecialchars($user['img'])?>" alt="Profile Picture" onerror="this.onerror=null;this.src='../assets/images/alterprofile.png';" class="profile-img" data-bs-toggle="modal" data-bs-target="#editPhotoModal">


        <h2>User Profile</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['fullname']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Description:</strong> Passionate about smart technologies and web development.</p>
        <p><strong>Location:</strong> Manila, Philippines</p>

        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProfileModal">
            Edit Profile
        </button>
    </div>
</div>

<!-- Modal for Editing Profile Info -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="update_profile.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5>Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">
        <div class="mb-3">
          <label for="fullname" class="form-label">Full Name</label>
          <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal for Editing Profile Picture -->
<div class="modal fade" id="editPhotoModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="upload_photo.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5>Edit Profile Picture</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">

        <div class="mb-3">
          <label for="profile_photo" class="form-label">Upload a photo</label>
          <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
        </div>

        <div class="mb-3">
          <label class="form-label">Or take a photo</label><br>
          <video id="video" width="100%" autoplay></video>
          <canvas id="canvas" style="display:none;"></canvas>
          <input type="hidden" name="webcam_image" id="webcam_image">
          <button type="button" class="btn btn-outline-primary mt-2" onclick="capture()">Capture</button>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save Photo</button>
      </div>
    </form>
  </div>
</div>

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

    const dataURL = canvas.toDataURL('image/png');
    document.getElementById('webcam_image').value = dataURL;
}

// Enable webcam
document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('video');
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
                video.play();
            })
            .catch(error => {
                console.warn("Webcam access denied or not available:", error);
            });
    }
});
</script>

</body>
</html>
