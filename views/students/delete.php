<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../../index.php");
    exit();
}
require_once "../../models/Database.php";

$pdo = Database::connect();
$id = $_GET['id'] ?? null;

if ($id) {

    $stmt = $pdo->prepare("SELECT user_id FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$student['user_id']]);
    }
}

header("Location: list.php");
exit();
