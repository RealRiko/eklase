<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../../index.php");
    exit();
}
require_once "../../models/Database.php";

$id = $_GET['id'] ?? null;
if ($id) {
    $pdo = Database::connect();
    $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: list.php");
exit();
