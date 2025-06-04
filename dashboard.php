<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'] ?? '';

// Optionally, redirect if role is not set
if (!$role) {
    echo "Lietotāja loma nav definēta.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Laipni lūdzam, <?= htmlspecialchars($role ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
        <a class="logout-button" href="logout.php">Izrakstīties</a>
        <ul class="dashboard-menu">
            <?php if ($role === 'teacher'): ?>
                <li><a href="views/students/list.php">Pārvaldīt skolēnus</a></li>
                <li><a href="register.php">Pievienot lietotājus</a></li>
                <li><a href="views/subjects/list.php">Pārvaldīt priekšmetus</a></li>
                <li><a href="views/grades/list.php">Pārvaldīt atzīmes</a></li>
                <li><a href="views/upload_avatar.php">Augšupielādēt avataru</a></li>
            <?php else: ?>
                <li><a href="views/grades/list.php">Skatīt manas atzīmes</a></li>
                <li><a href="views/upload_avatar.php">Augšupielādēt avataru</a></li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
