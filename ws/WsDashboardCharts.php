<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../DAL.class.php";

try {
    $dal = new DAL();
    
    // Get parameters
    $chartType = isset($_GET['chart']) ? $_GET['chart'] : '';
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    $categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
    $artistType = isset($_GET['artist_type']) ? $_GET['artist_type'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    
    // Build common WHERE clauses
    $artworkWhere = [];
    $artistWhere = [];
    $userWhere = [];
    
    if ($startDate && $endDate) {
        $artworkWhere[] = "a.created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
        $artistWhere[] = "artists.created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
        $userWhere[] = "users.created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
    }
    
    if ($categoryId && $categoryId > 0) {
        $artworkWhere[] = "a.category_id = $categoryId";
    }
    
    if ($artistType && $artistType !== 'all') {
        $artworkWhere[] = "a.artist_id IN (SELECT id FROM artists WHERE artist_type = '$artistType')";
    }
    
    if ($status && $status !== 'all') {
        $artworkWhere[] = "a.status = '$status'";
    }
    
    $artworkWhereClause = $artworkWhere ? ' WHERE ' . implode(' AND ', $artworkWhere) : '';
    $artistWhereClause = $artistWhere ? ' WHERE ' . implode(' AND ', $artistWhere) : '';
    $userWhereClause = $userWhere ? ' WHERE ' . implode(' AND ', $userWhere) : '';
    
    switch ($chartType) {
        case 'artwork_status':
            // Get artwork counts by status
            $query = "SELECT 
                        SUM(CASE WHEN a.status = 'Approved' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN a.status = 'Pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN a.status = 'Rejected' THEN 1 ELSE 0 END) as rejected
                      FROM artworks a" . $artworkWhereClause;
            $result = $dal->getData($query);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'approved' => (int)$result[0]['approved'],
                    'pending' => (int)$result[0]['pending'],
                    'rejected' => (int)$result[0]['rejected']
                ]
            ]);
            break;
            
        case 'categories':
            // Get artwork counts by category
            $query = "SELECT c.name as category, COUNT(a.id) as count
                      FROM categories c
                      LEFT JOIN artworks a ON c.id = a.category_id" . $artworkWhereClause . "
                      GROUP BY c.id, c.name
                      ORDER BY count DESC";
            $result = $dal->getData($query);
            
            $labels = [];
            $values = [];
            
            foreach ($result as $row) {
                $labels[] = $row['category'];
                $values[] = (int)$row['count'];
            }
            
            echo json_encode([
                'success' => true,
                'labels' => $labels,
                'values' => $values
            ]);
            break;
            
        case 'artist_types':
            // Get artist counts by type
            $query = "SELECT 
                        SUM(CASE WHEN artist_type = 'historical' THEN 1 ELSE 0 END) as historical,
                        SUM(CASE WHEN artist_type = 'community' THEN 1 ELSE 0 END) as community
                      FROM artists" . $artistWhereClause;
            $result = $dal->getData($query);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'historical' => (int)$result[0]['historical'],
                    'community' => (int)$result[0]['community']
                ]
            ]);
            break;
            
        case 'user_roles':
            // Get user counts by role
            $query = "SELECT 
                        SUM(CASE WHEN role = 'Admin' THEN 1 ELSE 0 END) as admin,
                        SUM(CASE WHEN role = 'User' THEN 1 ELSE 0 END) as user
                      FROM users" . $userWhereClause;
            $result = $dal->getData($query);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'admin' => (int)$result[0]['admin'],
                    'user' => (int)$result[0]['user']
                ]
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid chart type'
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
