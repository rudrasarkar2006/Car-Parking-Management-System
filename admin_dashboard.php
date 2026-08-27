<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Please log in as admin");
    exit();
}

$active = 'dashboard';

$slot_summary_sql = "
    SELECT vt.type_name,
           COUNT(ps.slot_id) AS total_slots,
           SUM(CASE WHEN ps.status = 'occupied' THEN 1 ELSE 0 END) AS occupied_slots
    FROM parking_slots ps
    JOIN vehicle_types vt ON ps.type_id = vt.type_id
    GROUP BY vt.type_name
";
$slot_summary = $conn->query($slot_summary_sql);

$totals_sql = "
    SELECT COUNT(*) AS total_slots,
           SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) AS occupied_slots
    FROM parking_slots
";
$totals = $conn->query($totals_sql)->fetch_assoc();
$total_slots = $totals['total_slots'];
$occupied_slots = $totals['occupied_slots'];
$available_slots = $total_slots - $occupied_slots;

$active_sql = "
    SELECT v.plate_number, vt.type_name, vt.max_hours, s.slot_number, sess.entry_time,
           TIMESTAMPDIFF(HOUR, sess.entry_time, NOW()) AS hours_parked
    FROM parking_sessions sess
    JOIN vehicles v ON sess.vehicle_id = v.vehicle_id
    JOIN vehicle_types vt ON v.type_id = vt.type_id
    JOIN parking_slots s ON sess.slot_id = s.slot_id
    WHERE sess.exit_time IS NULL
    ORDER BY sess.entry_time DESC
";
$active_sessions = $conn->query($active_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <p class="page-title">Admin dashboard</p>
        <p class="page-subtitle">Live slot status and activity</p>

        <div class="stat-grid">
            <div class="stat-card">
                <p class="stat-label">Total slots</p>
                <p class="stat-value"><?php echo $total_slots; ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Occupied</p>
                <p class="stat-value danger"><?php echo $occupied_slots; ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Available</p>
                <p class="stat-value success"><?php echo $available_slots; ?></p>
            </div>
        </div>

        <p class="section-title">Slot summary by type</p>
        <table>
            <tr><th>Vehicle Type</th><th>Total</th><th>Occupied</th><th>Available</th></tr>
            <?php while ($row = $slot_summary->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo ucfirst($row['type_name']); ?></td>
                    <td><?php echo $row['total_slots']; ?></td>
                    <td><?php echo $row['occupied_slots']; ?></td>
                    <td><?php echo $row['total_slots'] - $row['occupied_slots']; ?></td>
                </tr>
            <?php } ?>
        </table>

        <p class="section-title" style="margin-top:2rem;">Currently parked vehicles</p>
        <table>
            <tr><th>Plate Number</th><th>Type</th><th>Slot</th><th>Entry Time</th><th>Status</th></tr>
            <?php if ($active_sessions->num_rows === 0) { ?>
                <tr><td colspan="5">No vehicles currently parked</td></tr>
            <?php } ?>
          <?php while ($row = $active_sessions->fetch_assoc()) {
    $is_overdue = $row['hours_parked'] > $row['max_hours'];
?>
    <tr>
        <td><?php echo $row['plate_number']; ?></td>
        <td><?php echo ucfirst($row['type_name']); ?></td>
        <td><?php echo $row['slot_number']; ?></td>
        <td><?php echo $row['entry_time']; ?></td>
        <td>
            <span class="badge badge-occupied">Occupied</span>
            <?php if ($is_overdue) { ?>
                <span class="badge" style="background:#fff3cd;color:#856404;">Overdue (<?php echo $row['hours_parked']; ?>h / <?php echo $row['max_hours']; ?>h limit)</span>
            <?php } ?>
        </td>
    </tr>
<?php } ?>
        </table>
    </div>
</div>
</body>
</html>