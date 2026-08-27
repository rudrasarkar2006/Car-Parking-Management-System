<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php?error=Please log in as staff");
    exit();
}

$active = 'dashboard';

$active_count_sql = "SELECT COUNT(*) AS cnt FROM parking_sessions WHERE exit_time IS NULL";
$active_count = $conn->query($active_count_sql)->fetch_assoc()['cnt'];

$today_entries_sql = "SELECT COUNT(*) AS cnt FROM parking_sessions WHERE DATE(entry_time) = CURDATE()";
$today_entries = $conn->query($today_entries_sql)->fetch_assoc()['cnt'];

$today_exits_sql = "SELECT COUNT(*) AS cnt FROM parking_sessions WHERE DATE(exit_time) = CURDATE()";
$today_exits = $conn->query($today_exits_sql)->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <p class="page-title">Welcome, <?php echo $_SESSION['name']; ?></p>
        <p class="page-subtitle">Staff dashboard</p>

        <?php if (isset($_GET['msg'])) { ?>
            <p class="success-msg"><?php echo $_GET['msg']; ?></p>
        <?php } ?>

        <div class="stat-grid">
            <div class="stat-card">
                <p class="stat-label">Currently parked</p>
                <p class="stat-value danger"><?php echo $active_count; ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Entries today</p>
                <p class="stat-value"><?php echo $today_entries; ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Exits today</p>
                <p class="stat-value success"><?php echo $today_exits; ?></p>
            </div>
        </div>

        <p class="section-title">Quick actions</p>
        <a href="entry_form.php" class="badge badge-available" style="text-decoration:none;">🚘 New Vehicle Entry</a>
        &nbsp;
        <a href="exit_form.php" class="badge badge-occupied" style="text-decoration:none;">🚗 Vehicle Exit</a>
    </div>
</div>
</body>
</html>