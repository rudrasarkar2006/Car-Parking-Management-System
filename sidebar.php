<?php
$role = $_SESSION['role'];
?>
<div class="sidebar">
    <div class="sidebar-logo">🅿️ ParkEase</div>

    <?php if ($role === 'admin') { ?>
        <a href="admin_dashboard.php" class="<?php echo $active === 'dashboard' ? 'active' : ''; ?>">📊 Dashboard</a>
        <a href="manage_slots.php" class="<?php echo $active === 'slots' ? 'active' : ''; ?>">🅿️ Manage Slots</a>
        <a href="reports.php" class="<?php echo $active === 'reports' ? 'active' : ''; ?>">📈 Reports</a>
        <a href="audit_log_page.php" class="<?php echo $active === 'audit' ? 'active' : ''; ?>">📜 Audit Log</a>
    <?php } elseif ($role === 'staff') { ?>
        <a href="staff_dashboard.php" class="<?php echo $active === 'dashboard' ? 'active' : ''; ?>">📊 Dashboard</a>
        <a href="entry_form.php" class="<?php echo $active === 'entry' ? 'active' : ''; ?>">🚘 Vehicle Entry</a>
        <a href="exit_form.php" class="<?php echo $active === 'exit' ? 'active' : ''; ?>">🚗 Vehicle Exit</a>
        <a href="slot_requests.php" class="<?php echo $active === 'requests' ? 'active' : ''; ?>">📋 Slot Requests</a>
    <?php } else { ?>
        <a href="customer_dashboard.php" class="<?php echo $active === 'dashboard' ? 'active' : ''; ?>">📊 Dashboard</a>
    <?php } ?>

    <a href="logout.php">🚪 Logout</a>
    <a href="users.php" class="<?php echo $active === 'users' ? 'active' : ''; ?>">👥 Users</a>
      <button onclick="toggleTheme()" style="margin-top:auto;background:none;border:1px solid var(--border-color);color:var(--text-main);padding:8px 10px;border-radius:8px;cursor:pointer;font-size:13px;">
    🌓 Toggle Theme
</button>
  
</div>