<?php
class UserModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Check if email exists in the users table
    public function emailExists($email)
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Fetch user by email (for login)
    public function getUserByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Register a new user
    public function registerJobSeeker($user_type, $fullname, $email, $password, $disability)
    {
        if ($this->emailExists($email)) {
            return "Email already exists in the user database.";
        }

        if (!$this->isValidEmail($email)) {
            return "Invalid email address. Please use a real email.";
        }

        if (!$this->isValidPassword($password)) {
            return "Password must be at least 8 characters long, include uppercase, lowercase, and a special character.";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (user_type, fullname, email, password, disability) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$user_type, $fullname, $email, $hashedPassword, $disability]);
    }

    public function registerClient($user_type, $fullname, $email, $password)
    {
        if ($this->emailExists($email)) {
            return "Email already exists in the user database.";
        }

        if (!$this->isValidEmail($email)) {
            return "Invalid email address. Please use a real email.";
        }

        if (!$this->isValidPassword($password)) {
            return "Password must be at least 8 characters long, include uppercase, lowercase, and a special character.";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (user_type, fullname, email, password) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$user_type, $fullname, $email, $hashedPassword]);
    }

    // Validate email using PHP's filter_var
    private function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // Password validation for strength
    private function isValidPassword($password)
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/', $password);
    }
    public function updateProfile($user_id, $fullname, $email) {
        try {
            $stmt = $this->conn->prepare("UPDATE users SET fullname = ?, email = ? WHERE id = ?");
            return $stmt->execute([$fullname, $email, $user_id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
}
?>