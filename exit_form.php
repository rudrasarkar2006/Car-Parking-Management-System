<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php?error=Please log in as staff");
    exit();
}

$active = 'exit';

$parked_sql = "
    SELECT v.plate_number, vt.type_name, s.slot_number
    FROM parking_sessions sess
    JOIN vehicles v ON sess.vehicle_id = v.vehicle_id
    JOIN vehicle_types vt ON v.type_id = vt.type_id
    JOIN parking_slots s ON sess.slot_id = s.slot_id
    WHERE sess.exit_time IS NULL
    ORDER BY sess.entry_time DESC
";
$parked_vehicles = $conn->query($parked_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Exit - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <p class="page-title">Vehicle exit</p>
        <p class="page-subtitle">Check a vehicle out and calculate payment</p>

        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <div style="max-width:400px;">
            <form action="exit_summary.php" method="POST">
                <label>Select Vehicle</label>
                <select name="plate_number" required>
                    <option value="">-- Choose a parked vehicle --</option>
                    <?php if ($parked_vehicles->num_rows === 0) { ?>
                        <option value="" disabled>No vehicles currently parked</option>
                    <?php } ?>
                    <?php while ($row = $parked_vehicles->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($row['plate_number']); ?>">
                            <?php echo $row['plate_number']; ?> — <?php echo ucfirst($row['type_name']); ?> (Slot <?php echo $row['slot_number']; ?>)
                        </option>
                    <?php } ?>
                </select>

                <button type="submit">Continue to Payment</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>