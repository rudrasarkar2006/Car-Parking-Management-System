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
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: exit_form.php?error=No vehicle found with that plate number");
    exit();
}
$vehicle = $result->fetch_assoc();
$vehicle_id = $vehicle['vehicle_id'];
$type_id = $vehicle['type_id'];

$sql = "SELECT * FROM parking_sessions WHERE vehicle_id = ? AND exit_time IS NULL LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: exit_form.php?error=This vehicle is not currently checked in");
    exit();
}
$session = $result->fetch_assoc();
$session_id = $session['session_id'];
$slot_id = $session['slot_id'];
$entry_time = $session['entry_time'];

$sql = "SELECT hourly_rate FROM vehicle_types WHERE type_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $type_id);
$stmt->execute();
$result = $stmt->get_result();
$rate_row = $result->fetch_assoc();
$hourly_rate = $rate_row['hourly_rate'];

$exit_time = date("Y-m-d H:i:s");
$entry_ts = strtotime($entry_time);
$exit_ts = strtotime($exit_time);
$duration_hours = ($exit_ts - $entry_ts) / 3600;

$billable_hours = ceil($duration_hours);
if ($billable_hours < 1) {
    $billable_hours = 1;
}

$amount = $billable_hours * $hourly_rate;

$sql = "UPDATE parking_sessions SET exit_time = ? WHERE session_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $exit_time, $session_id);
$stmt->execute();

$sql = "INSERT INTO payments (session_id, amount, method) VALUES (?, ?, 'cash')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("id", $session_id, $amount);
$stmt->execute();
log_action($conn, $_SESSION['user_id'], "Vehicle checked out", "Plate: $plate_number, Amount: Tk $amount, Method: $payment_method");

$sql = "UPDATE parking_slots SET status = 'available' WHERE slot_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $slot_id);
$stmt->execute();

$message = "Vehicle $plate_number checked out. Duration: $billable_hours hr(s). Amount: Tk $amount";
header("Location: receipt.php?session_id=" . $session_id);
exit();
?>