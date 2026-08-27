<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Please log in as admin");
    exit();
}

$slot_id = $_GET['id'];

// Check the slot's current status - block deleting an occupied slot
$check = $conn->prepare("SELECT status FROM parking_slots WHERE slot_id = ?");
$check->bind_param("i", $slot_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_slots.php?error=Slot not found");
    exit();
}
$slot = $result->fetch_assoc();

if ($slot['status'] === 'occupied') {
    header("Location: manage_slots.php?error=" . urlencode("Can't delete an occupied slot"));
    exit();
}

// Check if this slot has any parking history - block if so (preserve records)
$history_check = $conn->prepare("SELECT COUNT(*) AS cnt FROM parking_sessions WHERE slot_id = ?");
$history_check->bind_param("i", $slot_id);
$history_check->execute();
$has_history = $history_check->get_result()->fetch_assoc()['cnt'] > 0;

if ($has_history) {
    header("Location: manage_slots.php?error=" . urlencode("Can't delete a slot with parking history"));
    exit();
}

$delete = $conn->prepare("DELETE FROM parking_slots WHERE slot_id = ?");
$delete->bind_param("i", $slot_id);
$delete->execute();
log_action($conn, $_SESSION['user_id'], "Slot deleted", "Slot ID: $slot_id");

header("Location: manage_slots.php?msg=Slot deleted");
exit();
?>