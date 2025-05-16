<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../../index.php");
    exit();
}
require_once "../../models/Database.php";

$pdo = Database::connect();
$error = '';
$success = '';

$students = $pdo->query("SELECT id, first_name, last_name FROM students")->fetchAll();
$subjects = $pdo->query("SELECT id, subject_name FROM subjects")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'] ?? '';
    $subjectId = $_POST['subject_id'] ?? '';
    $grade = $_POST['grade'] ?? '';

    if (!$studentId || !$subjectId || !$grade) {
        $error = "Visi lauki ir obligāti!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, grade, date) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$studentId, $subjectId, $grade]);
        $success = "Atzīme pievienota!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Pievienot atzīmi</title></head>
<body>
<h2>Pievienot jaunu atzīmi</h2>
<a href="list.php">Atpakaļ</a><br><br>

<?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
<?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>

<form method="POST">
    <label>Skolēns:</label><br>
    <select name="student_id" required>
        <?php foreach ($students as $s): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['first_name'] . " " . $s['last_name']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Priekšmets:</label><br>
    <select name="subject_id" required>
        <?php foreach ($subjects as $sub): ?>
            <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['subject_name']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Atzīme (1–10):</label><br>
    <input type="number" name="grade" min="1" max="10" required><br><br>

    <button type="submit">Saglabāt</button>
</form>
</body>
</html>
