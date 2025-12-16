<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
$user = current_user();

if ($user['role'] !== 'teacher') {
    header('Location: /assignment_portal/public/student_home.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$submission_id = (int)($_GET['submission_id'] ?? 0);

$stmt = $pdo->prepare('
    SELECT s.*, a.title, u.name as student_name 
    FROM submissions s 
    JOIN assignments a ON s.assignment_id = a.id 
    JOIN users u ON s.student_id = u.id 
    WHERE s.id = ? AND a.teacher_id = ?
');
$stmt->execute([$submission_id, $user['id']]);
$sub = $stmt->fetch();

require_once __DIR__ . '/../includes/header.php';

if (!$sub) {
    echo '<div class="container mt-4"><div class="alert alert-danger">Submission not found or you are not allowed to grade this submission.</div></div>';
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marks = intval($_POST['marks'] ?? 0);
    $feedback = trim($_POST['feedback'] ?? '');

    try {
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

        echo '<div class="container mt-4"><div class="alert alert-success">Grading saved successfully!</div></div>';
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}
?>

<div class="container mt-4">
    <h3 class="mb-4">Grade Assignment: <strong><?= htmlspecialchars($sub['title']) ?></strong></h3>
    <p><strong>Student:</strong> <?= htmlspecialchars($sub['student_name']) ?></p>

    <?php if (!empty($sub['file_path'])): ?>
        <p><a href="/assignment_portal/<?= htmlspecialchars($sub['file_path']) ?>" class="btn btn-outline-primary" target="_blank">Download Submission</a></p>
    <?php else: ?>
        <p>No file submitted.</p>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err) echo '<li>' . htmlspecialchars($err) . '</li>'; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="mt-3">
        <div class="mb-3">
            <label for="marks" class="form-label">Marks</label>
            <input type="number" name="marks" id="marks" class="form-control" min="0" value="<?= htmlspecialchars($sub['marks'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="feedback" class="form-label">Feedback</label>
            <textarea name="feedback" id="feedback" class="form-control" rows="4" required><?= htmlspecialchars($sub['feedback'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Save Grade</button>
        <a href="/assignment_portal/public/view_submissions.php?assignment_id=<?= $sub['assignment_id'] ?>" class="btn btn-secondary ms-2">Back to Submissions</a>
    </form>
</div>
