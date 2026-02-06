<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../DAL.class.php";

try {
    $dal = new DAL();
    
    // Test connection
    if (!$dal) {
        throw new Exception("DAL initialization failed");
    }
    
    // Get filter parameters
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    $comparison = isset($_GET['comparison']) && $_GET['comparison'] === 'true';
    $categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
    $artistType = isset($_GET['artist_type']) ? $_GET['artist_type'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;

    // Build filters
    $userFilters = [];
    $artistFilters = [];
    $artworkFilters = [];

    if ($startDate && $endDate) {
        $userFilters[] = "created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
        $artistFilters[] = "created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
        $artworkFilters[] = "created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
    }

    if ($artistType && $artistType !== 'all') {
        $artistFilters[] = "artist_type = '$artistType'";
        $artworkFilters[] = "artist_id IN (SELECT id FROM artists WHERE artist_type = '$artistType')";
    }

    if ($categoryId && $categoryId > 0) {
        $artworkFilters[] = "category_id = $categoryId";
    }

    if ($status && $status !== 'all') {
        $artworkFilters[] = "status = '$status'";
    }

    $userWhere = $userFilters ? " WHERE " . implode(" AND ", $userFilters) : "";
    $artistWhere = $artistFilters ? " WHERE " . implode(" AND ", $artistFilters) : "";
    $artworkWhere = $artworkFilters ? " WHERE " . implode(" AND ", $artworkFilters) : "";
    $pendingWhere = $artworkWhere ? $artworkWhere . " AND status = 'Pending'" : " WHERE status = 'Pending'";

    // Get total users
    $totalUsersQuery = "SELECT COUNT(*) as count FROM users" . $userWhere;
    $totalUsersResult = $dal->getData($totalUsersQuery);
    $totalUsers = $totalUsersResult[0]['count'];
    
    // Get total artists
    $totalArtistsQuery = "SELECT COUNT(*) as count FROM artists" . $artistWhere;
    $totalArtistsResult = $dal->getData($totalArtistsQuery);
    $totalArtists = $totalArtistsResult[0]['count'];
    
    // Get total artworks
    $totalArtworksQuery = "SELECT COUNT(*) as count FROM artworks" . $artworkWhere;
    $totalArtworksResult = $dal->getData($totalArtworksQuery);
    $totalArtworks = $totalArtworksResult[0]['count'];
    
    // Get pending artworks
    $pendingArtworksQuery = "SELECT COUNT(*) as count FROM artworks" . $pendingWhere;
    $pendingArtworksResult = $dal->getData($pendingArtworksQuery);
    $pendingArtworks = $pendingArtworksResult[0]['count'];
    
    $response = [
        'success' => true,
        'stats' => [
            'totalUsers' => $totalUsers,
            'totalArtists' => $totalArtists,
            'totalArtworks' => $totalArtworks,
            'pendingArtworks' => $pendingArtworks
        ]
    ];

    // Calculate comparison if requested
    if ($comparison && $startDate && $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $diff = $start->diff($end)->days;
        
        // Calculate previous period dates
        $prevEnd = clone $start;
        $prevEnd->modify('-1 day');
        $prevStart = clone $prevEnd;
        $prevStart->modify("-$diff days");
        
        $prevStartStr = $prevStart->format('Y-m-d');
        $prevEndStr = $prevEnd->format('Y-m-d');
        
        // Get previous period stats
        $prevUserWhere = str_replace("$startDate 00:00:00' AND '$endDate 23:59:59", "$prevStartStr 00:00:00' AND '$prevEndStr 23:59:59", $userWhere);
        $prevUsersQuery = "SELECT COUNT(*) as count FROM users" . $prevUserWhere;
        $prevUsersResult = $dal->getData($prevUsersQuery);
        $prevUsers = $prevUsersResult[0]['count'];
        
        $prevArtworkWhere = str_replace("$startDate 00:00:00' AND '$endDate 23:59:59", "$prevStartStr 00:00:00' AND '$prevEndStr 23:59:59", $artworkWhere);
        $prevArtworksQuery = "SELECT COUNT(*) as count FROM artworks" . $prevArtworkWhere;
        $prevArtworksResult = $dal->getData($prevArtworksQuery);
        $prevArtworks = $prevArtworksResult[0]['count'];
        
        $prevArtistWhere = str_replace("$startDate 00:00:00' AND '$endDate 23:59:59", "$prevStartStr 00:00:00' AND '$prevEndStr 23:59:59", $artistWhere);
        $prevArtistsQuery = "SELECT COUNT(*) as count FROM artists" . $prevArtistWhere;
        $prevArtistsResult = $dal->getData($prevArtistsQuery);
        $prevArtists = $prevArtistsResult[0]['count'];
        
        // Calculate percentage changes
        $usersChange = $prevUsers > 0 ? round((($totalUsers - $prevUsers) / $prevUsers) * 100, 1) : ($totalUsers > 0 ? 100 : 0);
        $artistsChange = $prevArtists > 0 ? round((($totalArtists - $prevArtists) / $prevArtists) * 100, 1) : ($totalArtists > 0 ? 100 : 0);
        $artworksChange = $prevArtworks > 0 ? round((($totalArtworks - $prevArtworks) / $prevArtworks) * 100, 1) : ($totalArtworks > 0 ? 100 : 0);
        
        $response['comparison'] = [
            'usersChange' => $usersChange,
            'artistsChange' => $artistsChange,
            'artworksChange' => $artworksChange
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
