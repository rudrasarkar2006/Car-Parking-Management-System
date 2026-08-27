<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Please log in as admin");
    exit();
}

$active = 'audit';

$sql = "
    SELECT a.action, a.details, a.logged_at, u.name, u.role
    FROM audit_log a
    JOIN users u ON a.user_id = u.user_id
    ORDER BY a.logged_at DESC
    LIMIT 100
";
$logs = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Audit Log - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <p class="page-title">Audit log</p>
        <p class="page-subtitle">Recent actions across the system (most recent 100)</p>

        <table>
            <tr><th>User</th><th>Role</th><th>Action</th><th>Details</th><th>Time</th></tr>
            <?php if ($logs->num_rows === 0) { ?>
                <tr><td colspan="5">No activity logged yet</td></tr>
            <?php } ?>
            <?php while ($row = $logs->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo ucfirst($row['role']); ?></td>
                    <td><?php echo $row['action']; ?></td>
                    <td style="font-size:12px;color:var(--text-muted);"><?php echo $row['details']; ?></td>
                    <td><?php echo $row['logged_at']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
</body>
</html>