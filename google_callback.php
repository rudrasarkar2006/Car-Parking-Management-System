<?php
session_start();
require_once 'db_connect.php';
require_once 'mail_config.php';
require_once 'audit_log.php';

function google_error($message) {
    header('Location: login.php?error=' . urlencode($message));
    exit();
}

if (isset($_GET['error'])) {
    google_error('Google sign-in was cancelled.');
}

if (!isset($_GET['code'], $_GET['state']) || !isset($_SESSION['google_oauth_state']) || !hash_equals($_SESSION['google_oauth_state'], $_GET['state'])) {
    unset($_SESSION['google_oauth_state']);
    google_error('Google sign-in could not be verified. Please try again.');
}
unset($_SESSION['google_oauth_state']);

$tokenFields = http_build_query([
    'code' => $_GET['code'],
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
]);

$curl = curl_init('https://oauth2.googleapis.com/token');
curl_setopt_array($curl, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $tokenFields,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_TIMEOUT => 15
]);
$tokenResponse = curl_exec($curl);
$httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$token = json_decode($tokenResponse, true);

if ($httpStatus !== 200 || empty($token['access_token'])) {
    google_error('Unable to complete Google sign-in. Please try again.');
}

$curl = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token['access_token']],
    CURLOPT_TIMEOUT => 15
]);
$profileResponse = curl_exec($curl);
$httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$profile = json_decode($profileResponse, true);

if ($httpStatus !== 200 || empty($profile['email']) || empty($profile['email_verified'])) {
    google_error('Google did not provide a verified email address.');
}

$email = strtolower(trim($profile['email']));
$name = trim($profile['name'] ?? strstr($email, '@', true));
$lookup = $conn->prepare('SELECT user_id, name, role FROM users WHERE email = ?');
$lookup->bind_param('s', $email);
$lookup->execute();
$user = $lookup->get_result()->fetch_assoc();

if (!$user) {
    // A random password prevents password login until the user explicitly sets one.
    $generatedPassword = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
    $insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
    $insert->bind_param('sss', $name, $email, $generatedPassword);
    if (!$insert->execute()) {
        google_error('Unable to create your account. Please try again.');
    }
    $user = ['user_id' => $conn->insert_id, 'name' => $name, 'role' => 'customer'];
    log_action($conn, $user['user_id'], 'Account created with Google', 'Email: ' . $email);
}

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['name'] = $user['name'];
$_SESSION['role'] = $user['role'];
log_action($conn, $user['user_id'], 'Signed in with Google', 'Email: ' . $email);

if ($user['role'] === 'admin') {
    header('Location: admin_dashboard.php');
} elseif ($user['role'] === 'staff') {
    header('Location: staff_dashboard.php');
} else {
    header('Location: customer_dashboard.php');
}
exit();
?>
