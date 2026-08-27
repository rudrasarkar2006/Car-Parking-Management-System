<?php
session_start();
include 'db_connect.php';
include 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Please log in as admin");
    exit();
}

$active = 'users';
$target_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $target_id);
$stmt->execute();
$target = $stmt->get_result()->fetch_assoc();

if (!$target) {
    header("Location: users.php?error=User not found");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Check the new email isn't already used by someone ELSE
    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
    $check->bind_param("si", $email, $target_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        header("Location: edit_user.php?id=$target_id&error=" . urlencode("That email is already used by another account"));
        exit();
    }

    $update = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE user_id = ?");
    $update->bind_param("sssi", $name, $email, $role, $target_id);
    $update->execute();
    log_action($conn, $_SESSION['user_id'], "User updated", "Edited user: $name ($email)");

    header("Location: users.php?msg=User updated");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User - ParkEase</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <p class="page-title">Edit user</p>
        <p class="page-subtitle">Update account details</p>

        <?php if (isset($_GET['error'])) { ?>
            <p class="error-msg"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <div style="max-width:400px;">
            <form method="POST">
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($target['name']); ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($target['email']); ?>" required>

                <label>Role</label>
                <select name="role" required>
                    <option value="customer" <?php echo $target['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                    <option value="staff" <?php echo $target['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                    <option value="admin" <?php echo $target['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>