<?php
header('Content-Type: application/json');

require_once __DIR__ . "/../CheckSession.php";
require_once __DIR__ . "/../Models/UserModel.php";
require_once __DIR__ . "/../Models/ArtistModel.php";

try {
    $userId = $_SESSION['user_id'];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle profile update (name and/or password)
        $userName = isset($_POST['userName']) ? trim($_POST['userName']) : '';
        $currentPassword = isset($_POST['currentPassword']) ? trim($_POST['currentPassword']) : '';
        $newPassword = isset($_POST['newPassword']) ? trim($_POST['newPassword']) : '';

        $userModel = new User();
        $users = $userModel->getUsers();
        $currentUser = null;
        foreach ($users as $user) {
            if ($user['id'] == $userId) {
                $currentUser = $user;
                break;
            }
        }

        if (!$currentUser) {
            echo json_encode([
                'success' => false,
                'message' => 'User not found'
            ]);
            exit;
        }

        $updateData = [];
        $errors = [];

        // Validate name if provided
        if (!empty($userName)) {
            if (strlen($userName) < 2) {
                $errors[] = 'Name must be at least 2 characters';
            } else {
                $updateData['name'] = $userName;
            }
        }

        // Handle password change if provided
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                $errors[] = 'Current password is required to change password';
            } elseif (!password_verify($currentPassword, $currentUser['password'])) {
                $errors[] = 'Current password is incorrect';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters';
            } else {
                $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }
        }

        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'message' => implode(', ', $errors)
            ]);
            exit;
        }

        if (empty($updateData)) {
            echo json_encode([
                'success' => false,
                'message' => 'No changes to update'
            ]);
            exit;
        }

        // Update user information
        $result = $userModel->updateUser($userId, $updateData);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update profile'
            ]);
        }
    } elseif (isset($_GET['getArtist'])) {
        $artistModel = new Artist();
        $artists = $artistModel->getArtists();
        
        // Find artist linked to this user
        $userArtist = null;
        foreach ($artists as $artist) {
            if ($artist['user_id'] == $userId) {
                $userArtist = $artist;
                break;
            }
        }
        
        if ($userArtist) {
            echo json_encode([
                'success' => true,
                'artist' => $userArtist
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No artist profile found'
            ]);
        }
    } else {
        // Return user information
        $userModel = new User();
        $users = $userModel->getUsers();
        
        // Find current user
        $currentUser = null;
        foreach ($users as $user) {
            if ($user['id'] == $userId) {
                $currentUser = $user;
                break;
            }
        }
        
        if ($currentUser) {
            // Remove password from response
            unset($currentUser['password']);
            
            echo json_encode([
                'success' => true,
                'user' => $currentUser
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'User not found'
            ]);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>