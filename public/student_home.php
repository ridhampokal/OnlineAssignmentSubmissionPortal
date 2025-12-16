<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
$user = current_user();

if ($user['role'] !== 'student') {
    header('Location: /assignment_portal/public/teacher_home.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$stmt = $pdo->query("SELECT id, name, email FROM users WHERE role='teacher' ORDER BY name");
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5">
    <div class="text-center mb-4">
        <h2 class="text-primary">Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
        <p class="text-muted">Here are all the teachers you can view:</p>
    </div>

    <?php if (empty($teachers)): ?>
        <div class="alert alert-info text-center rounded-4">No teachers found.</div>
    <?php else: ?>
        <div class="table-responsive shadow-lg rounded-4 overflow-hidden">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
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
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="/assignment_portal/public/dashboard_student.php" 
           class="btn btn-gradient text-white rounded-pill px-4 py-2" 
           style="background: linear-gradient(90deg,#4b6cb7,#182848);">
           Go to My Dashboard
        </a>
    </div>
</div>
