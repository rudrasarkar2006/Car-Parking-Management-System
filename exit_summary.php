<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php?error=Please log in as staff");
    exit();
}

$plate_number = trim($_POST['plate_number']);

$sql = "SELECT * FROM vehicles WHERE plate_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $plate_number);
$stmt->execute();
$vehicle = $stmt->get_result()->fetch_assoc();

if (!$vehicle) {
    header("Location: exit_form.php?error=Vehicle not found");
    exit();
}
$vehicle_id = $vehicle['vehicle_id'];
$type_id = $vehicle['type_id'];

$sql = "SELECT * FROM parking_sessions WHERE vehicle_id = ? AND exit_time IS NULL LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

if (!$session) {
    header("Location: exit_form.php?error=This vehicle is not currently checked in");
    exit();
}
$entry_time = $session['entry_time'];

$sql = "SELECT * FROM vehicle_types WHERE type_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $type_id);
$stmt->execute();
$type = $stmt->get_result()->fetch_assoc();
$hourly_rate = $type['hourly_rate'];

$entry_ts = strtotime($entry_time);
$now_ts = time();
$duration_hours = ($now_ts - $entry_ts) / 3600;
$billable_hours = max(1, ceil($duration_hours));
$amount = $billable_hours * $hourly_rate;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <p class="page-title">Payment</p>
        <p class="page-subtitle">Review the bill and complete checkout</p>

        <div class="payment-card">
            <div class="payment-row">
                <span>Vehicle</span>
                <strong><?php echo htmlspecialchars($plate_number); ?></strong>
            </div>
            <div class="payment-row">
                <span>Type</span>
                <strong><?php echo ucfirst($type['type_name']); ?></strong>
            </div>
            <div class="payment-row">
                <span>Entry Time</span>
                <strong><?php echo $entry_time; ?></strong>
            </div>
            <div class="payment-row">
                <span>Duration</span>
                <strong><?php echo $billable_hours; ?> hour(s)</strong>
            </div>
            <div class="payment-row">
                <span>Rate</span>
                <strong>Tk <?php echo $hourly_rate; ?>/hr</strong>
            </div>
            <div class="payment-divider"></div>
            <div class="payment-row payment-total">
                <span>Total Amount</span>
                <strong>Tk <?php echo number_format($amount, 2); ?></strong>
            </div>

            <form action="exit_process.php" method="POST">
                <input type="hidden" name="plate_number" value="<?php echo htmlspecialchars($plate_number); ?>">

                <label style="margin-top:20px;">Choose payment method</label>
                <div class="payment-methods">
                    <label class="payment-method-option">
                        <input type="radio" name="payment_method" value="cash" checked>
                        <span>💵 Cash</span>
                    </label>
                    <label class="payment-method-option">
                        <input type="radio" name="payment_method" value="card">
                        <span>💳 Card</span>
                    </label>
                    <label class="payment-method-option">
                        <input type="radio" name="payment_method" value="mobile_banking">
                        <span>📱 Mobile Banking</span>
                    </label>
                </div>

                <button type="submit" style="width:100%;">Confirm Payment — Tk <?php echo number_format($amount, 2); ?></button>
            </form>
        </div>
    </div>
</div>
</body>
</html>