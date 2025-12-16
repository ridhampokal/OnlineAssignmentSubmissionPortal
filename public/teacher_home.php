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

<div class="container mt-5">
    <div class="text-center mb-4">
        <h2 class="text-primary">Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
        <p class="text-muted">Here are all the students in your portal:</p>
    </div>

    <?php if (empty($students)): ?>
        <div class="alert alert-info text-center rounded-4">No students found.</div>
    <?php else: ?>
        <div class="table-responsive shadow-lg rounded-4 overflow-hidden">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
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
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="/assignment_portal/public/dashboard_teacher.php" class="btn btn-outline-secondary rounded-pill">
            Go to My Dashboard
        </a>
    </div>
</div>
