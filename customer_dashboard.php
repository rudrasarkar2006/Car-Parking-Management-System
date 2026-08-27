<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php?error=Please log in as customer");
    exit();
}

$active = 'dashboard';
$customer_id = $_SESSION['user_id'];

$vehicles_sql = "
    SELECT v.vehicle_id, v.plate_number, vt.type_name
    FROM vehicles v
    JOIN vehicle_types vt ON v.type_id = vt.type_id
    WHERE v.owner_id = ?
";
$stmt = $conn->prepare($vehicles_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$vehicles = $stmt->get_result();

$history_sql = "
    SELECT sess.session_id, v.plate_number, s.slot_number, sess.entry_time, sess.exit_time,
           p.amount, p.method
    FROM parking_sessions sess
    JOIN vehicles v ON sess.vehicle_id = v.vehicle_id
    JOIN parking_slots s ON sess.slot_id = s.slot_id
    LEFT JOIN payments p ON sess.session_id = p.session_id
    WHERE v.owner_id = ?
    ORDER BY sess.entry_time DESC
";
$stmt = $conn->prepare($history_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$history = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <p class="page-title">Welcome, <?php echo $_SESSION['name']; ?></p>
        <p class="page-subtitle">Your vehicles and parking history</p>

        <?php if (isset($_GET['msg'])) { ?>
            <p class="success-msg"><?php echo $_GET['msg']; ?></p>
        <?php } ?>
        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <p class="section-title">Live slot availability</p>
        <div class="stat-grid">
            <?php
            $availability_sql = "
                SELECT vt.type_name,
                       COUNT(s.slot_id) AS total,
                       SUM(CASE WHEN s.status = 'available' THEN 1 ELSE 0 END) AS available
                FROM vehicle_types vt
                LEFT JOIN parking_slots s ON s.type_id = vt.type_id
                GROUP BY vt.type_name
            ";
            $availability = $conn->query($availability_sql);
            while ($row = $availability->fetch_assoc()) {
                $total = $row['total'];
                $available = $row['available'] ?? 0;
            ?>
                <div class="stat-card">
                    <p class="stat-label"><?php echo ucfirst($row['type_name']); ?> slots</p>
                    <p class="stat-value success"><?php echo $available; ?> <span style="font-size:14px;color:var(--text-muted);">/ <?php echo $total; ?></span></p>
                </div>
            <?php } ?>
        </div>

        <?php
        $grid_slots_sql = "
            SELECT s.slot_number, s.status, vt.type_name
            FROM parking_slots s
            JOIN vehicle_types vt ON s.type_id = vt.type_id
            ORDER BY vt.type_name, s.slot_number
        ";
        $grid_slots = $conn->query($grid_slots_sql);
        $grouped = [];
        while ($row = $grid_slots->fetch_assoc()) {
            $grouped[$row['type_name']][] = $row;
        }
        ?>
        <?php foreach ($grouped as $type_name => $slots) { ?>
            <p style="font-size:13px;color:var(--text-muted);margin:0 0 8px;"><?php echo ucfirst($type_name); ?> slots</p>
            <div class="slot-grid">
                <?php foreach ($slots as $s) { ?>
                    <div class="slot-box <?php echo $s['status']; ?>"><?php echo $s['slot_number']; ?></div>
                <?php } ?>
            </div>
        <?php } ?>

        <p class="section-title" style="margin-top:2rem;">Request a specific slot</p>
        <div style="max-width:400px;margin-bottom:1.5rem;">
            <form action="request_slot.php" method="POST">
                <label>Choose an available slot</label>
                <select name="slot_id" required>
                    <option value="">-- Select a slot --</option>
                    <?php
                    $available_slots_sql = "
                        SELECT s.slot_id, s.slot_number, vt.type_name
                        FROM parking_slots s
                        JOIN vehicle_types vt ON s.type_id = vt.type_id
                        WHERE s.status = 'available'
                        ORDER BY vt.type_name, s.slot_number
                    ";
                    $available_slots = $conn->query($available_slots_sql);
                    while ($row = $available_slots->fetch_assoc()) { ?>
                        <option value="<?php echo $row['slot_id']; ?>">
                            <?php echo $row['slot_number']; ?> (<?php echo ucfirst($row['type_name']); ?>)
                        </option>
                    <?php } ?>
                </select>
                <button type="submit">Request This Slot</button>
            </form>
        </div>

        <p class="section-title">My slot requests</p>
        <table>
            <tr><th>Slot</th><th>Requested At</th><th>Status</th></tr>
            <?php
            $my_requests_sql = "
                SELECT sr.status, sr.requested_at, s.slot_number
                FROM slot_requests sr
                JOIN parking_slots s ON sr.slot_id = s.slot_id
                WHERE sr.customer_id = ?
                ORDER BY sr.requested_at DESC
            ";
            $stmt = $conn->prepare($my_requests_sql);
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $my_requests = $stmt->get_result();
            if ($my_requests->num_rows === 0) { ?>
                <tr><td colspan="3">No requests yet</td></tr>
            <?php }
            while ($row = $my_requests->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['slot_number']; ?></td>
                    <td><?php echo $row['requested_at']; ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending') { ?>
                            <span class="badge" style="background:#fff3cd;color:#856404;">Pending</span>
                        <?php } elseif ($row['status'] === 'approved') { ?>
                            <span class="badge badge-available">Approved</span>
                        <?php } else { ?>
                            <span class="badge badge-occupied">Rejected</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <p class="section-title" style="margin-top:2rem;">My vehicles</p>
        <table>
            <tr><th>Plate Number</th><th>Type</th></tr>
            <?php if ($vehicles->num_rows === 0) { ?>
                <tr><td colspan="2">No vehicles registered yet</td></tr>
            <?php } ?>
            <?php while ($row = $vehicles->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['plate_number']; ?></td>
                    <td><?php echo ucfirst($row['type_name']); ?></td>
                </tr>
            <?php } ?>
        </table>

        <p class="section-title" style="margin-top:2rem;">Parking history</p>
        <table>
            <tr><th>Plate Number</th><th>Slot</th><th>Entry Time</th><th>Exit Time</th><th>Amount Paid</th></tr>
            <?php if ($history->num_rows === 0) { ?>
                <tr><td colspan="5">No parking history yet</td></tr>
            <?php } ?>
            <?php while ($row = $history->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['plate_number']; ?></td>
                    <td><?php echo $row['slot_number']; ?></td>
                    <td><?php echo $row['entry_time']; ?></td>
                    <td>
                        <?php if ($row['exit_time']) { ?>
                            <?php echo $row['exit_time']; ?>
                        <?php } else { ?>
                            <span class="badge badge-occupied">Still parked</span>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($row['amount'] !== null) { ?>
                            Tk <?php echo number_format($row['amount'], 2); ?>
                            <br><a href="receipt.php?session_id=<?php echo $row['session_id']; ?>" style="font-size:12px;">View Receipt</a>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
</body>
</html>