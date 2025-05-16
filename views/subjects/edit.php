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

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: list.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
$stmt->execute([$id]);
$subject = $stmt->fetch();

if (!$subject) {
    $error = "Priekšmets nav atrasts.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectName = trim($_POST['subject_name'] ?? '');
    if (!$subjectName) {
        $error = "Nosaukums ir obligāts!";
    } else {
        $stmt = $pdo->prepare("UPDATE subjects SET subject_name = ? WHERE id = ?");
        $stmt->execute([$subjectName, $id]);
        $success = "Priekšmets atjaunināts!";
        $subject['subject_name'] = $subjectName;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Rediģēt priekšmetu</title></head>
<body>
<h2>Rediģēt priekšmetu</h2>
<a href="list.php">Atpakaļ</a><br><br>

<?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
<?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>

<form method="POST">
    <label>Nosaukums:</label><br>
    <input type="text" name="subject_name" value="<?= htmlspecialchars($subject['subject_name']) ?>" required><br><br>
    <button type="submit">Saglabāt</button>
</form>
</body>
</html>
