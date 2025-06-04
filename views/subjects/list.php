<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../../index.php");
    exit();
}
require_once "../../models/Database.php";

$pdo = Database::connect();
$stmt = $pdo->query("SELECT * FROM subjects ORDER BY subject_name ASC");
$subjects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Priekšmeti</title>
    <link rel="stylesheet" href="../../css/subjects.css">
</head>

<body>
    <h2>Priekšmetu saraksts</h2>
    <a href="../../dashboard.php">Atpakaļ</a> |
    <a href="create.php">Pievienot jaunu priekšmetu</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nosaukums</th>
                <th>Darbības</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?= $subject['id'] ?></td>
                    <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $subject['id'] ?>">Rediģēt</a> |
                        <a href="delete.php?id=<?= $subject['id'] ?>" onclick="return confirm('Vai tiešām dzēst?')">Dzēst</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

