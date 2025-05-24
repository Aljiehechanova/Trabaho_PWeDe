document.addEventListener("DOMContentLoaded", function () {
    const formFields = document.querySelectorAll("#resume-form input, #resume-form select");
    const progressBar = document.querySelector(".progress");
    const progressPercent = document.getElementById("progress-percent");
    const previewName = document.getElementById("preview-name");
    const previewEmail = document.getElementById("preview-email");
    const barangayInput = document.getElementById("barangay");
    const purokInput = document.getElementById("purok");
    const barangayCheckmark = document.getElementById("barangay-checkmark");

    const barangayData = {
        "Alangilan": ["Purok 1", "Purok 2", "Purok 3"],
        "Banago": ["Purok 1", "Purok 2"],
        "Bata": ["Purok 1", "Purok 2", "Purok 3", "Purok 4"],
        "Cabug": ["Purok 1"],
        "Estefania": ["Purok 1", "Purok 2"],
        "Granada": ["Purok 1", "Purok 2"],
        "Handumanan": ["Purok 1", "Purok 2"],
        "Mandalagan": ["Purok 1", "Purok 2", "Purok 3"],
        "Mansilingan": ["Purok 1", "Purok 2", "Purok 3"],
        "Montevista": ["Purok 1", "Purok 2"],
        "Pahanocoy": ["Purok 1", "Purok 2"],
        "Punta Taytay": ["Purok 1", "Purok 2", "Purok 3"],
        "Singcang-Airport": ["Purok 1", "Purok 2"],
        "Sum-ag": ["Purok 1", "Purok 2"],
        "Taculing": ["Purok 1", "Purok 2"],
        "Tangub": ["Purok 1", "Purok 2"],
        "Villamonte": ["Purok 1", "Purok 2", "Purok 3"],
        "Vista Alegre": ["Purok 1", "Purok 2", "Purok 3"]
    };

    function updateProgress() {
        let filledFields = Array.from(formFields).filter(field => field.value.trim() !== "").length;
        let progress = (filledFields / formFields.length) * 100;
        progressBar.style.width = `${progress}%`;
        progressPercent.textContent = `${Math.round(progress)}%`;
    }

    function updatePreview() {
        previewName.textContent = document.getElementById("firstname").value || "Your Name";
        previewEmail.textContent = document.getElementById("email").value || "your@email.com";
    }

    function populateBarangayList() {
        const dataList = document.getElementById("barangay-list");
        dataList.innerHTML = ""; 
        Object.keys(barangayData).forEach(brgy => {
            let option = document.createElement("option");
            option.value = brgy;
            dataList.appendChild(option);
        });
    }

    function validateBarangay() {
        let selectedBarangay = barangayInput.value;
        if (barangayData[selectedBarangay]) {
            barangayCheckmark.style.display = "inline";
            populatePurokList(barangayData[selectedBarangay]);
            purokInput.disabled = false;
        } else {
            barangayCheckmark.style.display = "none";
            purokInput.disabled = true;
            purokInput.value = ""; 
        }
        updateProgress();
    }

    function populatePurokList(puroks) {
        let purokList = document.getElementById("purok-list");
        purokList.innerHTML = "";
        puroks.forEach(purok => {
            let option = document.createElement("option");
            option.value = purok;
            purokList.appendChild(option);
        });
    }

    function previewResume() {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const experience = document.getElementById('experience').value.trim();

        if (name && email && experience) {
            document.getElementById('preview-section').style.display = 'block';
            document.getElementById('resume-preview').innerHTML =
                `<strong>Name:</strong> ${name} <br>
                 <strong>Email:</strong> ${email} <br>
                 <strong>Experience:</strong> ${experience}`;
            document.getElementById('download-btn').disabled = false;
        } else {
            alert('Please enter all details before previewing.');
        }
    }

    function selectOption(element, selector) {
        document.querySelectorAll(selector).forEach(option => option.classList.remove('selected'));
        element.classList.add('selected');
    }

    function showSection(sectionId) {
        document.querySelectorAll('.section').forEach(section => section.classList.remove('active'));
        document.getElementById(sectionId).classList.add('active');

        document.querySelectorAll('.step').forEach(step => step.classList.remove('active'));
        document.getElementById('step-' + sectionId).classList.add('active');
    }

    function navigateStep(currentStep, direction) {
        let newStep = currentStep + direction;
        document.getElementById('step-' + currentStep).classList.remove('active');
        document.getElementById('step-' + newStep).classList.add('active');
    }

    function validateInput(input, checkmarkId) {
        document.getElementById(checkmarkId).style.display = input.value ? 'inline' : 'none';
    }

    formFields.forEach(field => field.addEventListener("input", () => {
        updateProgress();
        updatePreview();
    }));

    barangayInput.addEventListener("input", validateBarangay);
    purokInput.addEventListener("input", updateProgress);

    document.getElementById("preview-btn").addEventListener("click", () => {
        alert("Preview updated! Check the right panel.");
    });

    populateBarangayList();

    window.showSection = showSection;
    window.previewResume = previewResume;
    window.selectColor = (el) => selectOption(el, ".color-option");
    window.selectTemplate = (el) => selectOption(el, ".template-option");
    window.nextStep = (step) => navigateStep(step, 1);
    window.prevStep = (step) => navigateStep(step, -1);
    window.validateInput = validateInput;
});
function showSection(section) {
    document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
    
    document.getElementById(section).classList.add('active');

    document.querySelector(".color-container").style.display = "none";

    if (section === "template") {
        document.querySelector(".color-container").style.display = "block";
    }
}
function updatePreview() {
    var firstname = document.getElementById('firstname').value;
    var lastname = document.getElementById('lastname').value;
    var barangay = document.getElementById('barangay').value;
    var purok = document.getElementById('purok').value;
    var postcode = document.getElementById('postcode').value;
    var phone = document.getElementById('phone').value;
    var email = document.getElementById('email').value;

    var resumeBox = document.getElementById('resume-box');
    resumeBox.innerHTML = `
        <h2>${firstname} ${lastname}</h2>
        <h3>Contact Information</h3>
        <p>Barangay: ${barangay}, Purok: ${purok}, ${postcode}</p>
        <p>Phone: ${phone}</p>
        <p>Email: ${email}</p>
        <!-- Add more sections like Work History, Education, Skills, and Summary here as needed -->
    `;
}
