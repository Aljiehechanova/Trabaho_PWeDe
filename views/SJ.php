<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Database Connection
try {
    $db = new PDO("mysql:host=localhost;dbname=trabahopwede", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $jobTitle = htmlspecialchars($_POST['jobTitle'] ?? '');
    $disabilityType = htmlspecialchars($_POST['disabilityType'] ?? '');
    $optionalSkills = isset($_POST['optionalSkills']) ? implode(", ", array_map('htmlspecialchars', $_POST['optionalSkills'])) : 'N/A';
    $requiredSkills = isset($_POST['requiredSkills']) ? implode(", ", array_map('htmlspecialchars', $_POST['requiredSkills'])) : '';
    $yearsExperience = htmlspecialchars($_POST['yearsExperience'] ?? 'N/A');

    $query = "INSERT INTO jobpost (
        jobpost_title, 
        disability_requirement, 
        skills_requirement, 
        optional_skills,
        years_experience,
        user_id
    ) VALUES (
        :jobTitle, 
        :disabilityType, 
        :requiredSkills, 
        :optionalSkills,
        :yearsExperience,
        :user_id
    )";

    try {
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':jobTitle' => $jobTitle,
            ':disabilityType' => $disabilityType,
            ':requiredSkills' => $requiredSkills,
            ':optionalSkills' => $optionalSkills,
            ':yearsExperience' => $yearsExperience,
            ':user_id' => $user_id
        ]);

        header("Location: posting.php");
        exit();
    } catch (PDOException $e) {
        echo "Error inserting data: " . $e->getMessage();
    }
} else {
    echo "Invalid form submission.";
}
?>
