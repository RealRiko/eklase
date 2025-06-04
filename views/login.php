<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pieslēgties</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Pieslēgšanās</h2>
        <?php if (isset($_GET['error'])): ?>
            <p class="error">Nepareizs lietotājvārds vai parole!</p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="username">Lietotājvārds:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Parole:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Ieiet">
        </form>
    </div>
</body>
</html>
