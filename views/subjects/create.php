<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../../index.php");
    exit();
}
require_once "../../models/Database.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectName = trim($_POST['subject_name'] ?? '');

    if (!$subjectName) {
        $error = "Priekšmeta nosaukums ir obligāts!";
    } else {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
        $stmt->execute([$subjectName]);
        $success = "Priekšmets pievienots!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Pievienot priekšmetu</title></head>
<body>
<h2>Pievienot jaunu priekšmetu</h2>
<a href="list.php">Atpakaļ</a><br><br>

<?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
<?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>

<form method="POST">
    <label>Nosaukums:</label><br>
    <input type="text" name="subject_name" required><br><br>
    <button type="submit">Saglabāt</button>
</form>
</body>
</html>
