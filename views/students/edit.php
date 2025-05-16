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

$stmt = $pdo->prepare("SELECT students.id, students.first_name, students.last_name, users.username, users.id as user_id 
                       FROM students 
                       JOIN users ON students.user_id = users.id 
                       WHERE students.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    $error = "Skolēns netika atrasts.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $newPassword = $_POST['password'] ?? '';

    if (!$firstName || !$lastName || !$username) {
        $error = "Vārds, uzvārds un lietotājvārds ir obligāti!";
    } else {
   
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkStmt->execute([$username, $student['user_id']]);
        if ($checkStmt->fetch()) {
            $error = "Lietotājvārds jau tiek izmantots!";
        } else {

            $pdo->prepare("UPDATE students SET first_name = ?, last_name = ? WHERE id = ?")
                ->execute([$firstName, $lastName, $id]);


            $pdo->prepare("UPDATE users SET username = ? WHERE id = ?")
                ->execute([$username, $student['user_id']]);

  
            if (!empty($newPassword)) {
                $hashedPass = password_hash($newPassword, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
                    ->execute([$hashedPass, $student['user_id']]);
            }

            $success = "Dati veiksmīgi atjaunināti!";
            $student['first_name'] = $firstName;
            $student['last_name'] = $lastName;
            $student['username'] = $username;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Labot skolēnu</title></head>
<body>
<h2>Labot skolēnu</h2>
<a href="list.php">Atpakaļ</a>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Vārds:</label><br>
    <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" required><br><br>

    <label>Uzvārds:</label><br>
    <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" required><br><br>

    <label>Lietotājvārds:</label><br>
    <input type="text" name="username" value="<?= htmlspecialchars($student['username']) ?>" required><br><br>

    <label>Jaunā parole (ja jānomaina):</label><br>
    <input type="password" name="password"><br><br>

    <button type="submit">Saglabāt izmaiņas</button>
</form>
</body>
</html>
