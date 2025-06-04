<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../models/Database.php";

$error = '';
$success = '';
$user = null;

$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['avatar']['tmp_name'];
        $fileName = $_FILES['avatar']['name'];
        $fileSize = $_FILES['avatar']['size'];
        $fileType = $_FILES['avatar']['type'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($fileType, $allowedTypes)) {
            $error = "Atļauti tikai JPG, PNG un GIF attēli.";
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $error = "Fails ir pārāk liels. Maksimālais izmērs: 2MB.";
        } else {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destPath = $uploadDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $avatarPath = 'uploads/' . $newFileName;

                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                if ($stmt->execute([$avatarPath, $_SESSION['user_id']])) {
                    $success = "Profila bilde veiksmīgi augšupielādēta.";
                } else {
                    $error = "Kļūda saglabājot avataru datubāzē.";
                }
            } else {
                $error = "Kļūda pārvietojot failu.";
            }
        }
    } else {
        $error = "Lūdzu izvēlies attēlu augšupielādei.";
    }
}

// Get current user avatar
$stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="lv">
<head>

    <meta charset="UTF-8">
    <title>Augšupielādēt avataru</title>
<link rel="stylesheet" href="../css/upload.css">

</head>
<body>
    <div class="upload-container">
        <h2>Augšupielādēt profila bildi</h2>
        <a class="back-link" href="../dashboard.php">← Atpakaļ uz galveno</a>

        <?php if ($error): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success-message"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if ($user && $user['avatar']): ?>
            <div class="avatar-preview">
                <p>Esošā profila bilde:</p>
                <img src="../<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar">
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="upload-form">
            <label for="avatar">Izvēlies attēlu (JPG, PNG, GIF, max 2MB):</label>
            <input type="file" name="avatar" id="avatar" accept="image/*" required>
            <button type="submit">Augšupielādēt</button>
        </form>
    </div>
</body>
</html>

