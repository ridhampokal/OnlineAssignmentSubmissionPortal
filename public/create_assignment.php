<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
$user = current_user();

if ($user['role'] !== 'teacher') { 
    header('Location: /assignment_portal/public/index.php'); 
    exit; 
}

require_once __DIR__ . '/../includes/db.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    
    if (!$title) $errors[] = 'Title is required';
    if (!$deadline) $errors[] = 'Deadline is required';
    
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO assignments (title, description, deadline, teacher_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([$title, $description, $deadline, $user['id']]);
        header('Location: /assignment_portal/public/dashboard_teacher.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-center mt-5">
    <div class="card shadow-lg p-4 rounded-4" style="width: 500px; background: linear-gradient(145deg,#ffffff,#dfe9f3);">
        <h3 class="text-center text-primary mb-4">Create New Assignment</h3>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input name="title" type="text" class="form-control rounded-pill" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control rounded" rows="4"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Deadline</label>
                <input name="deadline" type="datetime-local" class="form-control rounded-pill" required>
            </div>

            <button type="submit" class="btn w-100 py-2 text-white" style="background: linear-gradient(90deg,#4b6cb7,#182848);">
                Create Assignment
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="/assignment_portal/public/dashboard_teacher.php" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
        </div>
    </div>
</div>
