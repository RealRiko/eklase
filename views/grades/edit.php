<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../../index.php");
    exit();
}

require_once "../../models/Database.php";
$pdo = Database::connect();

$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: list.php");
    exit();
}

$error = '';
$success = '';

// Atrodam esošo atzīmi
$stmt = $pdo->prepare("SELECT * FROM grades WHERE id = ?");
$stmt->execute([$id]);
$gradeData = $stmt->fetch();

if (!$gradeData) {
    $error = "Atzīme nav atrasta!";
} else {
    $students = $pdo->query("SELECT id, first_name, last_name FROM students")->fetchAll();
    $subjects = $pdo->query("SELECT id, subject_name FROM subjects")->fetchAll();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $studentId = $_POST['student_id'] ?? '';
        $subjectId = $_POST['subject_id'] ?? '';
        $grade = $_POST['grade'] ?? '';

        if (!$studentId || !$subjectId || !$grade) {
            $error = "Visi lauki ir obligāti!";
        } else {
            $stmt = $pdo->prepare("UPDATE grades SET student_id = ?, subject_id = ?, grade = ? WHERE id = ?");
            $stmt->execute([$studentId, $subjectId, $grade, $id]);
            $success = "Atzīme veiksmīgi atjaunināta!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Rediģēt atzīmi</title></head>
<body>
<h2>Rediģēt atzīmi</h2>
<a href="list.php">Atpakaļ</a><br><br>

<?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<?php if ($success): ?><p style="color:green;"><?= htmlspecialchars($success) ?></p><?php endif; ?>

<?php if ($gradeData): ?>
<form method="POST">
    <label>Skolēns:</label><br>
    <select name="student_id" required>
        <?php foreach ($students as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $s['id'] == $gradeData['student_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Priekšmets:</label><br>
    <select name="subject_id" required>
        <?php foreach ($subjects as $sub): ?>
            <option value="<?= $sub['id'] ?>" <?= $sub['id'] == $gradeData['subject_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($sub['subject_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Atzīme:</label><br>
    <input type="number" name="grade" min="1" max="10" value="<?= $gradeData['grade'] ?>" required><br><br>

    <button type="submit">Saglabāt izmaiņas</button>
</form>
<?php endif; ?>
</body>
</html>
