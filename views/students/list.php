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
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Skolēnu saraksts</title>
<link rel="stylesheet" href="../../css/students.css">

</head>
<body>
    <div class="container">
        <h2>Skolēnu saraksts</h2>
        <a class="back-link" href="../../dashboard.php">Atpakaļ</a>
        
        <table>
            <thead>
                <tr>
                    <th>Vārds</th>
                    <th>Uzvārds</th>
                    <th>Lietotājvārds</th>
                    <th>Darbības</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['first_name']) ?></td>
                        <td><?= htmlspecialchars($student['last_name']) ?></td>
                        <td><?= htmlspecialchars($student['username']) ?></td>
                        <td>
                            <a class="edit" href="edit.php?id=<?= $student['id'] ?>">Labot</a> |
                            <a class="delete" href="delete.php?id=<?= $student['id'] ?>" onclick="return confirm('Dzēst skolēnu?')">Dzēst</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
