<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../DAL.class.php";

try {
    $dal = new DAL();
    
    // Get filter parameters
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    
    // Build date filter
    $dateFilter = "";
    if ($startDate && $endDate) {
        $dateFilter = " AND created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
    }
    
    $activities = [];
    
    // Build query based on filter
    switch ($filter) {
        case 'artworks':
            // Get recent artwork activities
            $artworkDateFilter = $dateFilter ? " WHERE aw.created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'" : "";
            $query = "SELECT 
                        'artwork' as type,
                        CONCAT('New Artwork: ', aw.title) as title,
                        CONCAT('Added by ', a.name) as description,
                        aw.created_at as activity_time
                      FROM artworks aw
                      INNER JOIN artists a ON aw.artist_id = a.id" . $artworkDateFilter . "
                      ORDER BY aw.created_at DESC
                      LIMIT 20";
            break;
            
        case 'users':
            // Get recent user activities
            $userDateFilter = $dateFilter ? " WHERE created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'" : "";
            $query = "SELECT 
                        'user' as type,
                        CONCAT('New User: ', u.name) as title,
                        CONCAT('Registered as ', u.role) as description,
                        u.created_at as activity_time
                      FROM users u" . $userDateFilter . "
                      ORDER BY u.created_at DESC
                      LIMIT 20";
            break;
            
        case 'artists':
            // Get recent artist activities
            $query = "SELECT 
                        'artist' as type,
                        CONCAT('Artist: ', a.name) as title,
                        CONCAT('Type: ', UPPER(SUBSTRING(a.artist_type, 1, 1)), LOWER(SUBSTRING(a.artist_type, 2))) as description,
                        a.created_at as activity_time
                      FROM artists a
                      ORDER BY a.id DESC
                      LIMIT 20";
            break;
            
        default: // 'all'
            // Combine all activities using UNION
            $artworkDateFilter = $startDate && $endDate ? " WHERE aw.created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'" : "";
            $userDateFilter = $startDate && $endDate ? " WHERE u.created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'" : "";
            $artistDateFilter = $startDate && $endDate ? " WHERE a.created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'" : "";

            $query = "
                SELECT 
                    'artwork' as type,
                    CONCAT('New Artwork: ', aw.title) as title,
                    CONCAT('Added by ', a.name) as description,
                    aw.created_at as activity_time
                FROM artworks aw
                INNER JOIN artists a ON aw.artist_id = a.id" . $artworkDateFilter . "
                
                UNION ALL
                
                SELECT 
                    'user' as type,
                    CONCAT('New User: ', u.name) as title,
                    CONCAT('Registered as ', u.role) as description,
                    u.created_at as activity_time
                FROM users u" . $userDateFilter . "
                
                UNION ALL
                
                SELECT 
                    'artist' as type,
                    CONCAT('New Artist: ', a.name) as title,
                    CONCAT('Type: ', UPPER(SUBSTRING(a.artist_type, 1, 1)), LOWER(SUBSTRING(a.artist_type, 2))) as description,
                    a.created_at as activity_time
                FROM artists a" . $artistDateFilter . "
                
                ORDER BY activity_time DESC
                LIMIT 20";
            break;
    }
    
    $result = $dal->getData($query);
    
    // Calculate time ago for each activity
    foreach ($result as &$activity) {
        $activity['time_ago'] = getTimeAgo($activity['activity_time']);
    }
    
    echo json_encode([
        'success' => true,
        'activities' => $result
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

// Helper function to calculate time ago
function getTimeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    } else {
        $years = floor($diff / 31536000);
        return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
    }
}
?>
