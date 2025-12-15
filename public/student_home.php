<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
$user = current_user();

if ($user['role'] !== 'student') {
    header('Location: /assignment_portal/public/teacher_home.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$stmt = $pdo->query("SELECT id, name, email, course FROM users WHERE role='teacher' ORDER BY name");
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<h2>Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
<p>Here are all the teachers:</p>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($teachers as $index => $t): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($t['name']) ?></td>
            <td><?= htmlspecialchars($t['email']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($teachers)): ?>
        <tr><td colspan="4" style="text-align:center;">No teachers found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<p>
    <a href="/assignment_portal/public/dashboard_student.php">
        <button>Go to My Dashboard</button>
    </a>
</p>

