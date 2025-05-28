<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("User fetch error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Builder</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease;
        }
        .back-button i {
            margin-right: 5px;
        }
        .back-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <a href="userPE.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="container">
        <h1><i class="fas fa-file-alt"></i> Resume Builder</h1>
        
        <div class="resume-builder">
            <form action="generate.php" method="POST" id="resumeForm" enctype="multipart/form-data">
                <!-- Personal Information -->
                <div class="section">
                    <h2><i class="fas fa-user"></i> Personal Information</h2>
                    <div class="photo-upload">
                    <div class="photo-preview" id="photoPreview">
                        <?php if (!empty($user['img'])): ?>
                            <img src="<?= htmlspecialchars($user['img']) ?>" alt="Profile Photo">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                    </div>
                                            <div class="upload-btn-wrapper">
                            <button class="upload-btn" type="button">
                                <i class="fas fa-camera"></i> Choose Photo
                            </button>
                            <input type="file" name="photo" accept="image/*" onchange="previewPhoto(this)">
                        </div>
                        <small class="photo-hint">Recommended size: 400x400px</small>
                    </div>
                    <div class="form-group">
                        <input type="text" name="fullName" placeholder="Full Name" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="tel" name="phone" placeholder="Phone Number" value="<?= htmlspecialchars($user['contact_number']) ?>" required>
                    </div>
                    <div class="form-group">
                        <textarea name="address" placeholder="Address" required><?= htmlspecialchars($user['location']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <textarea name="summary" placeholder="Professional Summary" required><?= htmlspecialchars($user['description']) ?></textarea>
                    </div>
                </div>

                <!-- Education -->
                <div class="section">
                    <h2><i class="fas fa-graduation-cap"></i> Education</h2>
                    <div id="educationFields">
                        <div class="education-entry">
                            <input type="text" name="education[]" placeholder="Degree/Course" required>
                            <input type="text" name="school[]" placeholder="School/University" required>
                            <input type="text" name="year[]" placeholder="Year" required>
                            <textarea name="education_description[]" placeholder="Description (optional)"></textarea>
                        </div>
                    </div>
                    <button type="button" class="add-btn" onclick="addEducation()">
                        <i class="fas fa-plus"></i> Add More Education
                    </button>
                </div>

                <!-- Experience -->
                <div class="section">
                    <h2><i class="fas fa-briefcase"></i> Work Experience</h2>
                    <div id="experienceFields">
                        <div class="experience-entry">
                            <input type="text" name="position[]" placeholder="Position" required>
                            <input type="text" name="company[]" placeholder="Company" required>
                            <div class="date-range">
                                <input type="date" name="start_date[]" placeholder="Start Date" required>
                                <input type="date" name="end_date[]" placeholder="End Date">
                                <label class="current-job">
                                    <input type="checkbox" name="current_job[]" onchange="toggleEndDate(this)">
                                    Current Job
                                </label>
                            </div>
                            <textarea name="description[]" placeholder="Job Description" required></textarea>
                        </div>
                    </div>
                    <button type="button" class="add-btn" onclick="addExperience()">
                        <i class="fas fa-plus"></i> Add More Experience
                    </button>
                </div>

                <!-- Skills -->
                <div class="section">
                    <h2><i class="fas fa-tools"></i> Skills</h2>
                    <div class="form-group">
                        <div class="skills-input">
                            <input type="text" id="skillInput" placeholder="Add a skill and press Enter">
                            <button type="button" onclick="addSkill()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div id="skillsList" class="skills-list"></div>
                        <input type="hidden" name="skills" id="skillsInput" required>
                    </div>
                </div>


                <button type="submit" class="submit-btn">
                    <i class="fas fa-file-export"></i> Generate Resume
                </button>
                <button type="submit" formaction="generate_pdf.php" class="submit-btn" style="background: var(--secondary-color);">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </button>
            </form>
        </div>
    </div>

    <script>
        let skills = [];

        function previewPhoto(input) {
            const preview = document.getElementById('photoPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Profile Photo">`;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function addEducation() {
            const educationFields = document.getElementById('educationFields');
            const newEntry = document.createElement('div');
            newEntry.className = 'education-entry';
            newEntry.innerHTML = `
                <input type="text" name="education[]" placeholder="Degree/Course" required>
                <input type="text" name="school[]" placeholder="School/University" required>
                <input type="text" name="year[]" placeholder="Year" required>
                <textarea name="education_description[]" placeholder="Description (optional)"></textarea>
                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            educationFields.appendChild(newEntry);
        }

        function addExperience() {
            const experienceFields = document.getElementById('experienceFields');
            const newEntry = document.createElement('div');
            newEntry.className = 'experience-entry';
            newEntry.innerHTML = `
                <input type="text" name="position[]" placeholder="Position" required>
                <input type="text" name="company[]" placeholder="Company" required>
                <div class="date-range">
                    <input type="date" name="start_date[]" placeholder="Start Date" required>
                    <input type="date" name="end_date[]" placeholder="End Date">
                    <label class="current-job">
                        <input type="checkbox" name="current_job[]" onchange="toggleEndDate(this)">
                        Current Job
                    </label>
                </div>
                <textarea name="description[]" placeholder="Job Description" required></textarea>
                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            experienceFields.appendChild(newEntry);
        }

        function addProject() {
            const projectFields = document.getElementById('projectFields');
            const newEntry = document.createElement('div');
            newEntry.className = 'project-entry';
            newEntry.innerHTML = `
                <input type="text" name="project_name[]" placeholder="Project Name" required>
                <input type="text" name="project_url[]" placeholder="Project URL (optional)">
                <textarea name="project_description[]" placeholder="Project Description" required></textarea>
                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            projectFields.appendChild(newEntry);
        }

        function toggleEndDate(checkbox) {
            const endDateInput = checkbox.closest('.date-range').querySelector('input[name="end_date[]"]');
            endDateInput.disabled = checkbox.checked;
            if (checkbox.checked) {
                endDateInput.value = '';
            }
        }

        function addSkill() {
            const skillInput = document.getElementById('skillInput');
            const skill = skillInput.value.trim();
            if (skill && !skills.includes(skill)) {
                skills.push(skill);
                updateSkillsList();
                skillInput.value = '';
            }
        }

        function removeSkill(skill) {
            skills = skills.filter(s => s !== skill);
            updateSkillsList();
        }

        function updateSkillsList() {
            const skillsList = document.getElementById('skillsList');
            const skillsInput = document.getElementById('skillsInput');
            skillsList.innerHTML = skills.map(skill => `
                <span class="skill">
                    ${skill}
                    <button type="button" onclick="removeSkill('${skill}')" class="remove-skill">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            `).join('');
            skillsInput.value = skills.join(',');
        }

        document.getElementById('skillInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addSkill();
            }
        });

        document.getElementById('resumeForm').addEventListener('submit', function(e) {
            if (skills.length === 0) {
                e.preventDefault();
                alert('Please add at least one skill');
            }
        });
    </script>
</body>
</html>
