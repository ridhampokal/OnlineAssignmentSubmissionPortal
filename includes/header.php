<?php
require_once __DIR__ . '/auth.php';
$user = current_user();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Assignment Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assignment_portal/public/assets/css/style.css">
  <style>
    body { background: #f0f2f5; }
    header { background: linear-gradient(90deg,#4b6cb7,#182848); padding: 15px 30px; border-radius:0 0 20px 20px; }
    header h2 { color:white; }
    nav a { margin-left: 15px; }
    nav a.btn { font-weight:bold; }
  </style>
</head>
<body>
<header class="d-flex justify-content-between align-items-center">
  <h2>Assignment Portal</h2>
  <nav>
    <?php if ($user): ?>
      <a class="btn btn-outline-light btn-sm" href="/assignment_portal/public/student_home.php">Home</a>
      <?php if ($user['role'] === 'teacher'): ?>
        <a class="btn btn-outline-light btn-sm" href="/assignment_portal/public/create_assignment.php">Create Assignment</a>
        <a class="btn btn-outline-light btn-sm" href="/assignment_portal/public/dashboard_teacher.php">Teacher Dashboard</a>
      <?php else: ?>
        <a class="btn btn-outline-light btn-sm" href="/assignment_portal/public/assignments.php">Assignments</a>
        <a class="btn btn-outline-light btn-sm" href="/assignment_portal/public/dashboard_student.php">Student Dashboard</a>
      <?php endif; ?>
      <a class="btn btn-danger btn-sm" href="/assignment_portal/public/logout.php">Logout</a>
    <?php else: ?>
      <a class="btn btn-outline-light btn-sm" href="/assignment_portal/public/login.php">Login</a>
      <a class="btn btn-outline-light btn-sm" href="/assignment_portal/public/register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container my-5">
