<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php?error=Please log in as staff");
    exit();
}

$active = 'requests';

$sql = "
    SELECT sr.request_id, sr.status, sr.requested_at, s.slot_number, u.name, u.email
    FROM slot_requests sr
    JOIN parking_slots s ON sr.slot_id = s.slot_id
    JOIN users u ON sr.customer_id = u.user_id
    ORDER BY sr.requested_at DESC
";
$requests = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Slot Requests - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <p class="page-title">Slot requests</p>
        <p class="page-subtitle">Customer requests for specific slots</p>

        <?php if (isset($_GET['msg'])) { ?>
            <p class="success-msg"><?php echo $_GET['msg']; ?></p>
        <?php } ?>

        <table>
            <tr><th>Customer</th><th>Slot</th><th>Requested</th><th>Status</th><th>Action</th></tr>
            <?php if ($requests->num_rows === 0) { ?>
                <tr><td colspan="5">No requests yet</td></tr>
            <?php } ?>
            <?php while ($row = $requests->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['name']; ?> (<?php echo $row['email']; ?>)</td>
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
                    <td>
                        <?php if ($row['status'] === 'pending') { ?>
                            <a href="respond_slot_request.php?id=<?php echo $row['request_id']; ?>&action=approve">Approve</a>
                            &nbsp;
                            <a href="respond_slot_request.php?id=<?php echo $row['request_id']; ?>&action=reject">Reject</a>
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