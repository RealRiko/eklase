<?php
require_once "models/Database.php";

class AuthController {
    public static function login($username, $password) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: index.php?error=1");
            exit();
        }
    }

    public static function logout() {
        session_destroy();
        header("Location: index.php");
    }
}
