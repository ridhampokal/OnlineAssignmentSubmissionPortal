<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
$user = current_user();
if ($user['role'] !== 'teacher') { header('Location: /assignment_portal/public/index.php'); exit; }

$submission_id = (int)($_GET['submission_id'] ?? 0);
$stmt = $pdo->prepare('SELECT s.*, a.title, u.name as student_name 
                       FROM submissions s 
                       JOIN assignments a ON s.assignment_id=a.id 
                       JOIN users u ON s.student_id=u.id 
                       WHERE s.id = ? AND a.teacher_id = ?');
$stmt->execute([$submission_id, $user['id']]);
$sub = $stmt->fetch();
if (!$sub) { echo 'Not found or not allowed'; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marks = intval($_POST['marks'] ?? 0);
    $feedback = trim($_POST['feedback'] ?? '');

    $stmt = $pdo->prepare('SELECT id FROM grades WHERE submission_id = ?');
    $stmt->execute([$submission_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $pdo->prepare('UPDATE grades SET marks = ?, feedback = ? WHERE submission_id = ?');
        $stmt->execute([$marks, $feedback, $submission_id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO grades (submission_id, marks, feedback) VALUES (?, ?, ?)');
        $stmt->execute([$submission_id, $marks, $feedback]);
    }

    header('Location: /assignment_portal/public/view_submissions.php?assignment_id=' . $sub['assignment_id']);
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>
<h3>Grade: <?= htmlspecialchars($sub['title']) ?> — <?= htmlspecialchars($sub['student_name']) ?></h3>
<form method="post">
  <label>Marks <input name="marks" type="number" min="0" required></label><br>
  <label>Feedback <textarea name="feedback" required></textarea></label><br>
  <button type="submit">Save</button>
</form>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
