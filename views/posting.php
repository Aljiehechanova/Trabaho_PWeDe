<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posting</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/posting.css">
</head>
<body>
<div class="top-navbar">
  <button onclick="location.href='userD.php'">User</button>
  <button onclick="location.href='clientD.php'">Client</button>
  <button onclick="location.href='addash.php'">Admin</button>
</div>
    <div class="sidebar">
        <ul>
            <li><a href="clientL.php">View Job List</a></li>
            <li class="active"><a href="posting.php">Posting</a></li>
            <li><a href="clientD.php">Analytic Dashboard</a></li>
            <li><a href="clientM.php">Messages</a></li>
        </ul>
    </div>

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
