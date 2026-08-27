<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php?error=Please log in as customer");
    exit();
}

$customer_id = $_SESSION['user_id'];
$slot_id = $_POST['slot_id'];

// Re-verify the slot is still available (never trust the dropdown alone)
$check = $conn->prepare("SELECT status FROM parking_slots WHERE slot_id = ?");
$check->bind_param("i", $slot_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0 || $result->fetch_assoc()['status'] !== 'available') {
    header("Location: customer_dashboard.php?error=That slot is no longer available");
    exit();
}

$stmt = $conn->prepare("INSERT INTO slot_requests (customer_id, slot_id, status) VALUES (?, ?, 'pending')");
$stmt->bind_param("ii", $customer_id, $slot_id);
$stmt->execute();

header("Location: customer_dashboard.php?msg=Slot request submitted");
exit();
?>