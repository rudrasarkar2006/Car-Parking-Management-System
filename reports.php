<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Please log in as admin");
    exit();
}

$active = 'reports';

$today_sql = "SELECT SUM(amount) AS total_today FROM payments WHERE DATE(paid_at) = CURDATE()";
$total_today = $conn->query($today_sql)->fetch_assoc()['total_today'] ?? 0;

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

$top_slot_sql = "
    SELECT s.slot_number, COUNT(sess.session_id) AS times_used
    FROM parking_sessions sess
    JOIN parking_slots s ON sess.slot_id = s.slot_id
    GROUP BY s.slot_number
    ORDER BY times_used DESC
    LIMIT 5
";
$top_slots = $conn->query($top_slot_sql);

$alltime_sql = "SELECT SUM(amount) AS total_alltime FROM payments";
$total_alltime = $conn->query($alltime_sql)->fetch_assoc()['total_alltime'] ?? 0;

// Revenue for each of the last 7 days (including days with zero revenue)
$daily_revenue = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_sql = "SELECT SUM(amount) AS total FROM payments WHERE DATE(paid_at) = ?";
    $stmt = $conn->prepare($day_sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $daily_revenue[] = [
        'date' => date('M j', strtotime($date)),
        'total' => (float)$total
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <p class="page-title">Reports</p>
        <p class="page-subtitle">Revenue and usage statistics</p>

        <div style="margin-bottom:1.5rem;">
            <a href="detailed_report.php" class="badge badge-available" style="text-decoration:none;">🖨️ Generate Printable PDF Report</a>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <p class="stat-label">Revenue today</p>
                <p class="stat-value success">Tk <?php echo number_format($total_today, 2); ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Revenue all-time</p>
                <p class="stat-value">Tk <?php echo number_format($total_alltime, 2); ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Avg. parking duration</p>
                <p class="stat-value"><?php echo round($avg_minutes, 1); ?> min</p>
            </div>
        </div>

        <p class="section-title">Revenue trend (last 7 days)</p>
        <div style="max-width:700px;margin-bottom:2rem;">
            <canvas id="revenueChart" height="80"></canvas>
            <p id="chartFallback" style="display:none;color:var(--text-muted);font-size:13px;">
                Chart couldn't load (no internet connection). Here's the same data as a table:
            </p>
            <table id="fallbackTable" style="display:none;">
                <tr><th>Date</th><th>Revenue (Tk)</th></tr>
                <?php foreach ($daily_revenue as $d) { ?>
                    <tr><td><?php echo $d['date']; ?></td><td><?php echo number_format($d['total'], 2); ?></td></tr>
                <?php } ?>
            </table>
        </div>

        <p class="section-title">Revenue by vehicle type</p>
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

        <p class="section-title" style="margin-top:2rem;">Top 5 most-used slots</p>
        <table>
            <tr><th>Slot Number</th><th>Times Used</th></tr>
            <?php while ($row = $top_slots->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['slot_number']; ?></td>
                    <td><?php echo $row['times_used']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

<script>
    if (typeof Chart === 'undefined') {
        document.getElementById('chartFallback').style.display = 'block';
        document.getElementById('fallbackTable').style.display = 'table';
    } else {
        const labels = <?php echo json_encode(array_column($daily_revenue, 'date')); ?>;
        const data = <?php echo json_encode(array_column($daily_revenue, 'total')); ?>;

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const gridColor = isDark ? '#2c333a' : '#e5e5e0';
        const textColor = isDark ? '#8a939c' : '#777';

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue (Tk)',
                    data: data,
                    borderColor: '#1a7f5a',
                    backgroundColor: 'rgba(26, 127, 90, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor } },
                    x: { grid: { color: gridColor }, ticks: { color: textColor } }
                }
            }
        });
    }
</script>
</body>
</html>