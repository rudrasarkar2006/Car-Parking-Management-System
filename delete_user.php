<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Please log in as admin");
    exit();
}

$target_id = $_GET['id'];

// Prevent admin from deleting their own account
if ($target_id == $_SESSION['user_id']) {
    header("Location: users.php?error=" . urlencode("You can't delete your own account"));
    exit();
}

// Check if this user owns any vehicle with parking history
$check = $conn->prepare("
    SELECT COUNT(*) AS cnt
    FROM parking_sessions sess
    JOIN vehicles v ON sess.vehicle_id = v.vehicle_id
    WHERE v.owner_id = ?
");
$check->bind_param("i", $target_id);
$check->execute();
$has_history = $check->get_result()->fetch_assoc()['cnt'] > 0;

if ($has_history) {
    header("Location: users.php?error=" . urlencode("Can't delete a user with parking history"));
    exit();
}

// Check if this user has any vehicles at all (even with no history, delete vehicles first)
$del_vehicles = $conn->prepare("DELETE FROM vehicles WHERE owner_id = ?");
$del_vehicles->bind_param("i", $target_id);
$del_vehicles->execute();

$delete = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$delete->bind_param("i", $target_id);
$delete->execute();
log_action($conn, $_SESSION['user_id'], "User deleted", "Deleted user ID: $target_id");

header("Location: users.php?msg=User deleted");
exit();
?>