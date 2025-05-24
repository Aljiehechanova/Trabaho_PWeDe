<?php
class ClientModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Insert Job Application
    public function insertJobApplication($data) {
        try {
            $query = "INSERT INTO jobpost 
                      (jobpost_title, disability_requirement, years_experience, optional_skills, skills_requirement) 
                      VALUES 
                      (:jobTitle, :disabilityType, :yearsExperience, :optionalSkills, :requiredSkills)";

            $stmt = $this->conn->prepare($query);

            // Bind parameters
            $stmt->bindParam(':jobTitle', $data['jobTitle']);
            $stmt->bindParam(':disabilityType', $data['disabilityType']);
            $stmt->bindParam(':yearsExperience', $data['yearsExperience']);
            $stmt->bindParam(':optionalSkills', $data['optionalSkills']);
            $stmt->bindParam(':requiredSkills', $data['requiredSkills']);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Insert Workshop Application
    public function insertWorkshopApplication($data) {
        $query = "INSERT INTO workshop (
            workshop_title,
            description,
            target_skills
        ) VALUES (
            :workshopTitle,
            :workshopDescription,
            :targetSkills
        )";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':workshopTitle', $data['workshopTitle']);
        $stmt->bindParam(':workshopDescription', $data['workshopDescription']);
        $stmt->bindParam(':targetSkills', $data['targetSkills']);

        return $stmt->execute();
    }
}
?>
