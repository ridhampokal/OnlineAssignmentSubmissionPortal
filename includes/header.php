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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
  <style>header{display:flex;justify-content:space-between;align-items:center} nav a{margin-right:15px}</style>
</head>
<body>
<header>
  <div><h2>Assignment Portal</h2></div>
  <nav>
    <?php if ($user): ?>
      <a href="/assignment_portal/public/student_home.php">Home</a>
      <?php if ($user['role'] === 'teacher'): ?>
        <a href="/assignment_portal/public/create_assignment.php">Create Assignment</a>
        <a href="/assignment_portal/public/dashboard_teacher.php">Teacher Dashboard</a>
      <?php else: ?>
        <a href="/assignment_portal/public/assignments.php">Assignments</a>
        <a href="/assignment_portal/public/dashboard_student.php">Student Dashboard</a>
      <?php endif; ?>
      <a href="/assignment_portal/public/logout.php">Logout</a>
    <?php else: ?>
      <a href="/assignment_portal/public/login.php">Login</a>
      <a href="/assignment_portal/public/register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
<main>
