<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../../index.php");
    exit();
}

require_once "../../models/Database.php";
$pdo = Database::connect();

$error = '';
$success = '';

$students = $pdo->query("SELECT id, first_name, last_name FROM students ORDER BY first_name, last_name")->fetchAll();
$subjects = $pdo->query("SELECT id, subject_name FROM subjects ORDER BY subject_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? '';
    $subject_id = $_POST['subject_id'] ?? '';
    $grade = $_POST['grade'] ?? '';

    if (!$student_id || !$subject_id || !$grade) {
        $error = "Visi lauki ir obligāti!";
    } elseif (!is_numeric($grade) || $grade < 1 || $grade > 10) {
        $error = "Atzīmei jābūt skaitlim no 1 līdz 10.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, grade, grade_date) VALUES (?, ?, ?, NOW())");
        if ($stmt->execute([$student_id, $subject_id, $grade])) {
            $success = "Atzīme veiksmīgi pievienota.";
        } else {
            $error = "Kļūda pievienojot atzīmi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pievienot atzīmi</title>
    <!-- Use absolute path to avoid path issues -->
    <link rel="stylesheet" href="/eklase/css/grade.css">
</head>
<body>
<div class="container">
    <h2>Pievienot jaunu atzīmi</h2>
    <a href="list.php">⬅️ Atpakaļ uz atzīmēm</a>

    <?php if ($error): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success-message"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Skolēns:</label>
        <select name="student_id" required>
            <option value="">Izvēlies skolēnu</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= $student['id'] ?>" <?= (isset($_POST['student_id']) && $_POST['student_id'] == $student['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Priekšmets:</label>
        <select name="subject_id" required>
            <option value="">Izvēlies priekšmetu</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?= $subject['id'] ?>" <?= (isset($_POST['subject_id']) && $_POST['subject_id'] == $subject['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($subject['subject_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Atzīme (1-10):</label>
        <input type="number" name="grade" min="1" max="10" value="<?= htmlspecialchars($_POST['grade'] ?? '') ?>" required>

        <button type="submit">Pievienot</button>
    </form>
</div>
</body>
</html>
