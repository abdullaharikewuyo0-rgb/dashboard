<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$timeout = 1800;
if (isset($_SESSION['login_at']) && (time() - $_SESSION['login_at']) > $timeout) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}

$userName     = $_SESSION['name'];
$userInitials = $_SESSION['initials'];
$userEmail    = $_SESSION['email'];
?>