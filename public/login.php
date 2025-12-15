<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, password FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    if ($row && password_verify($password, $row['password'])) {
        login_user_by_id($row['id']);
        header('Location: /assignment_portal/public/index.php');
        exit;
    } else {
        $errors[] = 'Invalid credentials';
    }
}
require_once __DIR__ . '/../includes/header.php';
?>
<h3>Login</h3>
<?php if ($errors) foreach ($errors as $e) echo '<p>' . htmlspecialchars($e) . '</p>'; ?>
<form method="post">
  <label>Email <input name="email" type="email" required></label>
  <label>Password <input name="password" type="password" required></label>
  <button type="submit">Login</button>
</form>
