<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-brand">
            <div class="icon-badge">🅿️</div>
            <p class="brand-title">ParkEase</p>
            <p class="brand-subtitle">Sign in to your account</p>
        </div>

        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <form action="login_process.php" method="POST">
            <label>Email</label>
            <div class="input-icon-wrap">
                <span class="icon-char">✉</span>
                <input type="email" name="email" placeholder="name@example.com" required>
            </div>

            <label>Password</label>
            <div class="input-icon-wrap">
                <span class="icon-char">🔒</span>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <p style="text-align:right;font-size:12px;margin-top:6px;">
    <a href="forgot_password.php">Forgot password?</a>
</p>

            <button type="submit">Sign in</button>
        </form>

        <p style="text-align:center;font-size:13px;color:#777;margin-top:1rem;">
            New here? <a href="register.php">Create an account</a>
        </p>

        <div class="role-legend">
            <span>🛡 Admin</span>
            <span>👤 Staff</span>
            <span>🚗 Customer</span>
        </div>
    <div class="login-map-corner">
    <iframe
        src="https://www.google.com/maps?q=University of South Asia,Aminbazar,Bangladesh&output=embed"
        width="260"
        height="160"
        style="border:0;"
        allowfullscreen=""
        loading="lazy">
    </iframe>
    </div>
    </div>
</div>
</body>
</html>