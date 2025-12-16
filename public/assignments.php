<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$user = current_user();
if ($user['role'] !== 'student') {
    header('Location: /assignment_portal/public/teacher_home.php');
    exit;
}

// Fetch all assignments with teacher name
$stmt = $pdo->prepare('
    SELECT a.*, u.name AS teacher_name 
    FROM assignments a 
    JOIN users u ON a.teacher_id = u.id 
    ORDER BY a.deadline
');
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch student submissions
$stmt = $pdo->prepare('SELECT assignment_id, file_path FROM submissions WHERE student_id = ?');
$stmt->execute([$user['id']]);
$subs = $stmt->fetchAll(PDO::FETCH_ASSOC);
$subs_index = [];
foreach ($subs as $s) {
    $subs_index[$s['assignment_id']] = $s;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5">
    <div class="text-center mb-4">
        <h2 class="text-primary">Assignments</h2>
        <p class="text-muted">Submit your work before the deadline.</p>
    </div>

    <?php if (empty($assignments)): ?>
        <div class="alert alert-info text-center rounded-4">No assignments available at the moment.</div>
    <?php else: ?>
        <div class="table-responsive shadow-lg rounded-4 overflow-hidden">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Teacher</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>File</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $index => $a): 
                        $submitted = $subs_index[$a['id']] ?? null;
                        $status = $submitted ? 'Submitted' : 'Pending';
                        $isPast = strtotime($a['deadline']) < time();
                    ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($a['title']) ?></td>
                            <td><?= htmlspecialchars($a['teacher_name']) ?></td>
                            <td><?= htmlspecialchars($a['deadline']) ?></td>
                            <td class="<?= $isPast && !$submitted ? 'text-danger fw-bold' : ($submitted ? 'text-success fw-bold' : '') ?>">
                                <?= $status ?>
                            </td>
                            <td>
                                <?php if ($submitted && $submitted['file_path']): ?>
                                    <a href="/assignment_portal/<?= htmlspecialchars($submitted['file_path']) ?>" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary rounded-pill">
                                       Download
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$submitted && !$isPast): ?>
                                    <a href="/assignment_portal/public/submit_assignment.php?assignment_id=<?= $a['id'] ?>" 
                                       class="btn btn-sm btn-gradient text-white rounded-pill" 
                                       style="background: linear-gradient(90deg,#4b6cb7,#182848);">
                                       Submit
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
