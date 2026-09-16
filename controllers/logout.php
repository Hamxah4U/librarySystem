<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Unset all session variables
$_SESSION = [];

// 2. Delete session cookie if present
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Destroy session
session_destroy();

// 4. Start fresh session for flash notification
session_start();
$_SESSION['flash_msg']  = "You have been logged out successfully.";
$_SESSION['flash_type'] = "info";

// 5. Redirect to login landing page
header('Location: /');
exit();