<?php
require_once __DIR__ . '/../includes/auth.php';
session_destroy();
header('Location: /assignment_portal/public/login.php');
exit;
