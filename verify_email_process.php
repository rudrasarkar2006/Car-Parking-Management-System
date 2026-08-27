<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['pending_email'])) {
    header("Location: register.php");
    exit();
}

$entered_code = trim($_POST['code']);

if ($entered_code !== $_SESSION['pending_code']) {
    header("Location: verify_email.php?error=Incorrect code. Please try again.");
    exit();
}

$name = $_SESSION['pending_name'];
$email = $_SESSION['pending_email'];
$hashed_password = $_SESSION['pending_password'];

$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
$stmt->bind_param("sss", $name, $email, $hashed_password);
$stmt->execute();

unset($_SESSION['pending_name']);
unset($_SESSION['pending_email']);
unset($_SESSION['pending_password']);
unset($_SESSION['pending_code']);

header("Location: login.php?error=" . urlencode("Email verified! Account created. Please sign in."));
exit();
?>