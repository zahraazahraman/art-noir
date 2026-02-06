<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../CheckSession.php";

require_once __DIR__ . "/../Models/ArtistModel.php";

try {
    $artist = new Artist();
    
    // For now: Only handle POST requests (Create)
    // Future: Could add GET to fetch artist info, PUT to update, DELETE to remove (allowing more user controls)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        
        if ($action === 'create') {
            // Get user ID from session
            $user_id = $_SESSION['user_id'];
            
            // Check if user already has an artist profile
            $checkQuery = "SELECT id FROM artists WHERE user_id = $user_id";
            require_once __DIR__ . "/../DAL.class.php";
            $dal = new DAL();
            $existing = $dal->getData($checkQuery);
            
            if ($existing && count($existing) > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'You already have an artist profile.'
                ]);
                exit;
            }
            
            // Validate input
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $biography = isset($_POST['biography']) ? trim($_POST['biography']) : '';
            $country_id = isset($_POST['country_id']) ? intval($_POST['country_id']) : 0;
            $birth_year = isset($_POST['birth_year']) ? trim($_POST['birth_year']) : null;
            $death_year = isset($_POST['death_year']) && $_POST['death_year'] !== '' ? trim($_POST['death_year']) : null;
            $artist_type = isset($_POST['artist_type']) ? trim($_POST['artist_type']) : 'community';
            
            // Validation
            if (empty($name) || empty($biography) || $country_id <= 0 || empty($birth_year)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please fill in all required fields.'
                ]);
                exit;
            }
            
            if (strlen($name) < 2 || strlen($name) > 100) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Artist name must be between 2 and 100 characters.'
                ]);
                exit;
            }
            
            if (strlen($biography) < 50 || strlen($biography) > 1000) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Biography must be between 50 and 1000 characters.'
                ]);
                exit;
            }
            
            // Create the artist profile
            $artistId = $artist->addArtist($name, $biography, $country_id, $birth_year, $death_year, $artist_type, $user_id);
            
            if ($artistId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Artist profile created successfully! You can now submit artworks.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create artist profile. Please try again.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action.'
            ]);
        }
    } else {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed.'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
