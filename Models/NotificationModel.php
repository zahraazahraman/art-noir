<?php
require_once __DIR__ . "/../DAL.class.php";

class Notification {
    private $_db;

    public function __construct() {
        $this->_db = new DAL();
    }

    public function __destruct() {
        $this->_db = null;
    }

    // Get all notifications (newest first)
    public function getNotifications($unreadOnly = false) {
        $sql = "SELECT * FROM notifications WHERE 1=1";
        
        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY created_at DESC";

        try {
            return $this->_db->getData($sql);
        } catch(Exception $e) {
            throw $e;
        }
    }

    // Get unread count
    public function getUnreadCount() {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE is_read = 0";

        try {
            $result = $this->_db->getData($sql);
            return $result[0]['count'] ?? 0;
        } catch(Exception $e) {
            throw $e;
        }
    }

    // Create notification
    public function createNotification($type, $title, $message, $relatedTable = null, $relatedId = null) {
        try {
            $sql = "INSERT INTO notifications (type, title, message, related_table, related_id) VALUES (?, ?, ?, ?, ?)";
            $params = [$type, $title, $message, $relatedTable, $relatedId];
            return $this->_db->executeQueryWithParams($sql, $params);
        } catch(Exception $e) {
            throw $e;
        }
    }

    // Mark notification as read
    public function markAsRead($id, $handledBy = null) {
        try {
            $sql = "UPDATE notifications SET is_read = 1";
            
            if ($handledBy) {
                $sql .= ", handled_by = $handledBy";
            }
            
            $sql .= " WHERE id = $id";
            
            return $this->_db->executeQuery($sql);
        } catch(Exception $e) {
            throw $e;
        }
    }

    // Mark all as read
    public function markAllAsRead() {
        try {
            $sql = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";
            return $this->_db->executeQuery($sql);
        } catch(Exception $e) {
            throw $e;
        }
    }

    // Delete notification
    public function deleteNotification($id) {
        try {
            $sql = "DELETE FROM notifications WHERE id = $id";
            return $this->_db->executeQuery($sql);
        } catch(Exception $e) {
            throw $e;
        }
    }

    // Get notification by ID
    public function getNotificationById($id) {
        $sql = "SELECT * FROM notifications WHERE id = $id";

        try {
            $result = $this->_db->getData($sql);
            return $result[0] ?? null;
        } catch(Exception $e) {
            throw $e;
        }
    }
}
?>