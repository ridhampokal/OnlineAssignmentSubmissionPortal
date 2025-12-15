<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
$user = current_user();

if ($user['role'] !== 'teacher') {
    header('Location: /assignment_portal/public/student_home.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$stmt = $pdo->query("SELECT id, name, email FROM users WHERE role='student' ORDER BY name");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<h2>Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
<p>Here are all the students:</p>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $index => $s): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($students)): ?>
        <tr><td colspan="3" style="text-align:center;">No students found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<p>
    <a href="/assignment_portal/public/dashboard_teacher.php">
        <button>Go to My Dashboard</button>
    </a>
</p>

