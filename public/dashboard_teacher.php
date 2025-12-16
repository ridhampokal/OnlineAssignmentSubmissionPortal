<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$user = current_user();
if ($user['role'] !== 'teacher') {
    header('Location: /assignment_portal/public/index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM assignments WHERE teacher_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">

    <div class="text-center mb-4">
        <h2 class="text-primary">Teacher Dashboard</h2>
        <h5>Welcome, <?= htmlspecialchars($user['name']) ?>!</h5>
    </div>

    <!-- Action Card -->
    <div class="mb-4 d-flex justify-content-center">
        <a href="/assignment_portal/public/create_assignment.php" class="btn btn-gradient btn-lg text-white" style="background: linear-gradient(90deg,#4b6cb7,#182848);">
            + Create New Assignment
        </a>
    </div>

    <!-- Assignments Table -->
    <?php if (!empty($assignments)): ?>
        <div class="table-responsive shadow-lg rounded-4 overflow-hidden">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Deadline</th>
                        <th>Created At</th>
                        <th>Submissions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $index => $a): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($a['title']) ?></td>
                            <td><?= htmlspecialchars($a['deadline']) ?></td>
                            <td><?= htmlspecialchars($a['created_at']) ?></td>
                            <td>
                                <a href="/assignment_portal/public/view_submissions.php?assignment_id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                    View Submissions
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center rounded-4 mt-3">
            No assignments created yet. <a href="/assignment_portal/public/create_assignment.php" class="fw-bold">Create one now!</a>
        </div>
    <?php endif; ?>

</div>
