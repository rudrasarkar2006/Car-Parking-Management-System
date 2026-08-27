<?php
session_start();

if (!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-brand">
            <div class="icon-badge">🔑</div>
            <p class="brand-title">Reset password</p>
            <p class="brand-subtitle">
                Enter the code sent to<br>
                <strong><?php echo $_SESSION['reset_email']; ?></strong>
            </p>
        </div>

        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <form action="reset_password_process.php" method="POST">
            <label>Reset Code</label>
            <input type="text" name="code" maxlength="6" placeholder="Enter 6-digit code" required>

            <label>New Password</label>
            <div class="input-icon-wrap">
                <span class="icon-char">🔒</span>
                <input type="password" name="new_password" required minlength="4">
            </div>

            <button type="submit">Reset Password</button>
        </form>
    </div>
</div>
</body>
</html>