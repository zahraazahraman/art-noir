<?php
header('Content-Type: application/json');
require_once '../Models/ArtworkModel.php';

try {
    $artworkModel = new Artwork();
    
    // Get all artworks with artist and category information
    $artworks = $artworkModel->getApprovedArtworks();
    
    echo json_encode($artworks);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to retrieve artworks',
        'message' => $e->getMessage()
    ]);
}
?>