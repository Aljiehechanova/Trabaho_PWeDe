<?php
class UserModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // --------------------------
    // Validation Methods
    // --------------------------

    private function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function isValidPassword($password)
    {
        // At least 8 characters, with uppercase, lowercase, number, and special character
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{8,}$/', $password);
    }

    // --------------------------
    // Email Check
    // --------------------------

    public function emailExists($email)
    {
        $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // --------------------------
    // Authentication & Retrieval
    // --------------------------

    public function getUserByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --------------------------
    // Registration
    // --------------------------

    public function registerJobSeeker($user_type, $fullname, $email, $password, $disability)
    {
        if ($this->emailExists($email)) {
            return "Email already exists.";
        }

        if (!$this->isValidEmail($email)) {
            return "Invalid email address.";
        }

        if (!$this->isValidPassword($password)) {
            return "Password must be at least 8 characters, include uppercase, lowercase, number, and a special character.";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("
            INSERT INTO users (user_type, fullname, email, password, disability_type)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$user_type, $fullname, $email, $hashedPassword, $disability]);
    }

    public function registerClient($user_type, $fullname, $email, $password)
    {
        if ($this->emailExists($email)) {
            return "Email already exists.";
        }

        if (!$this->isValidEmail($email)) {
            return "Invalid email address.";
        }

        if (!$this->isValidPassword($password)) {
            return "Password must be at least 8 characters, include uppercase, lowercase, number, and a special character.";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("
            INSERT INTO users (user_type, fullname, email, password)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$user_type, $fullname, $email, $hashedPassword]);
    }

    // --------------------------
    // Profile Update
    // --------------------------

    public function updateUser($id, $fullname, $email, $description, $location, $disability)
    {
        $sql = "UPDATE users 
                SET fullname = :fullname, email = :email, description = :description, 
                    location = :location, disability = :disability_type 
                WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':fullname' => $fullname,
            ':email' => $email,
            ':description' => $description,
            ':location' => $location,
            ':disability_type' => $disability,
            ':user_id' => $id
        ]);
    }
}
?>
