<?php

header('Content-Type: application/json');

require_once __DIR__ . "/../Models/CategoryModel.php";
require_once __DIR__ . "/../Core/Logger.php";

// Initialize logger
Logger::init();

try {
    $category = new Category();
    
    // Handle GET requests (Read)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $categories = $category->getCategories();
        
        Logger::info("Categories fetched", [
            'count' => count($categories)
        ]);
        
        echo json_encode($categories);
    }
    
    // Handle POST requests (Create/Update/Delete)
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        
        switch ($action) {
            case 'add':
                // Add new category
                $name = isset($_POST['name']) ? trim($_POST['name']) : '';
                
                // Validation
                if (empty($name)) {
                    Logger::warning("Category add failed - missing name", [
                        'name' => $name
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Category name is required'
                    ]);
                    exit;
                }
                
                if (strlen($name) < 2) {
                    Logger::warning("Category add failed - name too short", [
                        'name' => $name,
                        'length' => strlen($name)
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Category name must be at least 2 characters'
                    ]);
                    exit;
                }
                
                try {
                    $result = $category->addCategory($name);
                    
                    if ($result) {
                        Logger::info("Category added successfully", [
                            'category_id' => $result,
                            'name' => $name
                        ]);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Category added successfully'
                        ]);
                    } else {
                        Logger::error("Category add failed - database error", [
                            'name' => $name
                        ]);
                        
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to add category'
                        ]);
                    }
                } catch (Exception $e) {
                    Logger::exception($e, "Category add error", [
                        'name' => $name
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
                break;
                
            case 'update':
                // Update existing category
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $name = isset($_POST['name']) ? trim($_POST['name']) : '';
                
                // Validation
                if ($id <= 0) {
                    Logger::warning("Category update failed - invalid ID", [
                        'category_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid category ID'
                    ]);
                    exit;
                }
                
                if (empty($name)) {
                    Logger::warning("Category update failed - missing name", [
                        'category_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Category name is required'
                    ]);
                    exit;
                }
                
                if (strlen($name) < 2) {
                    Logger::warning("Category update failed - name too short", [
                        'category_id' => $id,
                        'name' => $name,
                        'length' => strlen($name)
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Category name must be at least 2 characters'
                    ]);
                    exit;
                }
                
                try {
                    $result = $category->updateCategory($id, $name);
                    
                    if ($result) {
                        Logger::info("Category updated successfully", [
                            'category_id' => $id,
                            'name' => $name
                        ]);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Category updated successfully'
                        ]);
                    } else {
                        Logger::error("Category update failed - database error", [
                            'category_id' => $id,
                            'name' => $name
                        ]);
                        
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to update category'
                        ]);
                    }
                } catch (Exception $e) {
                    Logger::exception($e, "Category update error", [
                        'category_id' => $id,
                        'name' => $name
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
                break;
                
            case 'delete':
                // Delete category
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                
                if ($id <= 0) {
                    Logger::warning("Category delete failed - invalid ID", [
                        'category_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid category ID'
                    ]);
                    exit;
                }
                
                // Check if category has artworks
                if ($category->hasArtworks($id)) {
                    Logger::warning("Category delete failed - has artworks", [
                        'category_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Cannot delete category. Please move all artworks to another category first.'
                    ]);
                    exit;
                }
                
                try {
                    $result = $category->deleteCategory($id);
                    
                    if ($result) {
                        Logger::info("Category deleted successfully", [
                            'category_id' => $id
                        ]);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Category deleted successfully'
                        ]);
                    } else {
                        Logger::error("Category delete failed - database error", [
                            'category_id' => $id
                        ]);
                        
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to delete category'
                        ]);
                    }
                } catch (Exception $e) {
                    Logger::exception($e, "Category delete error", [
                        'category_id' => $id
                    ]);
                    
                    echo json_encode([
                        'success' => false,
                        'message' => $e->getMessage()
                    ]);
                }
                break;
                
            default:
                Logger::warning("Category action failed - invalid action", [
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
        Logger::warning("Categories API - invalid request method", [
            'method' => $_SERVER['REQUEST_METHOD']
        ]);
        
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
    }
    
} catch (Exception $e) {
    Logger::exception($e, "Categories API unexpected error");
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
