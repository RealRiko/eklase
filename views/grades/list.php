<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

require_once "../../models/Database.php";
$pdo = Database::connect();

$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];


$studentFilter = '';
$params = [];


if ($role === 'student') {

    $stmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
    $stmt->execute([$userId]);
    $student = $stmt->fetch();
    if (!$student) {
        die("Skolēns nav atrasts.");
    }
    $studentFilter = "WHERE grades.student_id = ?";
    $params[] = $student['id'];
} else {

    $student_id = $_GET['student_id'] ?? '';
    $subject_id = $_GET['subject_id'] ?? '';

    $whereClauses = [];
    if ($student_id) {
        $whereClauses[] = "grades.student_id = ?";
        $params[] = $student_id;
    }
    if ($subject_id) {
        $whereClauses[] = "grades.subject_id = ?";
        $params[] = $subject_id;
    }

    if (count($whereClauses) > 0) {
        $studentFilter = "WHERE " . implode(" AND ", $whereClauses);
    }
}

$sql = "SELECT grades.id, students.first_name, students.last_name, subjects.subject_name, grades.grade, grades.grade_date
        FROM grades
        JOIN students ON grades.student_id = students.id
        JOIN subjects ON grades.subject_id = subjects.id
        $studentFilter
        ORDER BY grades.grade_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$grades = $stmt->fetchAll();

if ($role === 'teacher') {
    $students = $pdo->query("SELECT id, first_name, last_name FROM students ORDER BY first_name, last_name")->fetchAll();
    $subjects = $pdo->query("SELECT id, subject_name FROM subjects ORDER BY subject_name")->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Grades</title>
  <link rel="stylesheet" href="../../css/grades.css">

</head>
<body>
<h2>Atzīmes</h2>
<a href="../../dashboard.php">Atpakaļ</a>

<?php if ($role === 'teacher'): ?>
<form method="GET" action="">
    <label>Filtrēt pēc skolēna:</label>
    <select name="student_id">
        <option value="">-- Visi skolēni --</option>
        <?php foreach ($students as $s): ?>
            <option value="<?= $s['id'] ?>" <?= (isset($_GET['student_id']) && $_GET['student_id'] == $s['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Filtrēt pēc priekšmeta:</label>
    <select name="subject_id">
        <option value="">-- Visi priekšmeti --</option>
        <?php foreach ($subjects as $sub): ?>
            <option value="<?= $sub['id'] ?>" <?= (isset($_GET['subject_id']) && $_GET['subject_id'] == $sub['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($sub['subject_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Filtrēt</button>
</form>
<br>
<a href="add.php">Pievienot jaunu atzīmi</a><br><br>
<?php endif; ?>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th>Skolēns</th>
        <th>Priekšmets</th>
        <th>Atzīme</th>
        <th>Datums</th>
        <?php if ($role === 'teacher'): ?>
            <th>Darbības</th>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?php if (count($grades) === 0): ?>
        <tr><td colspan="<?= $role === 'teacher' ? 5 : 4 ?>">Nav atzīmju.</td></tr>
    <?php else: ?>
        <?php foreach ($grades as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                <td><?= htmlspecialchars($row['subject_name']) ?></td>
                <td><?= htmlspecialchars($row['grade']) ?></td>
                <td><?= htmlspecialchars($row['grade_date']) ?></td>
                <?php if ($role === 'teacher'): ?>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>">Labot</a> |
                        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Vai tiešām dzēst šo atzīmi?');">Dzēst</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>
