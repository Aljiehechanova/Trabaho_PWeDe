<?php
session_start();
include '../config/db_connection.php';
include '../controllers/UserController.php';

$userController = new UserController($conn);

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $user = $userController->login($email, $password);

    if ($user && is_array($user)) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_type'] = $user['user_type'];

        if ($user['user_type'] === 'job_seeker') {
            header("Location: ../views/userP.php");
        } elseif ($user['user_type'] === 'client') {
            header("Location: ../views/clientP.php");
        }elseif ($user['user_type'] === 'admin') {
            header("Location: ../views/addash.php");
        }
        exit();
    }

    // Check if the email exists
    if ($userController->emailExists($email)) {
        $error = "Incorrect password. Please try again.";
    } else {
        $error = "Email not found. Please check your email or register.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabaho PWeDe - Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <!-- Go Back Button -->
        <a href="home.php" style="position: absolute; top: 20px; left: 20px; text-decoration: none; background-color: #007bff; color: white; padding: 8px 12px; border-radius: 5px;">← Go Back</a>

        <img src="../assets/images/TrabahoPWeDeLogo.png" alt="Trabaho PWeDe Logo" class="logo">
        <h1>LOGIN TO YOUR ACCOUNT</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <input type="text" name="email" placeholder="Email" required
                    style="width: 95%; max-width: 400px; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin: 5px 0;">
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required
                    style="width: 95%; max-width: 400px; padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin: 5px 0;">
            </div>
            <div class="checkbox-group">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#">&nbsp;Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-primary">Sign in</button>
        </form>
        <p class="signup">Don’t have a Trabaho PWeDe account? <a href="RC.php">Sign up</a></p>
    </div>
</body>


</html>