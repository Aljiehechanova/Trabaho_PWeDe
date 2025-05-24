<?php
// Database Connection
try {
    $db = new PDO("mysql:host=localhost;dbname=trabaho_pwede", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if form data exists
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and retrieve form data
    $jobTitle = htmlspecialchars($_POST['jobTitle'] ?? '');
    $disabilityType = htmlspecialchars($_POST['disabilityType'] ?? '');
    
    // Convert multiple selections into a comma-separated string
    $optionalSkills = isset($_POST['optionalSkills']) ? implode(", ", array_map('htmlspecialchars', $_POST['optionalSkills'])) : 'N/A';
    $requiredSkills = isset($_POST['requiredSkills']) ? implode(", ", array_map('htmlspecialchars', $_POST['requiredSkills'])) : '';
    $yearsExperience = htmlspecialchars($_POST['yearsExperience'] ?? 'N/A');

    // Insert Query
    $query = "INSERT INTO jobpost (
                jobpost_title, 
                disability_requirement, 
                skills_requirement, 
                optional_skills,
                years_experience
            ) VALUES (
                :jobTitle, 
                :disabilityType, 
                :requiredSkills, 
                :optionalSkills,
                :yearsExperience
            )";

    try {
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':jobTitle' => $jobTitle,
            ':disabilityType' => $disabilityType,
            ':requiredSkills' => $requiredSkills,
            ':optionalSkills' => $optionalSkills,
            ':yearsExperience' => $yearsExperience
        ]);

        // Redirect after success
        header("Location: posting.php");
        exit();
    } catch (PDOException $e) {
        echo "Error inserting data: " . $e->getMessage();
    }
} else {
    echo "Invalid form submission.";
}
?>
