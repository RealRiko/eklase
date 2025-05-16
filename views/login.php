<!DOCTYPE html>
<html>
<head>
    <title>Pieslēgties</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Pieslēgšanās</h2>
    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;">Nepareizs lietotājvārds vai parole!</p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>Lietotājvārds:</label>
        <input type="text" name="username" required><br>

        <label>Parole:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="Ieiet">


    </form>
</body>
</html>

