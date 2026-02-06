<?php
header('Content-Type: application/json');

require_once __DIR__ . "/../Models/CountryModel.php";

try {
    $country = new Country();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Check if getting by ID
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $country->getCountryById($id);
            echo json_encode($result);
        } 
        // Check if getting by code
        elseif (isset($_GET['code'])) {
            $code = trim($_GET['code']);
            $result = $country->getCountryByCode($code);
            echo json_encode($result);
        } 
        // Get all countries
        else {
            $countries = $country->getCountries();
            echo json_encode($countries);
        }
    } else {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
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