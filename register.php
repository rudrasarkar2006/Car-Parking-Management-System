<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-brand">
            <div class="icon-badge">🅿️</div>
            <p class="brand-title">Create account</p>
            <p class="brand-subtitle">Register as a customer</p>
        </div>

        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <form action="register_process.php" method="POST">
            <label>Full Name</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <div class="input-icon-wrap">
                <span class="icon-char">✉</span>
                <input type="email" name="email" placeholder="name@example.com" required>
            </div>

            <label>Password</label>
            <div class="input-icon-wrap">
                <span class="icon-char">🔒</span>
                <input type="password" name="password" required minlength="4">
            </div>

            <button type="submit">Create account</button>
        </form>

        <p style="text-align:center;font-size:13px;color:#777;margin-top:1rem;">
            Already have an account? <a href="login.php">Sign in</a>
        </p>
    </div>
</div>
</body>
</html>