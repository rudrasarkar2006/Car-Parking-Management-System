<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Please log in as admin");
    exit();
}

$today_sql = "SELECT SUM(amount) AS total_today FROM payments WHERE DATE(paid_at) = CURDATE()";
$total_today = $conn->query($today_sql)->fetch_assoc()['total_today'] ?? 0;

$alltime_sql = "SELECT SUM(amount) AS total_alltime FROM payments";
$total_alltime = $conn->query($alltime_sql)->fetch_assoc()['total_alltime'] ?? 0;

$by_type_sql = "
    SELECT vt.type_name, SUM(p.amount) AS revenue, COUNT(p.payment_id) AS num_payments
    FROM payments p
    JOIN parking_sessions sess ON p.session_id = sess.session_id
    JOIN vehicles v ON sess.vehicle_id = v.vehicle_id
    JOIN vehicle_types vt ON v.type_id = vt.type_id
    GROUP BY vt.type_name
    ORDER BY revenue DESC
";
$by_type = $conn->query($by_type_sql);

$avg_sql = "SELECT AVG(TIMESTAMPDIFF(MINUTE, entry_time, exit_time)) AS avg_minutes FROM parking_sessions WHERE exit_time IS NOT NULL";
$avg_minutes = $conn->query($avg_sql)->fetch_assoc()['avg_minutes'] ?? 0;

$all_transactions_sql = "
    SELECT v.plate_number, vt.type_name, p.amount, p.method, p.paid_at
    FROM payments p
    JOIN parking_sessions sess ON p.session_id = sess.session_id
    JOIN vehicles v ON sess.vehicle_id = v.vehicle_id
    JOIN vehicle_types vt ON v.type_id = vt.type_id
    ORDER BY p.paid_at DESC
";
$all_transactions = $conn->query($all_transactions_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detailed Report - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: white; }
        .report-page { max-width: 800px; margin: 30px auto; padding: 20px; }
        .report-header { text-align: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid var(--accent); }
        .report-meta { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
        .no-print { text-align: center; margin: 1.5rem 0; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="report-page">
        <div class="report-header">
            <p style="font-size:22px;font-weight:700;margin:0;">🅿️ ParkEase — Detailed Report</p>
            <p class="report-meta">Generated on <?php echo date("F j, Y, g:i a"); ?></p>
        </div>

        <div class="no-print">
            <button onclick="window.print()">🖨️ Download / Print as PDF</button>
        </div>

        <p class="section-title">Summary</p>
        <table>
            <tr><th>Metric</th><th>Value</th></tr>
            <tr><td>Revenue today</td><td>Tk <?php echo number_format($total_today, 2); ?></td></tr>
            <tr><td>Revenue all-time</td><td>Tk <?php echo number_format($total_alltime, 2); ?></td></tr>
            <tr><td>Average parking duration</td><td><?php echo round($avg_minutes, 1); ?> minutes</td></tr>
        </table>

        <p class="section-title" style="margin-top:2rem;">Revenue by vehicle type</p>
        <table>
            <tr><th>Type</th><th>Revenue (Tk)</th><th>Payments</th></tr>
            <?php while ($row = $by_type->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo ucfirst($row['type_name']); ?></td>
                    <td><?php echo number_format($row['revenue'], 2); ?></td>
                    <td><?php echo $row['num_payments']; ?></td>
                </tr>
            <?php } ?>
        </table>

        <p class="section-title" style="margin-top:2rem;">All transactions</p>
        <table>
            <tr><th>Vehicle</th><th>Type</th><th>Amount</th><th>Method</th><th>Paid At</th></tr>
            <?php if ($all_transactions->num_rows === 0) { ?>
                <tr><td colspan="5">No transactions yet</td></tr>
            <?php } ?>
            <?php while ($row = $all_transactions->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['plate_number']; ?></td>
                    <td><?php echo ucfirst($row['type_name']); ?></td>
                    <td>Tk <?php echo number_format($row['amount'], 2); ?></td>
                    <td><?php echo ucwords(str_replace('_', ' ', $row['method'])); ?></td>
                    <td><?php echo $row['paid_at']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>