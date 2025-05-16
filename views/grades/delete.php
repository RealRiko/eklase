<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../../index.php");
    exit();
}

require_once "../../models/Database.php";
$pdo = Database::connect();

$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM grades WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: list.php");
exit();
