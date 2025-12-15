<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
$user = current_user();

$stmt = $pdo->prepare('SELECT a.*, u.name AS teacher_name 
                       FROM assignments a 
                       JOIN users u ON a.teacher_id = u.id 
                       ORDER BY a.deadline');
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('
    SELECT 
        s.id AS submission_id,
        s.assignment_id,
        s.file_path,
        s.submission_date,
        g.marks,
        g.feedback
    FROM submissions s
    LEFT JOIN grades g ON g.submission_id = s.id
    WHERE s.student_id = ?
');
$stmt->execute([$user['id']]);
$subs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$subs_index = [];
foreach ($subs as $s) {
    $subs_index[$s['assignment_id']] = $s;
}

require_once __DIR__ . '/../includes/header.php';
?>

<h3>Student Dashboard (<?= htmlspecialchars($user['name']) ?>)</h3>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Assignment</th>
            <th>Teacher</th>
            <th>Deadline</th>
            <th>Submission Status</th>
            <th>File</th>
            <th>Marks</th>
            <th>Feedback</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($assignments as $a): 
        $submitted = $subs_index[$a['id']] ?? null;
        $status = $submitted ? 'Submitted' : 'Pending';
    ?>
        <tr>
            <td><?= htmlspecialchars($a['title']) ?></td>
            <td><?= htmlspecialchars($a['teacher_name']) ?></td>
            <td><?= htmlspecialchars($a['deadline']) ?></td>
            <td><?= $status ?></td>
            <td>
                <?php if ($submitted && $submitted['file_path']): ?>
                    <a href="/assignment_portal/<?= htmlspecialchars($submitted['file_path']) ?>" target="_blank">Download</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td><?= $submitted && $submitted['marks'] !== null ? htmlspecialchars($submitted['marks']) : '-' ?></td>
            <td><?= $submitted && !empty($submitted['feedback']) ? htmlspecialchars($submitted['feedback']) : '-' ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
