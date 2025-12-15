<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = ($_POST['role'] ?? 'student') === 'teacher' ? 'teacher' : 'student';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 chars';

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        try {
            $stmt->execute([$name, $email, $hash, $role]);
            $id = $pdo->lastInsertId();
            login_user_by_id($id);
            header('Location: /assignment_portal/public/index.php');
            exit;
        } catch (PDOException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                $errors[] = 'Email already registered';
            } else {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
require_once __DIR__ . '/../includes/header.php';
?>
<h3>Register</h3>
<?php if ($errors): ?>
  <ul><?php foreach ($errors as $err) echo '<li>' . htmlspecialchars($err) . '</li>'; ?></ul>
<?php endif; ?>
<form method="post">
  <label>Name <input name="name" required></label>
  <label>Email <input name="email" type="email" required></label>
  <label>Password <input name="password" type="password" required></label>
  <label>Role
    <select name="role">
      <option value="student">Student</option>
      <option value="teacher">Teacher</option>
    </select>
  </label>
  <button type="submit">Register</button>
</form>


