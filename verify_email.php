<?php
session_start();

if (!isset($_SESSION['pending_email'])) {
    header("Location: register.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify Email - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-brand">
            <div class="icon-badge">✉</div>
            <p class="brand-title">Verify your email</p>
            <p class="brand-subtitle">
                A verification code was sent to<br>
                <strong><?php echo $_SESSION['pending_email']; ?></strong>
            </p>
        </div>

        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <form action="verify_email_process.php" method="POST">
            <label>Verification Code</label>
            <input type="text" name="code" maxlength="6" placeholder="Enter 6-digit code" required>

            <button type="submit">Verify & Create Account</button>
        </form>
    </div>
</div>
</body>
</html>