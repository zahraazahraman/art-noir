<?php
session_start();
header('Content-Type: application/json');

require_once '../DAL.class.php';

try {
    $dal = new DAL();

    // GET: Retrieve messages
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get unread count
        if (isset($_GET['count'])) {
            $sql = "SELECT COUNT(*) as count FROM messages WHERE is_read = 0";
            $result = $dal->getData($sql);
            echo json_encode(['count' => $result[0]['count']]);
        } 
        // Get messages
        else {
            $unreadOnly = isset($_GET['unread']) && $_GET['unread'] === 'true';
            
            if ($unreadOnly) {
                $sql = "SELECT * FROM messages WHERE is_read = 0 ORDER BY created_at DESC";
            } else {
                $sql = "SELECT * FROM messages ORDER BY created_at DESC";
            }
            
            $messages = $dal->getData($sql);
            echo json_encode($messages);
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
                    echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
                    exit;
                }
                
                // Build query based on whether handled_by is provided
                if ($handledBy !== null) {
                    $sql = "UPDATE messages SET is_read = 1, handled_by = ? WHERE id = ?";
                    $result = $dal->executeQueryWithParams($sql, [$handledBy, $id]);
                } else {
                    $sql = "UPDATE messages SET is_read = 1 WHERE id = ?";
                    $result = $dal->executeQueryWithParams($sql, [$id]);
                }
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Message marked as read' : 'Failed to mark as read'
                ]);
                break;
                
            case 'mark_all_read':
                $sql = "UPDATE messages SET is_read = 1";
                $result = $dal->executeQuery($sql);
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'All messages marked as read' : 'Failed to mark all as read'
                ]);
                break;
                
            case 'delete':
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
                    exit;
                }
                
                $sql = "DELETE FROM messages WHERE id = ?";
                $result = $dal->executeQueryWithParams($sql, [$id]);
                
                echo json_encode([
                    'success' => $result,
                    'message' => $result ? 'Message deleted' : 'Failed to delete message'
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
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
