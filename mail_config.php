<?php
// Your Gmail address and the App Password you generated (NOT your real Gmail password)
define('SMTP_EMAIL', 'your_email@gmail.com'); // paste your Gmail address here
define('SMTP_APP_PASSWORD', 'your_16_digit_app_password'); // paste your 16-char app password here, no spaces

// Google OAuth credentials. Create a "Web application" client in Google Cloud
// Console and add http://localhost/parking_system/google_callback.php as an
// authorised redirect URI (adjust the address if this project uses another URL).
// Never commit real OAuth secrets to a public repository.
define('GOOGLE_CLIENT_ID', 'PASTE_YOUR_GOOGLE_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'PASTE_YOUR_GOOGLE_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI', 'http://localhost/parking_system/google_callback.php');
?>
