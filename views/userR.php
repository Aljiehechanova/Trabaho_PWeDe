<?php
include '../config/db_connection.php';
include '../controllers/UserController.php';

$userController = new UserController($conn);
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['agreement'])) {
        $error = "You must agree to the terms and conditions.";
    } else {
        $user_type = "job_seeker";
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $disability = $_POST['disability'];

        $result = $userController->registerJobSeeker($user_type, $fullname, $email, $password, $disability);

        if ($result === true) {
            echo "<script>alert('Registration successful! Redirecting to login...'); window.location.href = 'login.php';</script>";
            exit();
        } else {
            $error = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Job Seeker Registration - Trabaho PWeDe</title>
    <link rel="stylesheet" href="../assets/css/register.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h2 class="text-center">Job Seeker Registration</h2>

                <?php if (!empty($error)) { ?>
                    <script>
                        alert("<?php echo $error; ?>");
                    </script>
                <?php } ?>

                <form method="POST">
                    <input type="hidden" name="user_type" value="job_seeker">

                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="password-requirements">
                        <p>Password must contain:</p>
                        <ul id="passwordRules">
                            <li id="length" class="invalid">✔ More than 8 characters</li>
                            <li id="uppercase" class="invalid">✔ An uppercase letter</li>
                            <li id="lowercase" class="invalid">✔ A lowercase letter</li>
                            <li id="number" class="invalid">✔ A number (0–9)</li>
                            <li id="special" class="invalid">✔ A special character (!@#$%^&*)</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label for="disability" class="form-label">Disability</label>
                        <select class="form-select" id="disability" name="disability" required>
                            <option value="" disabled <?= empty($user['disability']) ? 'selected' : '' ?>>Select Disability</option>
                            <option value="Visual Impairment" <?= ($user['disability'] === 'Visual Impairment') ? 'selected' : '' ?>>Visual Impairment</option>
                            <option value="Hearing Impairment" <?= ($user['disability'] === 'Hearing Impairment') ? 'selected' : '' ?>>Hearing Impairment</option>
                            <option value="Physical Impairment" <?= ($user['disability'] === 'Physical Impairment') ? 'selected' : '' ?>>Physical Impairment</option>
                            <option value="Speech Impairment" <?= ($user['disability'] === 'Speech Impairment') ? 'selected' : '' ?>>Speech Impairment</option>
                        </select>
                        </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="agreement" name="agreement">
                        <label class="form-check-label" for="agreement">
                            I agree to the <a href="terms.php" target="_blank">Terms and Conditions</a> and <a
                                href="privacy.php" target="_blank">Privacy Policy</a>.
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
            </div>
        </div>
    </div>
</body>
<script>
document.querySelector('input[name="password"]').addEventListener('input', function () {
    const password = this.value;

    const length = document.getElementById('length');
    const uppercase = document.getElementById('uppercase');
    const lowercase = document.getElementById('lowercase');
    const number = document.getElementById('number');
    const special = document.getElementById('special');

    // Apply validation for each rule
    length.className = password.length > 8 ? 'valid' : 'invalid';
    uppercase.className = /[A-Z]/.test(password) ? 'valid' : 'invalid';
    lowercase.className = /[a-z]/.test(password) ? 'valid' : 'invalid';
    number.className = /\d/.test(password) ? 'valid' : 'invalid';
    special.className = /[!@#$%^&*]/.test(password) ? 'valid' : 'invalid';
});
</script>
</html>