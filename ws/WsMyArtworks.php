<?php
header('Content-Type: application/json');

require_once __DIR__ . "/../CheckSession.php";
require_once __DIR__ . "/../Models/ArtworkModel.php";
require_once __DIR__ . "/../Models/ArtistModel.php";

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

try {
    $userId = $_SESSION['user_id'];
    
    // First, find the artist ID for this user
    $artistModel = new Artist();
    $artists = $artistModel->getArtists();
    
    $artistId = null;
    foreach ($artists as $artist) {
        if ($artist['user_id'] == $userId) {
            $artistId = $artist['id'];
            break;
        }
    }
    
    if (!$artistId) {
        echo json_encode([
            'success' => false,
            'message' => 'No artist profile found'
        ]);
        exit;
    }
    
    // Get artworks for this artist
    $artworkModel = new Artwork();
    $allArtworks = $artworkModel->getArtworks();
    
    // Filter artworks by artist_id
    $userArtworks = array_filter($allArtworks, function($artwork) use ($artistId) {
        return $artwork['artist_id'] == $artistId;
    });
    
    // Reset array keys
    $userArtworks = array_values($userArtworks);
    
    echo json_encode([
        'success' => true,
        'artworks' => $userArtworks
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>