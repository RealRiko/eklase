<?php
if (!isset($error)) $error = '';
if (!isset($success)) $success = '';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Lietotāja reģistrācija</title>
    <script>
    function toggleStudentFields() {
        const role = document.querySelector('input[name="role"]:checked').value;
        const studentFields = document.getElementById('student-fields');
        studentFields.style.display = (role === 'student') ? 'block' : 'none';
    }
    </script>
</head>
<body onload="toggleStudentFields()">

    <h2>Jauna lietotāja reģistrācija (skolotājs)</h2>
    <a href="../../dashboard.php">Atpakaļ</a>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">
    <label>Loma:</label><br>
        <label><input type="radio" name="role" value="teacher" onchange="toggleStudentFields()" checked> Skolotājs</label><br>
        <label><input type="radio" name="role" value="student" onchange="toggleStudentFields()"> Skolnieks</label><br><br>
        <label>Lietotājvārds:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Parole:</label><br>
        <input type="password" name="password" required><br><br>

        <div id="student-fields" style="display:none;">
            <label>Vārds:</label><br>
            <input type="text" name="first_name"><br><br>

            <label>Uzvārds:</label><br>
            <input type="text" name="last_name"><br><br>
        </div>

        <button type="submit">Reģistrēt</button>
    </form>

</body>
</html>
