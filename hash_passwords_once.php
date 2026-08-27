<?php
include 'db_connect.php';
include 'audit_log.php';

// Get all users
$result = $conn->query("SELECT user_id, password FROM users");

while ($row = $result->fetch_assoc()) {
    $user_id = $row['user_id'];
    $plain_password = $row['password'];

    // Skip if it's already hashed (hashed passwords are long and start with $2y$)
    if (strpos($plain_password, '$2y$') === 0) {
        continue;
    }

    $hashed = password_hash($plain_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $hashed, $user_id);
    $stmt->execute();

    echo "Updated user_id $user_id<br>";
}

echo "Done. Delete this file now.";
?>