<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';
include 'send_email.php';

$email = trim($_POST['email']);

$stmt = $conn->prepare("SELECT user_id, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    // Don't reveal whether the email exists or not - just show a generic message either way
    header("Location: login.php?error=" . urlencode("If that email exists, a reset code has been sent."));
    exit();
}

$code = strval(random_int(100000, 999999));

$_SESSION['reset_user_id'] = $user['user_id'];
$_SESSION['reset_code'] = $code;
$_SESSION['reset_email'] = $email;

$subject = "Your ParkEase password reset code";
$body = "
    <p>Hi {$user['name']},</p>
    <p>Your password reset code is:</p>
    <h2 style='letter-spacing:4px;'>$code</h2>
    <p>If you didn't request this, you can safely ignore this email.</p>
";
send_email($email, $user['name'], $subject, $body);

header("Location: reset_password.php");
exit();
?>