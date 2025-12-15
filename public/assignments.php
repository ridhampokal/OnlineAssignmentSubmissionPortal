<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query('SELECT a.*, u.name AS teacher_name FROM assignments a JOIN users u ON a.teacher_id = u.id ORDER BY a.deadline');
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Assignments</h2>

<?php if (!empty($assignments)): ?>
<table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse: collapse;">
    <thead style="background-color: #f2f2f2;">
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Teacher</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($assignments as $index => $a): 
        $isPast = strtotime($a['deadline']) < time();
        $status = $isPast ? 'Past Deadline' : 'Open';
    ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($a['title']) ?></td>
            <td><?= htmlspecialchars($a['teacher_name']) ?></td>
            <td><?= htmlspecialchars($a['deadline']) ?></td>
            <td style="<?= $isPast ? 'color:red;font-weight:bold;' : 'color:green;' ?>">
                <?= $status ?>
            </td>
            <td>
                <?php if (!$isPast): ?>
                    <a href="/assignment_portal/public/submit_assignment.php?assignment_id=<?= $a['id'] ?>">Submit</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>No assignments available at the moment.</p>
<?php endif; ?>

