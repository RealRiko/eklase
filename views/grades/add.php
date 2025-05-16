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

// Iegūstam skolēnus un priekšmetus izvēlnēm
$students = $pdo->query("SELECT id, first_name, last_name FROM students ORDER BY first_name, last_name")->fetchAll();
$subjects = $pdo->query("SELECT id, subject_name FROM subjects ORDER BY subject_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? '';
    $subject_id = $_POST['subject_id'] ?? '';
    $grade = $_POST['grade'] ?? '';

    // Validācija
    if (!$student_id || !$subject_id || !$grade) {
        $error = "Visi lauki ir obligāti!";
    } elseif (!is_numeric($grade) || $grade < 1 || $grade > 10) {
        $error = "Atzīmei jābūt skaitlim no 1 līdz 10.";
    } else {
        // Ievieto datubāzē
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
<html>
<head>
    <title>Pievienot atzīmi</title>
    <link rel="stylesheet" href="../../styles.css">
</head>
<body>
<h2>Pievienot jaunu atzīmi</h2>
<a href="list.php">Atpakaļ uz atzīmēm</a><br><br>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label>Skolēns:</label><br>
    <select name="student_id" required>
        <option value="">Izvēlies skolēnu</option>
        <?php foreach ($students as $student): ?>
            <option value="<?= $student['id'] ?>" <?= (isset($_POST['student_id']) && $_POST['student_id'] == $student['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Priekšmets:</label><br>
    <select name="subject_id" required>
        <option value="">Izvēlies priekšmetu</option>
        <?php foreach ($subjects as $subject): ?>
            <option value="<?= $subject['id'] ?>" <?= (isset($_POST['subject_id']) && $_POST['subject_id'] == $subject['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($subject['subject_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Atzīme (1-10):</label><br>
    <input type="number" name="grade" min="1" max="10" value="<?= htmlspecialchars($_POST['grade'] ?? '') ?>" required><br><br>

    <button type="submit">Pievienot</button>
</form>
</body>
</html>
