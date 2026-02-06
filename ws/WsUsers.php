<?php 
session_start();
header("Access-Control-Allow-Origin: *"); // this line allows CORS: Cross-Origin Resource Sharing
require_once "../Models/UserModel.php";
require_once __DIR__ . "/../Core/Logger.php";

// Initialize logger
Logger::init();

try {
$user = new User();

// Handle GET requests (Read/Search)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = isset($_GET['search']) ? $_GET['search'] : null;
    $role = isset($_GET['role']) ? $_GET['role'] : null;
    $state = isset($_GET['state']) ? $_GET['state'] : null;
    $users = $user->getUsers($search, $role, $state);
    
    Logger::info("Users fetched", [
        'search' => $search,
        'role' => $role,
        'state' => $state,
        'count' => count($users)
    ]);
    
    echo json_encode($users);
}

// Handle POST requests (Create/Update/Delete)
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'add':
            // Add new user
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $role = isset($_POST['role']) ? trim($_POST['role']) : '';
            $state = isset($_POST['state']) ? trim($_POST['state']) : '';
            
            // Validation
            if (empty($name) || empty($email) || empty($password) || empty($role) || empty($state)) {
                Logger::warning("User add failed - missing fields", [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ]);
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Please fill in all required fields'
                ]);
                exit;
            }
            
            try {
                $result = $user->addUser($name, $email, $password, $role, $state);
                
                if ($result) {
                    Logger::info("User added successfully", [
                        'user_id' => $result,
                        'name' => $name,
                        'email' => $email,
                        'role' => $role,
                        'state' => $state
                    ]);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'User added successfully'
                    ]);
                } else {
                    Logger::error("User add failed - database error", [
                        'name' => $name,
                        'email' => $email,
                        'role' => $role
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to add User'
                    ]);
                }
            } catch (Exception $e) {
                Logger::exception($e, "User add error", [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ]);
                
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;
            
        case 'update':
            // Update existing user
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $role = isset($_POST['role']) ? trim($_POST['role']) : '';
            $state = isset($_POST['state']) ? trim($_POST['state']) : '';
            
            // Validation
            if ($id <= 0) {
                Logger::warning("User update failed - invalid ID", [
                    'user_id' => $id
                ]);
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid user ID'
                ]);
                exit;
            }
            
            if (empty($name) || empty($email) || empty($role) || empty($state)) {
                Logger::warning("User update failed - missing fields", [
                    'user_id' => $id,
                    'name' => $name
                ]);
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Please fill in all required fields'
                ]);
                exit;
            }
            
            try {
                $updateData = [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'state' => $state
                ];
                
                $result = $user->updateUser($id, $updateData);
                
                if ($result) {
                    Logger::info("User updated successfully", [
                        'user_id' => $id,
                        'name' => $name,
                        'email' => $email,
                        'role' => $role,
                        'state' => $state
                    ]);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'User updated successfully'
                    ]);
                } else {
                    Logger::error("User update failed - database error", [
                        'user_id' => $id,
                        'name' => $name
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update user'
                    ]);
                }
            } catch (Exception $e) {
                Logger::exception($e, "User update error", [
                    'user_id' => $id,
                    'name' => $name
                ]);
                
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;
            
        case 'delete':
            // Delete user
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            if ($id <= 0) {
                Logger::warning("User delete failed - invalid ID", [
                    'user_id' => $id
                ]);
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid user ID'
                ]);
                exit;
            }
            
            try {
                $result = $user->deleteUser($id);
                
                if ($result) {
                    Logger::info("User deleted successfully", [
                        'user_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'User deleted successfully'
                    ]);
                } else {
                    Logger::error("User delete failed - database error", [
                        'user_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to delete user'
                    ]);
                }
            } catch (Exception $e) {
                Logger::exception($e, "User delete error", [
                    'user_id' => $id
                ]);
                
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;
            
        default:
            Logger::warning("User action failed - invalid action", [
                'action' => $action
            ]);
            
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            break;
    }
}

// Handle unsupported methods
else {
    Logger::warning("Users API - invalid request method", [
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
    
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}

} catch (Exception $e) {
Logger::exception($e, "Users API unexpected error");

http_response_code(500);
echo json_encode([
    'success' => false,
    'message' => 'Server error: ' . $e->getMessage()
]);
}
?>
