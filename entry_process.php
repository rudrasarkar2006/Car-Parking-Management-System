<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';
include 'send_email.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php?error=Please log in as staff");
    exit();
}

$plate_number = trim($_POST['plate_number']);
$type_id = $_POST['type_id'];
$staff_id = $_SESSION['user_id'];
$owner_id = $_POST['owner_id'];

$sql = "SELECT * FROM vehicles WHERE plate_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $plate_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $vehicle = $result->fetch_assoc();
    $vehicle_id = $vehicle['vehicle_id'];
} else {
    $sql = "INSERT INTO vehicles (plate_number, owner_id, type_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $plate_number, $owner_id, $type_id);
    $stmt->execute();
    $vehicle_id = $stmt->insert_id;
}

// Step 2: Verify the chosen slot is real, matches the vehicle type, and is still available
$slot_id = $_POST['slot_id'];

$sql = "SELECT * FROM parking_slots WHERE slot_id = ? AND type_id = ? AND status = 'available'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $slot_id, $type_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: entry_form.php?error=" . urlencode("That slot is no longer available. Please choose another."));
    exit();
}

$slot = $result->fetch_assoc();

$sql = "INSERT INTO parking_sessions (vehicle_id, slot_id, staff_id, entry_time) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $vehicle_id, $slot_id, $staff_id);
$stmt->execute();
log_action($conn, $staff_id, "Vehicle checked in", "Plate: $plate_number, Slot: {$slot['slot_number']}");

$sql = "UPDATE parking_slots SET status = 'occupied' WHERE slot_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $slot_id);
$stmt->execute();

// Look up the vehicle owner's email to notify them
$owner_sql = "SELECT u.name, u.email FROM users u WHERE u.user_id = ?";
$stmt = $conn->prepare($owner_sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$owner = $stmt->get_result()->fetch_assoc();

if ($owner) {
    $subject = "Your vehicle has been parked - ParkEase";
    $body = "
        <p>Hi {$owner['name']},</p>
        <p>Your vehicle <strong>$plate_number</strong> has been checked in.</p>
        <p><strong>Slot:</strong> {$slot['slot_number']}<br>
           <strong>Time:</strong> " . date("Y-m-d H:i:s") . "</p>
        <p>Thank you for using ParkEase.</p>
    ";
    send_email($owner['email'], $owner['name'], $subject, $body);
}

header("Location: staff_dashboard.php?msg=Vehicle checked in to slot " . urlencode($slot['slot_number']));
exit();
?>