<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../Models/NotificationModel.php";

try {
    $notification = new Notification();
    
    // GET: Retrieve notifications
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get unread count
        if (isset($_GET['count'])) {
            $count = $notification->getUnreadCount();
            echo json_encode(['count' => $count]);
        } 
        // Get all notifications
        else {
            $unreadOnly = isset($_GET['unread']) && $_GET['unread'] === 'true';
            $notifications = $notification->getNotifications($unreadOnly);
            echo json_encode($notifications);
        }
    }
    
    // POST: Mark as read or delete
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        
        switch ($action) {
            case 'mark_read':
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $handledBy = isset($_POST['handled_by']) ? intval($_POST['handled_by']) : null;
                
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
                    exit;
                }
                
                $result = $notification->markAsRead($id, $handledBy);
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Notification marked as read' : 'Failed to mark as read'
                ]);
                break;
                
            case 'mark_all_read':
                $result = $notification->markAllAsRead();
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'All notifications marked as read' : 'Failed to mark all as read'
                ]);
                break;
                
            case 'delete':
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
                    exit;
                }
                
                $result = $notification->deleteNotification($id);
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Notification deleted' : 'Failed to delete notification'
                ]);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    }
    
    else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>