<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$user = current_user();
if ($user['role'] !== 'teacher') {
    header('Location: /assignment_portal/public/index.php');
    exit;
}

$assignment_id = (int)($_GET['assignment_id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM assignments WHERE id = ? AND teacher_id = ?');
$stmt->execute([$assignment_id, $user['id']]);
$assignment = $stmt->fetch();
if (!$assignment) {
    echo "Assignment not found or you are not allowed to view it.";
    exit;
}

$stmt = $pdo->prepare('
    SELECT s.id as submission_id, s.file_path, s.submission_date, u.name as student_name, g.marks, g.feedback
    FROM submissions s
    JOIN users u ON s.student_id = u.id
    LEFT JOIN grades g ON g.submission_id = s.id
    WHERE s.assignment_id = ?
    ORDER BY s.submission_date ASC
');
$stmt->execute([$assignment_id]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<h3>Submissions for: <?= htmlspecialchars($assignment['title']) ?></h3>
<?php if (!$submissions): ?>
    <p>No submissions yet.</p>
<?php else: ?>
<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>Student</th>
            <th>File</th>
            <th>Submitted At</th>
            <th>Marks</th>
            <th>Feedback</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($submissions as $sub): ?>
        <tr>
            <td><?= htmlspecialchars($sub['student_name']) ?></td>
            <td>
                <?php if ($sub['file_path']): ?>
                    <a href="/assignment_portal/<?= htmlspecialchars($sub['file_path']) ?>" target="_blank">Download</a>
                <?php else: ?>
                    No file
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($sub['submission_date']) ?></td>
            <td><?= $sub['marks'] !== null ? htmlspecialchars($sub['marks']) : 'Not graded' ?></td>
            <td><?= !empty($sub['feedback']) ? htmlspecialchars($sub['feedback']) : '-' ?></td>
            <td><a href="/assignment_portal/public/grade_submission.php?submission_id=<?= $sub['submission_id'] ?>">Grade Now</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>


