<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($role) ?></h2>
    <a href="logout.php">Logout</a>
    <ul>
        <?php if ($role === 'teacher'): ?>
            <li><a href="views/students/list.php">Manage Students</a></li>
            <li><a href="register.php">Add Users</a></li>
            <li><a href="views/subjects/list.php">Manage Subjects</a></li>
            <li><a href="views/grades/list.php">Manage Grades</a></li>
            <li><a href="views/upload_avatar.php">Upload Avatar</a></li>
        <?php else: ?>
            <li><a href="views/grades/list.php">View My Grades</a></li>
            <li><a href="views/upload_avatar.php">Upload Avatar</a></li>
        <?php endif; ?>
    </ul>
</body>
</html>
