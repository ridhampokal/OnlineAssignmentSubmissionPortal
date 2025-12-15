<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
$user = current_user();
$assignment_id = (int)($_GET['assignment_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM assignments WHERE id = ?');
$stmt->execute([$assignment_id]);
$assignment = $stmt->fetch();
if (!$assignment) { echo 'Assignment not found'; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error';
    } else {
        $file = $_FILES['submission_file'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['pdf','doc','docx','zip','txt'];
        if (!in_array(strtolower($ext), $allowed)) $errors[] = 'Invalid file type';
        if ($file['size'] > 10 * 1024 * 1024) $errors[] = 'File too large';

        if (empty($errors)) {
            $safeName = uniqid('sub_') . '.' . $ext;
            $destRelative = 'assets/uploads/' . $safeName;
            $dest = __DIR__ . '/../' . $destRelative;
            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                $errors[] = 'Failed to move uploaded file';
            } else {
                $stmt = $pdo->prepare('INSERT INTO submissions (assignment_id, student_id, file_path) VALUES (?, ?, ?)');
                $stmt->execute([$assignment_id, $user['id'], $destRelative]);
                header('Location: /assignment_portal/public/dashboard_student.php');
                exit;
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<h3>Submit for: <?php echo htmlspecialchars($assignment['title']); ?></h3>
<?php if ($errors) foreach ($errors as $e) echo '<p>' . htmlspecialchars($e) . '</p>'; ?>
<form method="post" enctype="multipart/form-data">
  <label>Choose file <input name="submission_file" type="file" required></label>
  <button type="submit">Upload</button>
</form>

