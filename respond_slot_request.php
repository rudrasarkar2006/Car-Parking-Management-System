<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php?error=Please log in as staff");
    exit();
}

$request_id = $_GET['id'];
$action = $_GET['action']; // 'approve' or 'reject'

$new_status = ($action === 'approve') ? 'approved' : 'rejected';

$stmt = $conn->prepare("UPDATE slot_requests SET status = ?, responded_at = NOW() WHERE request_id = ?");
$stmt->bind_param("si", $new_status, $request_id);
$stmt->execute();
log_action($conn, $_SESSION['user_id'], "Slot request " . $new_status, "Request ID: $request_id");

header("Location: slot_requests.php?msg=" . urlencode("Request " . $new_status));
exit();
?>