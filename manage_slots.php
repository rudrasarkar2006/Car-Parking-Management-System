<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Please log in as admin");
    exit();
}

$active = 'slots';
$types = $conn->query("SELECT * FROM vehicle_types");

$slots_sql = "
    SELECT s.slot_id, s.slot_number, s.status, vt.type_name
    FROM parking_slots s
    JOIN vehicle_types vt ON s.type_id = vt.type_id
    ORDER BY vt.type_name, s.slot_number
";
$slots = $conn->query($slots_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Slots - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <p class="page-title">Manage parking slots</p>
        <p class="page-subtitle">Add, view, or remove slots</p>

        <?php if (isset($_GET['msg'])) { ?>
            <p class="success-msg"><?php echo $_GET['msg']; ?></p>
        <?php } ?>
        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <p class="section-title">Vehicle type limits</p>
        <table style="max-width:500px;margin-bottom:2rem;">
            <tr><th>Type</th><th>Hourly Rate</th><th>Max Hours</th><th>Action</th></tr>
            <?php
            $type_rows = $conn->query("SELECT * FROM vehicle_types");
            while ($t = $type_rows->fetch_assoc()) { ?>
                <tr>
                    <form action="update_type_limit.php" method="POST">
                        <td><?php echo ucfirst($t['type_name']); ?></td>
                        <td>Tk <?php echo $t['hourly_rate']; ?>/hr</td>
                        <td>
                            <input type="hidden" name="type_id" value="<?php echo $t['type_id']; ?>">
                            <input type="number" name="max_hours" value="<?php echo $t['max_hours']; ?>" min="1" style="width:80px;">
                        </td>
                        <td><button type="submit" style="margin-top:0;">Update</button></td>
                    </form>
                </tr>
            <?php } ?>
        </table>

        <p class="section-title">Add a new slot</p>
        <div style="max-width:400px;margin-bottom:2rem;">
            <form action="add_slot.php" method="POST">
                <label>Slot Number</label>
                <input type="text" name="slot_number" placeholder="e.g. C2" required>

                <label>Vehicle Type</label>
                <select name="type_id" required>
                    <?php $types->data_seek(0); while ($row = $types->fetch_assoc()) { ?>
                        <option value="<?php echo $row['type_id']; ?>"><?php echo ucfirst($row['type_name']); ?></option>
                    <?php } ?>
                </select>

                <button type="submit">Add Slot</button>
            </form>
        </div>

        <p class="section-title">All slots</p>
        <table>
            <tr><th>Slot Number</th><th>Type</th><th>Status</th><th>Action</th></tr>
            <?php while ($row = $slots->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['slot_number']; ?></td>
                    <td><?php echo ucfirst($row['type_name']); ?></td>
                    <td>
                        <?php if ($row['status'] === 'available') { ?>
                            <span class="badge badge-available">Available</span>
                        <?php } else { ?>
                            <span class="badge badge-occupied">Occupied</span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="delete_slot.php?id=<?php echo $row['slot_id']; ?>"
                           onclick="return confirm('Delete this slot?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
</body>
</html>