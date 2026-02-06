<?php
session_start();

require_once __DIR__ . "/../Core/Logger.php";

// Initialize logger
Logger::init();

// Capture user info before destroying session
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Log the logout event
if ($userId) {
    Logger::auth('logout', [
        'id' => $userId,
        'name' => $username,
        'email' => $email,
        'role' => $role
    ], true, "User logged out successfully");
} else {
    Logger::warning("Logout attempted without active session", [
        'session_data' => $_SESSION ? 'present' : 'empty',
        'ip_address' => Logger::getClientIP()
    ]);
}

// Destroy all session data
$_SESSION = array();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: ../Login.php');
exit;
?>
