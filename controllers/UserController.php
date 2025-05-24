<?php
include '../config/db_connection.php';
include '../models/UserModel.php';

class UserController
{
    private $userModel;

    public function __construct($db)
    {
        $this->userModel = new UserModel($db);
    }

    // Login Function
    public function login($email, $password)
    {
        $user = $this->userModel->getUserByEmail($email);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                return $user; // Return user data if login is successful
            } else {
                return null; // Incorrect password
            }
        }

        return null; // User not found
    }
    // Registration Function
    public function registerJobSeeker($user_type, $fullname, $email, $password, $disability)
    {
        // Validate Email Format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }

        // Check Email Domain
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain, "MX")) {
            return "The email domain does not exist.";
        }

        // Check for Duplicate Email
        if ($this->userModel->getUserByEmail($email)) {
            return "This email is already registered.";
        }

        // Password Validation (Improved Regex)
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            return "Password must be at least 8 characters long, include 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.";
        }

        // Register User
        return $this->userModel->registerJobSeeker($user_type, $fullname, $email, $password, $disability)
            ? true
            : "Failed to register. Please try again.";
    }

    public function registerClient($user_type, $fullname, $email, $password)
    {
        // Validate Email Format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }

        // Check Email Domain
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain, "MX")) {
            return "The email domain does not exist.";
        }

        // Check for Duplicate Email
        if ($this->userModel->getUserByEmail($email)) {
            return "This email is already registered.";
        }

        // Password Validation (Improved Regex)
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            return "Password must be at least 8 characters long, include 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.";
        }

        // Register User
        return $this->userModel->registerClient($user_type, $fullname, $email, $password)
            ? true
            : "Failed to register. Please try again.";
    }

    public function emailExists($email)
    {
        return $this->userModel->getUserByEmail($email) ? true : false;
    }
    public function updateProfile($user_id, $fullname, $email, $description, $location, $disability) {
        return $this->userModel->updateUser($user_id, $fullname, $email, $description, $location, $disability);
    }
    
    
    
}

?>