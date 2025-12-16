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

<div class="container" style="padding-top: 80px; padding-bottom: 80px; max-width: 450px;">
    <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body p-4">
            <h3 class="text-center mb-4 fw-bold text-primary">Welcome Back</h3>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" required>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required>
                </div>

                <button type="submit" class="btn w-100 py-2 text-white rounded-pill" 
                        style="background: linear-gradient(90deg,#4b6cb7,#182848); font-weight: 500;">
                    Login
                </button>
            </form>

            <p class="text-center mt-3">
                Don't have an account? <a href="register.php">Register</a>
            </p>
        </div>
    </div>
</div>
