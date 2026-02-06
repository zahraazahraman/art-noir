<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../Models/UserModel.php";
require_once __DIR__ . "/../Core/Logger.php";

// Initialize logger
Logger::init();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $role = isset($_POST['role']) ? trim($_POST['role']) : '';
        
        // Validation
        if (empty($email) || empty($password) || empty($role)) {
            Logger::warning("Login attempt with missing fields", [
                'email' => $email ?: 'not_provided',
                'role' => $role ?: 'not_provided',
                'ip_address' => Logger::getClientIP()
            ]);
            
            echo json_encode([
                'success' => false,
                'message' => 'Please fill in all fields'
            ]);
            exit;
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Logger::warning("Login attempt with invalid email format", [
                'email' => $email,
                'ip_address' => Logger::getClientIP()
            ]);
            
            echo json_encode([
                'success' => false,
                'message' => 'Invalid email format'
            ]);
            exit;
        }
        
        // Check credentials
        $user = new User();
        $result = $user->checkLogin($email, $password, $role);
        
        if ($result) {
            // Login successful - create session
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['username'] = $result['name'];
            $_SESSION['email'] = $result['email'];
            $_SESSION['role'] = $result['role'];
            $_SESSION['logged_in'] = true;
            
            // Log successful login
            Logger::auth('login', [
                'id' => $result['id'],
                'name' => $result['name'],
                'email' => $result['email'],
                'role' => $result['role']
            ], true);
            
            // Determine redirect based on role
            $redirect = ($role === 'Admin') ? 'admin/index.php' : 'public/index.php';
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $redirect,
                'user' => [
                    'name' => $result['name'],
                    'email' => $result['email'],
                    'role' => $result['role']
                ]
            ]);
        } else {
            // Log failed login attempt
            Logger::auth('failed_login', [
                'email' => $email,
                'role' => $role
            ], false, "Login failed - invalid credentials");
            
            echo json_encode([
                'success' => false,
                'message' => 'Invalid email, password, or role.'
            ]);
        }
    } else {
        Logger::warning("Invalid request method attempted", [
            'method' => $_SERVER['REQUEST_METHOD'],
            'ip_address' => Logger::getClientIP()
        ]);
        
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
    }
} catch (Exception $e) {
    Logger::exception($e, "Login error occurred", [
        'email' => $email ?? 'not_provided'
    ]);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
