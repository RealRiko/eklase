<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../../index.php");
    exit();
}
require_once "../../models/Database.php";

$pdo = Database::connect();
$students = $pdo->query("SELECT students.id, first_name, last_name, username FROM students 
                         JOIN users ON students.user_id = users.id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head><title>Skolēnu saraksts</title></head>
<body>
<h2>Skolēnu saraksts</h2>
<a href="../../dashboard.php">Atpakaļ</a>
<table border="1">
    <tr><th>Vārds</th><th>Uzvārds</th><th>Lietotājvārds</th><th>Darbības</th></tr>
    <?php foreach ($students as $student): ?>
        <tr>
            <td><?= htmlspecialchars($student['first_name']) ?></td>
            <td><?= htmlspecialchars($student['last_name']) ?></td>
            <td><?= htmlspecialchars($student['username']) ?></td>
            <td>
                <a href="edit.php?id=<?= $student['id'] ?>">Labot</a> |
                <a href="delete.php?id=<?= $student['id'] ?>" onclick="return confirm('Dzēst skolēnu?')">Dzēst</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
