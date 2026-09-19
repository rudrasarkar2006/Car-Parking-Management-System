<?php
session_start();
require_once 'mail_config.php';

if (GOOGLE_CLIENT_ID === 'PASTE_YOUR_GOOGLE_CLIENT_ID_HERE' || GOOGLE_CLIENT_SECRET === 'PASTE_YOUR_GOOGLE_CLIENT_SECRET_HERE') {
    header('Location: login.php?error=' . urlencode('Google sign-in has not been configured yet.'));
    exit();
}

$_SESSION['google_oauth_state'] = bin2hex(random_bytes(32));
$parameters = [
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'state' => $_SESSION['google_oauth_state'],
    'prompt' => 'select_account'
];

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($parameters, '', '&', PHP_QUERY_RFC3986));
exit();
?>
