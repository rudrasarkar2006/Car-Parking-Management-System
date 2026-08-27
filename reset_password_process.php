<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot_password.php");
    exit();
}

$entered_code = trim($_POST['code']);
$new_password = $_POST['new_password'];

if ($entered_code !== $_SESSION['reset_code']) {
    header("Location: reset_password.php?error=Incorrect code. Please try again.");
    exit();
}

$user_id = $_SESSION['reset_user_id'];
$hashed = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
$stmt->bind_param("si", $hashed, $user_id);
$stmt->execute();

log_action($conn, $user_id, "Password reset", "User reset their own password via email code");

unset($_SESSION['reset_user_id']);
unset($_SESSION['reset_code']);
unset($_SESSION['reset_email']);

header("Location: login.php?error=" . urlencode("Password reset successfully. Please sign in."));
exit();
?>