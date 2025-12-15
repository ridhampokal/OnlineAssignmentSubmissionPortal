<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
$user = current_user();
if ($user['role'] !== 'teacher') { header('Location: /assignment_portal/public/index.php'); exit; }

require_once __DIR__ . '/../includes/db.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    
    if (!$title) $errors[] = 'Title required';
    if (!$deadline) $errors[] = 'Deadline required';
    
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO assignments (title, description, deadline, teacher_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([$title, $description, $deadline, $user['id']]);
        header('Location: /assignment_portal/public/dashboard_teacher.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';

?>
<h3>Create Assignment</h3>
<?php if ($errors) foreach ($errors as $e) echo '<p>' . htmlspecialchars($e) . '</p>'; ?>
<form method="post">
  <label>Title <input name="title" required></label>
  <label>Description <textarea name="description"></textarea></label>
  <label>Deadline <input name="deadline" type="datetime-local" required></label>
  <button type="submit">Create</button>
</form>
