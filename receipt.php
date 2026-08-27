<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$session_id = $_GET['session_id'];

$sql = "
    SELECT p.amount, p.method, p.paid_at,
           sess.entry_time, sess.exit_time,
           v.plate_number, v.owner_id,
           vt.type_name, vt.hourly_rate,
           s.slot_number
    FROM payments p
    JOIN parking_sessions sess ON p.session_id = sess.session_id
    JOIN vehicles v ON sess.vehicle_id = v.vehicle_id
    JOIN vehicle_types vt ON v.type_id = vt.type_id
    JOIN parking_slots s ON sess.slot_id = s.slot_id
    WHERE p.session_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $session_id);
$stmt->execute();
$receipt = $stmt->get_result()->fetch_assoc();

if (!$receipt) {
    die("Receipt not found.");
}

if ($_SESSION['role'] === 'customer' && $receipt['owner_id'] != $_SESSION['user_id']) {
    die("You don't have permission to view this receipt.");
}

$duration_hours = round((strtotime($receipt['exit_time']) - strtotime($receipt['entry_time'])) / 3600, 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
    <style>
        .receipt-box {
            max-width: 420px;
            margin: 40px auto;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 2rem;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px dashed var(--border-color);
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
            color: var(--text-muted);
        }
        .receipt-row strong { color: var(--text-main); }
        .receipt-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed var(--border-color);
            font-size: 18px;
        }
        .receipt-total strong { color: var(--accent); }
        .no-print { text-align: center; margin-top: 1.5rem; }

        @media print {
            .no-print { display: none; }
            body { background: white; }
            .receipt-box { box-shadow: none; border: none; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="receipt-box">
        <div class="receipt-header">
            <p style="font-size:20px;font-weight:700;margin:0;">🅿️ ParkEase</p>
            <p style="font-size:12px;color:var(--text-muted);margin:4px 0 0;">Payment Receipt</p>
        </div>

        <div class="receipt-row"><span>Receipt No.</span><strong>#<?php echo str_pad($session_id, 6, '0', STR_PAD_LEFT); ?></strong></div>
        <div class="receipt-row"><span>Vehicle</span><strong><?php echo $receipt['plate_number']; ?></strong></div>
        <div class="receipt-row"><span>Type</span><strong><?php echo ucfirst($receipt['type_name']); ?></strong></div>
        <div class="receipt-row"><span>Slot</span><strong><?php echo $receipt['slot_number']; ?></strong></div>
        <div class="receipt-row"><span>Entry Time</span><strong><?php echo $receipt['entry_time']; ?></strong></div>
        <div class="receipt-row"><span>Exit Time</span><strong><?php echo $receipt['exit_time']; ?></strong></div>
        <div class="receipt-row"><span>Duration</span><strong><?php echo $duration_hours; ?> hr(s)</strong></div>
        <div class="receipt-row"><span>Rate</span><strong>Tk <?php echo $receipt['hourly_rate']; ?>/hr</strong></div>
        <div class="receipt-row"><span>Payment Method</span><strong><?php echo ucwords(str_replace('_', ' ', $receipt['method'])); ?></strong></div>
        <div class="receipt-row"><span>Paid At</span><strong><?php echo $receipt['paid_at']; ?></strong></div>

        <div class="receipt-row receipt-total">
            <span>Total Paid</span>
            <strong>Tk <?php echo number_format($receipt['amount'], 2); ?></strong>
        </div>

        <div class="no-print">
            <button onclick="window.print()">🖨️ Download / Print as PDF</button>
        </div>
    </div>
</body>
</html>