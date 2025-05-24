<?php
include '../config/db_connection.php';
include '../models/ClientModel.php';

class ClientController {
    private $clientModel;

    public function __construct($db) {
        $this->clientModel = new ClientModel($db);
    }

    public function submitJobApplication() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                'jobTitle'         => $_POST['jobTitle'],
                'jobDescription'   => $_POST['jobDescription'],
                'disabilityType'   => $_POST['disabilityType'],
                'yearsExperience'  => $_POST['yearsExperience'],
                'optionalSkills'   => $_POST['optionalSkills'],
                'requiredSkills'   => $_POST['requiredSkills']
            ];

            if ($this->clientModel->insertJobApplication($data)) {
                echo "Job application submitted successfully!";
            } else {
                echo "Error submitting job application.";
            }
        }
    }

    public function submitWorkshopApplication() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                'workshopTitle'        => $_POST['workshopTitle'],
                'workshopDescription'  => $_POST['workshopDescription'],
                'targetSkills'         => $_POST['targetSkills']
            ];

            if ($this->clientModel->insertWorkshopApplication($data)) {
                echo "Workshop application submitted successfully!";
            } else {
                echo "Error submitting workshop application.";
            }
        }
    }
}

$controller = new ClientController($conn);

if (isset($_POST['submitJob'])) {
    $controller->submitJobApplication();
} elseif (isset($_POST['submitWorkshop'])) {
    $controller->submitWorkshopApplication();
}
?>
