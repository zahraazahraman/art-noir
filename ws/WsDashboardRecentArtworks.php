<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../DAL.class.php";

try {
    $dal = new DAL();
    
    // Get filter parameters
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

    // Build filters
    $filters = [];
    
    if ($startDate && $endDate) {
        $filters[] = "aw.created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
    }
    
    if ($status) {
        $filters[] = "aw.status = '$status'";
    }
    
    if ($categoryId) {
        $filters[] = "aw.category_id = $categoryId";
    }
    
    $whereClause = !empty($filters) ? " WHERE " . implode(" AND ", $filters) : "";

    // Get recent artworks with artist information
    $query = "SELECT 
                aw.id,
                aw.title,
                aw.image_path,
                aw.status,
                aw.created_at,
                a.name as artist_name
              FROM artworks aw
              INNER JOIN artists a ON aw.artist_id = a.id" . $whereClause . "
              ORDER BY aw.created_at DESC
              LIMIT 10";
    
    $result = $dal->getData($query);
    
    echo json_encode([
        'success' => true,
        'artworks' => $result
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
