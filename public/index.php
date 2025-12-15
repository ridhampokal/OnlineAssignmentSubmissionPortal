<?php
require_once __DIR__ . '/../includes/auth.php';

if (!is_logged_in()) {
    header('Location: /assignment_portal/public/login.php');
    exit;
}

$user = current_user();

if ($user['role'] === 'teacher') {
    header('Location: /assignment_portal/public/dashboard_teacher.php');
    exit;
} else {
    header('Location: /assignment_portal/public/dashboard_student.php');
    exit;
}
