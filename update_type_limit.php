<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Please log in as admin");
    exit();
}

$type_id = $_POST['type_id'];
$max_hours = $_POST['max_hours'];

$stmt = $conn->prepare("UPDATE vehicle_types SET max_hours = ? WHERE type_id = ?");
$stmt->bind_param("ii", $max_hours, $type_id);
$stmt->execute();

header("Location: manage_slots.php?msg=Time limit updated");
exit();
?>