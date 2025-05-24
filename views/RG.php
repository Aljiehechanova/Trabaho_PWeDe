<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Builder</title>
    <link rel="stylesheet" href="../assets/css/resumebuilder.css">
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Logo">
            <h1>Resume Builder</h1>
        </div>
        <div class="steps">
            <div id="step-template" class="step active" onclick="showSection('template')">1. Choose Template</div>
            <div id="step-details" class="step" onclick="showSection('details')">2. Enter Details</div>
            <div id="step-download" class="step" onclick="showSection('download')">3. Download Resume</div>
        </div>

        <div id="template" class="section active">
            <h2>Choose a Template</h2>
            <div class="template-option" onclick="selectTemplate(this)">Template 1</div>
            <div class="template-option" onclick="selectTemplate(this)">Template 2</div>
            <div class="template-option" onclick="selectTemplate(this)">Template 3</div>
            <button onclick="showSection('details')">Next</button>
        </div>

        <div class="step-1">
            <div class="color-container">
                <p class="color-title">COLOR</p>
                <div class="color-options">
                    <div class="color-option selected" style="background: linear-gradient(45deg, white 50%, lightgray 50%);" onclick="selectColor(this)"></div>
                    <div class="color-option" style="background-color: gray;" onclick="selectColor(this)"></div>
                    <div class="color-option" style="background-color: navy;" onclick="select(this)"></div>
                    <div class="color-option" style="background-color: purple;" onclick="selectColor(this)"></div>
                    <div class="color-option" style="background-color: deepskyblue;" onclick="selectColor(this)"></div>
                    <div class="color-option" style="background-color: mediumturquoise;" onclick="selectColor(this)"></div>
                    <div class="color-option" style="background-color: darkgreen;" onclick="selectColor(this)"></div>
                    <div class="color-option" style="background-color: brown;" onclick="selectColor(this)"></div>
                    <div class="color-option" style="background-color: lightcoral;" onclick="selectColor(this)"></div>
                    <div class="color-option" style="background-color: gold;" onclick="selectColor(this)"></div>
                </div>
            </div>
        </div>

        <div id="details" class="section">
            <aside class="sidebar">
                <h2>Resume Builder</h2>
                <ol>
                    <li class="active">Heading</li>
                    <li>Work History</li>
                    <li>Education</li>
                    <li>Skills</li>
                    <li>Summary</li>
                    <li>Finalize</li>
                </ol>
                <div class="progress-container">
                    <p>Resume Completeness:</p>
                    <div class="progress-bar">
                        <div class="progress" id="progress"></div>
                    </div>
                    <span id="progress-percent">0%</span>
                </div>
            </aside>
            <main>
                <h1>Get your Resume To get a Job you Deserve</h1>
                <p>We suggest including an email and phone number.</p>
                <form id="resume-form" novalidate>
                    <div class="input-container">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" placeholder="e.g. Maria" required oninput="validateInput(this, 'firstname-checkmark'); updatePreview();">
                        <span id="firstname-checkmark" class="checkmark">✔</span>
                    </div>

                    <div class="input-container">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" placeholder="e.g. Santos" required oninput="validateInput(this, 'lastname-checkmark'); updatePreview();">
                        <span id="lastname-checkmark" class="checkmark">✔</span>
                    </div>

                    <div class="input-container">
                        <label for="barangay"><strong>Barangay</strong></label>
                        <input type="text" id="barangay" name="barangay" placeholder="Enter Barangay" list="barangay-list" oninput="validateBarangay(); updatePreview();">
                        <datalist id="barangay-list"></datalist>
                        <span id="barangay-checkmark" class="checkmark">✔</span>
                    </div>

                    <div class="input-container">
                        <label for="purok"><strong>Purok</strong></label>
                        <input type="text" id="purok" name="purok" placeholder="Enter Purok" list="purok-list" oninput="validatePurok(); updatePreview();" disabled>
                        <datalist id="purok-list"></datalist>
                        <span id="purok-checkmark" class="checkmark">✔</span>
                    </div>

                    <div class="input-container">
                        <label for="postcode">Postcode</label>
                        <input type="text" id="postcode" name="postcode" placeholder="e.g. 1000" required oninput="validateInput(this, 'postcode-checkmark'); updatePreview();">
                        <span id="postcode-checkmark" class="checkmark">✔</span>
                    </div>

                    <div class="input-container">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" placeholder="e.g. 2 1234 5678" required oninput="validateInput(this, 'phone-checkmark'); updatePreview();">
                        <span id="phone-checkmark" class="checkmark">✔</span>
                    </div>

                    <div class="input-container">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="e.g. maria@email.com" required oninput="validateInput(this, 'email-checkmark'); updatePreview();">
                        <span id="email-checkmark" class="checkmark">✔</span>
                    </div>

                    <button id="preview-btn" type="button">Preview</button>
                    <button id="next-btn" type="button" onclick="showSection('work-history')">Next</button>
                </form>
            </main>
            <aside class="resume-preview">
                <h3>Resume Preview</h3>
                <div class="resume-box" id="resume-box">
                </div>
            </aside>
        </div>

        <div id="download" class="section">
            <h2>Resume Preview</h2>
            <div id="preview-section">
                <p id="resume-preview">Your resume will appear here before download.</p>
            </div>
            <button id="download-btn" disabled>Download PDF</button>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
