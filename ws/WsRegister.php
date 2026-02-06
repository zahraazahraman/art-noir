<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../Models/UserModel.php";
require_once __DIR__ . "/../Core/Logger.php";

// Initialize logger
Logger::init();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $role = isset($_POST['role']) ? trim($_POST['role']) : '';

        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            Logger::warning("Registration attempt with missing fields", [
                'name' => $name ?: 'not_provided',
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
            Logger::warning("Registration attempt with invalid email format", [
                'email' => $email,
                'ip_address' => Logger::getClientIP()
            ]);
            
            echo json_encode([
                'success' => false,
                'message' => 'Invalid email format'
            ]);
            exit;
        }

        // Validate password length
        if (strlen($password) < 6) {
            Logger::warning("Registration attempt with weak password", [
                'email' => $email,
                'ip_address' => Logger::getClientIP()
            ]);
            
            echo json_encode([
                'success' => false,
                'message' => 'Password must be at least 6 characters'
            ]);
            exit;
        }

        // Validate role
        if ($role !== 'User') {
            Logger::warning("Registration attempt with invalid role", [
                'email' => $email,
                'role' => $role,
                'ip_address' => Logger::getClientIP()
            ]);
            
            echo json_encode([
                'success' => false,
                'message' => 'Invalid role selected'
            ]);
            exit;
        }

        // Register user
        $user = new User();
        try {
            $result = $user->addUser($name, $email, $password, $role, 'Active');
            if ($result) {
                // Log successful registration
                Logger::auth('register', [
                    'id' => $result,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ], true, "New user registered successfully");
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Account created successfully'
                ]);
            } else {
                Logger::error("Registration failed - database error", [
                    'email' => $email,
                    'name' => $name,
                    'role' => $role
                ]);
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create account. Please try again.'
                ]);
            }
        } catch (Exception $e) {
            Logger::exception($e, "Registration error occurred", [
                'email' => $email,
                'name' => $name
            ]);
            
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    } else {
        Logger::warning("Invalid request method for registration", [
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
    Logger::exception($e, "Unexpected error during registration");
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
