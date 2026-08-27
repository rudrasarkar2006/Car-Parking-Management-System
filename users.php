<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: login.php?error=Please log in as staff or admin");
    exit();
}

$active = 'users';

$sql = "
    SELECT u.user_id, u.name, u.email, u.role,
           v.plate_number, vt.type_name,
           s.slot_number,
           sess.entry_time
    FROM users u
    LEFT JOIN vehicles v ON v.owner_id = u.user_id
    LEFT JOIN vehicle_types vt ON v.type_id = vt.type_id
    LEFT JOIN parking_sessions sess ON sess.vehicle_id = v.vehicle_id AND sess.exit_time IS NULL
    LEFT JOIN parking_slots s ON sess.slot_id = s.slot_id
    ORDER BY u.name
";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $uid = $row['user_id'];
    if (!isset($users[$uid])) {
        $users[$uid] = [
            'name' => $row['name'],
            'email' => $row['email'],
            'role' => $row['role'],
            'vehicles' => []
        ];
    }
    if ($row['plate_number']) {
        $users[$uid]['vehicles'][] = [
            'plate_number' => $row['plate_number'],
            'type_name' => $row['type_name'],
            'slot_number' => $row['slot_number'],
            'entry_time' => $row['entry_time']
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <p class="page-title">All users</p>
        <p class="page-subtitle">Everyone registered, their vehicles, and where they're parked</p>

        <?php if (isset($_GET['msg'])) { ?>
            <p class="success-msg"><?php echo $_GET['msg']; ?></p>
        <?php } ?>
        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Vehicles</th>
                <th>Currently Parked</th>
                <?php if ($_SESSION['role'] === 'admin') { ?>
                    <th>Action</th>
                <?php } ?>
            </tr>
            <?php foreach ($users as $uid => $u) { ?>
                <tr>
                    <td><?php echo $u['name']; ?></td>
                    <td><?php echo $u['email']; ?></td>
                    <td><?php echo ucfirst($u['role']); ?></td>
                    <td>
                        <?php if (count($u['vehicles']) === 0) { ?>
                            -
                        <?php } else { ?>
                            <?php foreach ($u['vehicles'] as $v) { ?>
                                <?php echo $v['plate_number']; ?> (<?php echo ucfirst($v['type_name']); ?>)<br>
                            <?php } ?>
                        <?php } ?>
                    </td>
                    <td>
                        <?php
                        $parked_any = false;
                        foreach ($u['vehicles'] as $v) {
                            if ($v['slot_number']) {
                                $parked_any = true;
                                echo $v['plate_number'] . ' → Slot ' . $v['slot_number'] . '<br>';
                            }
                        }
                        if (!$parked_any) { echo '<span style="color:var(--text-muted);">Not parked</span>'; }
                        ?>
                    </td>
                    <?php if ($_SESSION['role'] === 'admin') { ?>
                        <td>
                            <?php if ($uid != $_SESSION['user_id']) { ?>
                                <a href="edit_user.php?id=<?php echo $uid; ?>">✏ Edit</a>
                                &nbsp;
                                <a href="delete_user.php?id=<?php echo $uid; ?>" onclick="return confirm('Delete this user?');">🗑 Delete</a>
                            <?php } else { ?>
                                <span style="color:var(--text-muted);">(you)</span>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
</body>
</html>