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
    $artistType = isset($_GET['artist_type']) ? $_GET['artist_type'] : null;

    // Build filters
    $filters = [];
    
    if ($startDate && $endDate) {
        $filters[] = "aw.created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
    }
    
    if ($artistType) {
        $filters[] = "a.artist_type = '$artistType'";
    }
    
    $whereClause = !empty($filters) ? " WHERE " . implode(" AND ", $filters) : "";

    // Get top artists by artwork count
    $query = "SELECT 
                a.id,
                a.name,
                a.artist_type,
                COUNT(aw.id) as artwork_count
              FROM artists a
              LEFT JOIN artworks aw ON a.id = aw.artist_id" . $whereClause . "
              GROUP BY a.id, a.name, a.artist_type
              HAVING artwork_count > 0
              ORDER BY artwork_count DESC, a.name ASC
              LIMIT 10";
    
    $result = $dal->getData($query);
    
    // Format artist type for display
    foreach ($result as &$artist) {
        $artist['artist_type'] = ucfirst($artist['artist_type']);
        $artist['artwork_count'] = (int)$artist['artwork_count'];
    }
    
    echo json_encode([
        'success' => true,
        'artists' => $result
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
