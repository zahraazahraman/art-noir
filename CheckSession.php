<?php
// THIS FILE WILL BE INCLUDED IN EVERY FILE THAT NEEDS LOGIN, INSTEAD OF STARTING SESSIONS MANUALLY.

// Session lifetime settings
ini_set('session.gc_maxlifetime', 600);
session_set_cookie_params(600);

session_start();

// Session timeout logic
$timeout_duration = 600;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['redirect' => '../Login.php']);
        exit;
    } else {
        header('Location: ../Login.php');
        exit;
    }
}

// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time();

// CHECKIN LOGIN
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['redirect' => '../Login.php']);
        exit;
    } else {
        header('Location: ../Login.php');
        exit;
    }
}

// ROLE CHEK
function requireAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['redirect' => '../Login.php']);
            exit;
        } else {
            header('Location: ../Login.php');
            exit;
        }
    }
}

function requireUser() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'User') {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['redirect' => '../Login.php']);
            exit;
        } else {
            header('Location: ../Login.php');
            exit;
        }
    }
}
?>