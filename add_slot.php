<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Please log in as admin");
    exit();
}

$slot_number = trim($_POST['slot_number']);
$type_id = $_POST['type_id'];

// Check for duplicate slot number
$check = $conn->prepare("SELECT slot_id FROM parking_slots WHERE slot_number = ?");
$check->bind_param("s", $slot_number);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    header("Location: manage_slots.php?error=A slot with that number already exists");
    exit();
}

$stmt = $conn->prepare("INSERT INTO parking_slots (slot_number, type_id, status) VALUES (?, ?, 'available')");
$stmt->bind_param("si", $slot_number, $type_id);
$stmt->execute();
log_action($conn, $_SESSION['user_id'], "Slot added", "Slot: $slot_number");
header("Location: manage_slots.php?msg=Slot added successfully");
exit();
?>