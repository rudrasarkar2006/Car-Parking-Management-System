<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-brand">
            <div class="icon-badge">🔑</div>
            <p class="brand-title">Forgot password</p>
            <p class="brand-subtitle">Enter your email to receive a reset code</p>
        </div>

        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <form action="forgot_password_process.php" method="POST">
            <label>Email</label>
            <div class="input-icon-wrap">
                <span class="icon-char">✉</span>
                <input type="email" name="email" placeholder="name@example.com" required>
            </div>

            <button type="submit">Send Reset Code</button>
        </form>

        <p style="text-align:center;font-size:13px;color:var(--text-muted);margin-top:1rem;">
            <a href="login.php">Back to login</a>
        </p>
    </div>
</div>
</body>
</html>