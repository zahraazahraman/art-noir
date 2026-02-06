<?php
header('Content-Type: application/json');

require_once __DIR__ . "/../CheckSession.php";
require_once __DIR__ . "/../Models/ArtistModel.php";
require_once __DIR__ . "/../Core/Logger.php";

// Initialize logger
Logger::init();

try {
    $artist = new Artist();
    
    // Handle GET requests (Read/Search)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $search = isset($_GET['search']) ? $_GET['search'] : null;
        $artistType = isset($_GET['artist_type']) ? $_GET['artist_type'] : null;
        
        $artists = $artist->getArtists($search, $artistType);
        
        Logger::info("Artists fetched", [
            'search' => $search,
            'artist_type' => $artistType,
            'count' => count($artists)
        ]);
        
        echo json_encode($artists);
    }
    
    // Handle POST requests (Create/Update/Delete)
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        
        switch ($action) {
            case 'add':
                // Add new artist
                $name = isset($_POST['name']) ? trim($_POST['name']) : '';
                $biography = isset($_POST['biography']) ? trim($_POST['biography']) : '';
                $countryId = isset($_POST['country_id']) ? intval($_POST['country_id']) : 0;
                $birthYear = isset($_POST['birth_year']) && $_POST['birth_year'] !== '' ? trim($_POST['birth_year']) : null;
                $deathYear = isset($_POST['death_year']) && $_POST['death_year'] !== '' ? trim($_POST['death_year']) : null;
                $artistType = isset($_POST['artist_type']) ? trim($_POST['artist_type']) : '';
                
                // Validation
                if (empty($name) || empty($biography) || $countryId <= 0 || empty($artistType)) {
                    Logger::warning("Artist add failed - missing fields", [
                        'name' => $name,
                        'country_id' => $countryId,
                        'artist_type' => $artistType
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Please fill in all required fields'
                    ]);
                    exit;
                }
                
                try {
                    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
                    $result = $artist->addArtist($name, $biography, $countryId, $birthYear, $deathYear, $artistType, $userId);
                    
                    if ($result) {
                        Logger::info("Artist added successfully", [
                            'artist_id' => $result,
                            'name' => $name,
                            'artist_type' => $artistType
                        ]);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Artist added successfully'
                        ]);
                    } else {
                        Logger::error("Artist add failed - database error", [
                            'name' => $name,
                            'artist_type' => $artistType
                        ]);
                        
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to add artist'
                        ]);
                    }
                } catch (Exception $e) {
                    Logger::exception($e, "Artist add error", [
                        'name' => $name,
                        'artist_type' => $artistType
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
                break;
                
            case 'update':
                // Update existing artist
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $name = isset($_POST['name']) ? trim($_POST['name']) : '';
                $biography = isset($_POST['biography']) ? trim($_POST['biography']) : '';
                $countryId = isset($_POST['country_id']) ? intval($_POST['country_id']) : 0;
                $birthYear = isset($_POST['birth_year']) && $_POST['birth_year'] !== '' ? trim($_POST['birth_year']) : null;
                $deathYear = isset($_POST['death_year']) && $_POST['death_year'] !== '' ? trim($_POST['death_year']) : null;
                $artistType = isset($_POST['artist_type']) ? trim($_POST['artist_type']) : '';
                
                // Validation
                if ($id <= 0) {
                    Logger::warning("Artist update failed - invalid ID", [
                        'artist_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid artist ID'
                    ]);
                    exit;
                }
                
                if (empty($name) || empty($biography) || $countryId <= 0 || empty($artistType)) {
                    Logger::warning("Artist update failed - missing fields", [
                        'artist_id' => $id,
                        'name' => $name
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Please fill in all required fields'
                    ]);
                    exit;
                }
                
                try {
                    $result = $artist->updateArtist($id, $name, $biography, $countryId, $birthYear, $deathYear, $artistType);
                    
                    if ($result) {
                        Logger::info("Artist updated successfully", [
                            'artist_id' => $id,
                            'name' => $name,
                            'artist_type' => $artistType
                        ]);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Artist updated successfully'
                        ]);
                    } else {
                        Logger::error("Artist update failed - database error", [
                            'artist_id' => $id,
                            'name' => $name
                        ]);
                        
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to update artist'
                        ]);
                    }
                } catch (Exception $e) {
                    Logger::exception($e, "Artist update error", [
                        'artist_id' => $id,
                        'name' => $name
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
                break;
                
            case 'delete':
                // Delete artist
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                
                if ($id <= 0) {
                    Logger::warning("Artist delete failed - invalid ID", [
                        'artist_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid artist ID'
                    ]);
                    exit;
                }
                
                try {
                    $result = $artist->deleteArtist($id);
                    
                    if ($result) {
                        Logger::info("Artist deleted successfully", [
                            'artist_id' => $id
                        ]);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Artist deleted successfully'
                        ]);
                    } else {
                        Logger::error("Artist delete failed - database error", [
                            'artist_id' => $id
                        ]);
                        
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to delete artist'
                        ]);
                    }
                } catch (Exception $e) {
                    Logger::exception($e, "Artist delete error", [
                        'artist_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
                break;
                
            default:
                Logger::warning("Artist action failed - invalid action", [
                    'action' => $action
                ]);
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
                break;
        }
    }
    
    // Handle unsupported methods
    else {
        Logger::warning("Artists API - invalid request method", [
            'method' => $_SERVER['REQUEST_METHOD']
        ]);
        
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
    }
    
} catch (Exception $e) {
    Logger::exception($e, "Artists API unexpected error");
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
