<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';
include 'send_email.php';

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];

$check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    header("Location: register.php?error=An account with that email already exists");
    exit();
}

// Generate a real random 6-digit code
$code = strval(random_int(100000, 999999));

$_SESSION['pending_name'] = $name;
$_SESSION['pending_email'] = $email;
$_SESSION['pending_password'] = password_hash($password, PASSWORD_DEFAULT);
$_SESSION['pending_code'] = $code;

$subject = "Your ParkEase verification code";
$body = "
    <p>Hi $name,</p>
    <p>Your ParkEase verification code is:</p>
    <h2 style='letter-spacing:4px;'>$code</h2>
    <p>Enter this code to complete your registration.</p>
";

$sent = send_email($email, $name, $subject, $body);

if (!$sent) {
    header("Location: register.php?error=" . urlencode("Couldn't send verification email. Please try again."));
    exit();
}

header("Location: verify_email.php");
exit();
?>