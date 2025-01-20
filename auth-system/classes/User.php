<?php 
class User {
    private $conn;

    public function __construct($db) 
    {
        $this->conn = $db;
    }

    public function register($username, $email, $password) {
        $stmt = $this->conn->prepare('SELECT count(*) FROM users WHERE email = :email');
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $emailExists = $stmt->fetchColumn();
        
        if ($emailExists) {
            return json_encode(["error" => "Email already exists in the system."]);
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $success = $stmt->execute();

        if ($success) {
            return json_encode(["success" => "User registered successfully."]);
        } else {
            return json_encode(["error" => "Failed to register user."]);
        }
    } 
}
