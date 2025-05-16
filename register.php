<?php
session_start();
require_once "models/Database.php";

// Tikai skolotājs drīkst reģistrēt jaunus lietotājus
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');

    // Validācija
    if (!$username || !$password || !$role) {
        $error = "Lietotājvārds, parole un loma ir obligāti!";
    } elseif ($role === 'student' && (!$firstName || !$lastName)) {
        $error = "Skolniekam jānorāda vārds un uzvārds!";
    } elseif (!in_array($role, ['teacher', 'student'])) {
        $error = "Nederīga loma!";
    } else {
        try {
            $pdo = Database::connect();

            // Pārbauda vai username aizņemts
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if ($stmt->fetch()) {
                $error = "Lietotājvārds jau aizņemts!";
            } else {
                $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashedPass, $role]);

                $userId = $pdo->lastInsertId();

                if ($role === 'student') {
                    $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, user_id) VALUES (?, ?, ?)");
                    $stmt->execute([$firstName, $lastName, $userId]);
                }

                $success = "Lietotājs veiksmīgi reģistrēts!";
            }
        } catch (PDOException $e) {
            $error = "Kļūda datubāzē: " . $e->getMessage();
        }
    }
}

include "views/register_view.php";
