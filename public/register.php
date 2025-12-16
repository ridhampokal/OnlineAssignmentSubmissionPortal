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

<div class="container" style="padding-top: 80px; padding-bottom: 80px; max-width: 450px;">
    <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body p-4">
            <h3 class="text-center mb-4 fw-bold text-primary">Create Account</h3>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <input type="text" name="name" class="form-control form-control-lg" placeholder="Name" required>
                </div>

                <div class="mb-3">
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" required>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required>
                </div>

                <div class="mb-3">
                    <select name="role" class="form-select form-select-lg">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>

                <button type="submit" class="btn w-100 py-2 text-white rounded-pill" 
                        style="background: linear-gradient(90deg,#4b6cb7,#182848); font-weight: 500;">
                    Register
                </button>
            </form>

            <p class="text-center mt-3">
                Already have an account? <a href="index.php">Login</a>
            </p>
        </div>
    </div>
</div>
