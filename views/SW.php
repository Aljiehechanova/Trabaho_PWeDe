<?php
include '../config/db_connection.php';
include '../models/ClientModel.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clientModel = new ClientModel($conn);

    $data = [
        'workshopTitle' => $_POST['workshopTitle'] ?? '',
        'workshopDescription' => $_POST['workshopDescription'] ?? '',
        'targetSkills' => $_POST['targetSkills'] ?? ''
    ];

    if ($clientModel->insertWorkshopApplication($data)) {
        echo "<script>
            alert('Workshop application submitted successfully!');
            window.location.href = 'client_profile.php';
        </script>";
    } else {
        echo "<script>
            alert('Error submitting workshop application.');
            window.location.href = 'client_profile.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Invalid request.');
        window.location.href = 'client_profile.php';
    </script>";
}
?>
