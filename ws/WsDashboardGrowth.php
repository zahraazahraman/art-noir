<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../DAL.class.php";

try {
    $dal = new DAL();
    
    // Get metric type (artworks, users, artists)
    $metric = isset($_GET['metric']) ? $_GET['metric'] : 'artworks';
    
    // Get last 12 months data
    $months = [];
    $labels = [];
    
    for ($i = 11; $i >= 0; $i--) {
        $date = new DateTime();
        $date->modify("-$i month");
        $months[] = $date->format('Y-m');
        $labels[] = $date->format('M Y');
    }
    
    $data = [];
    
    switch ($metric) {
        case 'artworks':
            foreach ($months as $month) {
                $startDate = $month . '-01';
                $endDate = date('Y-m-t', strtotime($startDate));
                
                $query = "SELECT COUNT(*) as count FROM artworks 
                         WHERE created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
                $result = $dal->getData($query);
                $data[] = (int)$result[0]['count'];
            }
            break;
            
        case 'users':
            foreach ($months as $month) {
                $startDate = $month . '-01';
                $endDate = date('Y-m-t', strtotime($startDate));
                
                $query = "SELECT COUNT(*) as count FROM users 
                         WHERE created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
                $result = $dal->getData($query);
                $data[] = (int)$result[0]['count'];
            }
            break;
            
        case 'artists':
            foreach ($months as $month) {
                $startDate = $month . '-01';
                $endDate = date('Y-m-t', strtotime($startDate));
                
                $query = "SELECT COUNT(*) as count FROM artists 
                         WHERE created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
                $result = $dal->getData($query);
                $data[] = (int)$result[0]['count'];
            }
            break;
    }
    
    // Calculate growth percentage
    $currentMonth = end($data);
    $previousMonth = $data[count($data) - 2];
    $growthPercentage = $previousMonth > 0 ? 
        round((($currentMonth - $previousMonth) / $previousMonth) * 100, 1) : 
        ($currentMonth > 0 ? 100 : 0);
    
    // Calculate total for the period
    $total = array_sum($data);
    
    // Calculate average
    $average = round($total / count($data), 1);
    
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'data' => $data,
        'metrics' => [
            'currentMonth' => $currentMonth,
            'previousMonth' => $previousMonth,
            'growthPercentage' => $growthPercentage,
            'total' => $total,
            'average' => $average
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
