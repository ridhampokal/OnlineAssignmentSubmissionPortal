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

<h2>Teacher Dashboard (<?= htmlspecialchars($user['name']) ?>)</h2>

<div style="margin: 15px 0;">
    <a href="/assignment_portal/public/create_assignment.php">
        <button style="padding: 8px 12px; font-size: 14px;">Create New Assignment</button>
    </a>
</div>

<h3>Your Assignments</h3>

<?php if (!empty($assignments)): ?>
<table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse: collapse;">
    <thead style="background-color: #f2f2f2;">
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
                <a href="/assignment_portal/public/view_submissions.php?assignment_id=<?= $a['id'] ?>">
                    View Submissions
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>No assignments created yet. <a href="/assignment_portal/public/create_assignment.php">Create one now</a>.</p>
<?php endif; ?>

