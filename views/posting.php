<?php
session_start();
require_once '../config/db_connection.php';

// Redirect if not logged in
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posting</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/posting.css">
    <link rel="stylesheet" href="../assets/css/dashboardstyle.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
<div class="sidebar">
    <ul>
        <li><a href="clientL.php">View Job List</a></li>
        <li class="active"><a href="posting.php">Posting</a></li>
        <li><a href="clientD.php">Analytic Dashboard</a></li>
        <li><a href="clientM.php">Inbox</a></li>
    </ul>
</div>
<div class="layout-container">
    <div class="main-content">
        <h1>Create a New Application</h1>

        <!-- Suggestion Button -->
        <div class="suggestion-buttons">
            <button onclick="showForm('job')">Create Job Application</button>
            <button onclick="showForm('workshop')">Create Workshop Application</button>
        </div>

        <!-- Job Application Form -->
        <div id="jobForm" class="application-form" style="display: none;">
            <h2>Job Application</h2>
            <form action="SJ.php" method="POST">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">

                <label for="jobTitle">Job Title:</label>
                <select id="jobTitle" name="jobTitle" onchange="toggleOtherField('jobTitle', 'otherJobTitleField')">
                    <option value="Data Encoder">Data Encoder</option>
                    <option value="Call Center Agent">Call Center Agent</option>
                    <option value="Graphic Designer">Graphic Designer</option>
                    <option value="Software Developer">Software Developer</option>
                    <option value="Administrative Assistant">Administrative Assistant</option>
                    <option value="Freelance Writer">Freelance Writer</option>
                    <option value="Customer Support Representative">Customer Support Representative</option>
                    <option value="Massage Therapist">Massage Therapist</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" id="otherJobTitleField" name="otherJobTitle" placeholder="Enter Job Title" style="display: none;">

                <label for="disabilityType">Disability Type:</label>
                <select id="disabilityType" name="disabilityType" required>
                    <option value="Visual Impairment">Visual Impairment</option>
                    <option value="Hearing Impairment">Hearing Impairment</option>
                    <option value="Physical Disability">Physical Disability</option>
                    <option value="Speech Impairment">Speech Impairment</option>
                </select>

                <label for="yearsExperience">Years of Experience:</label>
                <select id="yearsExperience" name="yearsExperience" required>
                    <option value="N/A">N/A</option>
                    <option value="1-2 years">1-2 years</option>
                    <option value="3-5 years">3-5 years</option>
                    <option value="5+ years">5+ years</option>
                </select>

                <label for="requiredSkills">Required Skills:</label>
                <select id="requiredSkills" name="requiredSkills[]" multiple onchange="toggleOtherField('requiredSkills', 'otherRequiredSkillsField')">
                    <option value="Computer Literacy">Computer Literacy</option>
                    <option value="Graphic Design">Graphic Design</option>
                    <option value="Programming">Programming</option>
                    <option value="Customer Service">Customer Service</option>
                    <option value="Data Entry">Data Entry</option>
                    <option value="Public Speaking">Public Speaking</option>
                    <option value="Massage Therapy">Massage Therapy</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" id="otherRequiredSkillsField" name="otherRequiredSkills" placeholder="Enter Required Skills" style="display: none;">

                <label for="optionalSkills">Optional Skills:</label>
                <select id="optionalSkills" name="optionalSkills[]" multiple onchange="toggleOtherField('optionalSkills', 'otherOptionalSkillsField')">
                    <option value="Video Editing">Video Editing</option>
                    <option value="Social Media Management">Social Media Management</option>
                    <option value="Content Writing">Content Writing</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Technical Support">Technical Support</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" id="otherOptionalSkillsField" name="otherOptionalSkills" placeholder="Enter Optional Skills" style="display: none;">

                <button type="submit">Submit Job Application</button>
            </form>
        </div>

        <!-- Workshop Application Form -->
        <div id="workshopForm" class="application-form" style="display: none;">
            <h2>Workshop Application</h2>
            <form action="SW.php" method="POST">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">

                <label for="workshopTitle">Workshop Title:</label>
                <input type="text" id="workshopTitle" name="workshopTitle" required>

                <label for="workshopDescription">Workshop Description:</label>
                <textarea id="workshopDescription" name="workshopDescription" rows="4" required></textarea>

                <label for="targetSkills">Target Skills:</label>
                <input type="text" id="targetSkills" name="targetSkills" required>

                <button type="submit">Submit Workshop Application</button>
            </form>
        </div>
    </div>
</div>
<!-- JavaScript -->
<script>
    function showForm(type) {
        document.getElementById('jobForm').style.display = (type === 'job') ? 'block' : 'none';
        document.getElementById('workshopForm').style.display = (type === 'workshop') ? 'block' : 'none';
    }

    function toggleOtherField(selectId, otherFieldId) {
        const select = document.getElementById(selectId);
        const otherField = document.getElementById(otherFieldId);

        if (select.value === "Other" || Array.from(select.selectedOptions).some(option => option.value === "Other")) {
            otherField.style.display = "block";
            otherField.required = true;
        } else {
            otherField.style.display = "none";
            otherField.required = false;
            otherField.value = "";
        }
    }
</script>
</body>
</html>
