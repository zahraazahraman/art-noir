<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../CheckSession.php";
require_once __DIR__ . "/../Models/ArtworkModel.php";
require_once __DIR__ . "/../Models/NotificationModel.php";
require_once __DIR__ . "/../Core/Logger.php";

// Initialize logger
Logger::init();

try {
    $artwork = new Artwork();
    
    // Handle GET requests (Read/Search)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $search = isset($_GET['search']) ? $_GET['search'] : null;
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
        
        $artworks = $artwork->getArtworks($search, $category_id);
        
        Logger::info("Artworks fetched", [
            'search' => $search,
            'category_id' => $category_id,
            'count' => count($artworks)
        ]);
        
        echo json_encode($artworks);
    }
    
    
    // Handle POST requests (Create)
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        
       switch ($action) {
            case 'add':
                // Add new artwork
                $title = isset($_POST['title']) ? trim($_POST['title']) : '';
                $description = isset($_POST['description']) ? trim($_POST['description']) : '';
                $artist_id = isset($_POST['artist_id']) ? intval($_POST['artist_id']) : 0;
                $category_id = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? intval($_POST['category_id']) : null;
                $year_created = isset($_POST['year_created']) && $_POST['year_created'] !== '' ? intval($_POST['year_created']) : null;
                $status = isset($_POST['status']) ? $_POST['status'] : 'Pending';
                
                // Handle image upload
                if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    Logger::warning("Artwork add failed - image upload error", [
                        'title' => $title,
                        'error_code' => $_FILES['image']['error'] ?? 'no_image'
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Image upload failed'
                    ]);
                    exit;
                }

                // Save uploaded image
                $image = $_FILES['image'];

                $fileName = $_FILES['image']['name'];
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileSize = $_FILES['image']['size'];
                $fileType = $_FILES['image']['type'];
                $fileError = $_FILES['image']['error'];

                $fileExt = explode('.', $fileName);
                $fileActualExt = strtolower(end($fileExt));

                $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                $fileDestination = '../artworks/' . $fileNameNew;

                if (!is_dir('../artworks')) mkdir('../artworks', 0777, true);

                if (!move_uploaded_file($fileTmpPath, $fileDestination)) {
                    Logger::error("Artwork add failed - file move error", [
                        'title' => $title,
                        'destination' => $fileDestination
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to move uploaded file.'
                    ]);
                    exit;
                }
                $image_path = 'artworks/' . $fileNameNew;

                // Validation
                if (empty($title) || empty($description) || empty($image_path) || $artist_id <= 0) {
                    Logger::warning("Artwork add failed - missing fields", [
                        'title' => $title,
                        'artist_id' => $artist_id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Please fill in all required fields'
                    ]);
                    exit;
                }
                
                try {
                    $result = $artwork->addArtwork($title, $description, $image_path, $artist_id, $category_id, $year_created, $status);
                    
                    if ($result) {
                        Logger::info("Artwork added successfully", [
                            'artwork_id' => $result,
                            'title' => $title,
                            'artist_id' => $artist_id,
                            'status' => $status
                        ]);
                        
                        // Create notification for admin
                        $notificationModel = new Notification();
                            
                        $notificationModel->createNotification(
                            'artwork_pending',
                            'New Artwork Pending Review',
                            "New artwork '$title' has been submitted and is pending approval.",
                            'artworks',
                            $result // Inserted artwork ID
                        );

                        echo json_encode([
                            'success' => true,
                            'message' => 'Artwork added successfully'
                        ]);
                    } else {
                        Logger::error("Artwork add failed - database error", [
                            'title' => $title,
                            'artist_id' => $artist_id
                        ]);
                        
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to add artwork'
                        ]);
                    }
                } catch (Exception $e) {
                    Logger::exception($e, "Artwork add error", [
                        'title' => $title,
                        'artist_id' => $artist_id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
                break;
                
            case 'update':
                // Update artwork
                $artwork_id   = isset($_POST['artwork_id']) ? intval($_POST['artwork_id']) : 0;
                $title        = isset($_POST['title']) ? trim($_POST['title']) : '';
                $description  = isset($_POST['description']) ? trim($_POST['description']) : '';
                $artist_id    = isset($_POST['artist_id']) ? intval($_POST['artist_id']) : 0;
                $category_id  = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? intval($_POST['category_id']) : null;
                $year_created = isset($_POST['year_created']) && $_POST['year_created'] !== '' ? intval($_POST['year_created']) : null;
                $status = isset($_POST['status']) ? $_POST['status'] : 'Pending';

                // Get old image path (required)
                $old_image_path = isset($_POST['old_image_path']) ? trim($_POST['old_image_path']) : '';

                if ($artwork_id <= 0 || empty($title) || empty($description) || $artist_id <= 0) {
                    Logger::warning("Artwork update failed - missing fields", [
                        'artwork_id' => $artwork_id,
                        'title' => $title
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Missing required fields'
                    ]);
                    exit;
                }

                // Default: keep old image
                $image_path = $old_image_path;

                // Check if new file uploaded
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

                    // Upload new image
                    $fileName = $_FILES['image']['name'];
                    $fileTmpPath = $_FILES['image']['tmp_name'];

                    $fileExt = explode('.', $fileName);
                    $fileActualExt = strtolower(end($fileExt));

                    $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                    $fileDestination = '../artworks/' . $fileNameNew;

                    if (!is_dir('../artworks')) mkdir('../artworks', 0777, true);

                    if (!move_uploaded_file($fileTmpPath, $fileDestination)) {
                        Logger::error("Artwork update failed - file upload error", [
                            'artwork_id' => $artwork_id,
                            'title' => $title
                        ]);
                        
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to upload new image.'
                        ]);
                        exit;
                    }

                    // Set new image path
                    $image_path = 'artworks/' . $fileNameNew;
                }

                // Get current artwork to check status change
                $currentArtwork = $artwork->getArtworks(null, null);
                $current = null;
                foreach ($currentArtwork as $art) {
                    if ($art['id'] == $artwork_id) {
                        $current = $art;
                        break;
                    }
                }
                $oldStatus = $current ? $current['status'] : null;

                try {
                    // Update DB
                    $result = $artwork->updateArtwork(
                        $artwork_id,
                        $title,
                        $description,
                        $image_path,
                        $artist_id,
                        $category_id,
                        $year_created,
                        $status
                    );

                    if ($result) {
                        Logger::info("Artwork updated successfully", [
                            'artwork_id' => $artwork_id,
                            'title' => $title,
                            'old_status' => $oldStatus,
                            'new_status' => $status
                        ]);
                        
                        if ($oldStatus !== $status) {
                            // Status changed, create notification
                            $notificationModel = new Notification();
                            if ($status === 'Approved') {
                                $notificationModel->createNotification(
                                    'artwork_approved',
                                    'Artwork Approved',
                                    "Your artwork '$title' has been approved.",
                                    'artworks',
                                    $artwork_id
                                );
                            } elseif ($status === 'Rejected') {
                                $notificationModel->createNotification(
                                    'artwork_rejected',
                                    'Artwork Rejected',
                                    "Your artwork '$title' has been rejected.",
                                    'artworks',
                                    $artwork_id
                                );
                            }
                        }

                        echo json_encode([
                            'success' => $result,
                            'message' => $result ? 'Artwork updated successfully' : 'Failed to update artwork'
                        ]);
                    } else {
                        Logger::error("Artwork update failed - database error", [
                            'artwork_id' => $artwork_id,
                            'title' => $title
                        ]);
                        
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to update artwork'
                        ]);
                    }
                } catch (Exception $e) {
                    Logger::exception($e, "Artwork update error", [
                        'artwork_id' => $artwork_id,
                        'title' => $title
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }

                break;

            case 'delete':
                // Delete artwork
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                
                if ($id <= 0) {
                    Logger::warning("Artwork delete failed - invalid ID", [
                        'artwork_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid artwork ID'
                    ]);
                    exit;
                }
                
                try {
                    $result = $artwork->deleteArtwork($id);
                    
                    if ($result) {
                        Logger::info("Artwork deleted successfully", [
                            'artwork_id' => $id
                        ]);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Artwork deleted successfully'
                        ]);
                    } else {
                        Logger::error("Artwork delete failed - database error", [
                            'artwork_id' => $id
                        ]);
                        
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to delete artwork'
                        ]);
                    }
                } catch (Exception $e) {
                    Logger::exception($e, "Artwork delete error", [
                        'artwork_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
                break;

            default:
                Logger::warning("Artwork action failed - invalid action", [
                    'action' => $action
                ]);
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
                break;
        }
    }

} catch (Exception $e) {
    Logger::exception($e, "Artworks API unexpected error");
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
