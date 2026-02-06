<?php
header('Content-Type: application/json');

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

try {
    // Validate input
    if (empty($data['name']) || empty($data['email']) || empty($data['subject']) || empty($data['message'])) {
        throw new Exception('All fields are required');
    }

    // Sanitize input
    $name = htmlspecialchars(trim($data['name']));
    $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($data['subject']));
    $message = htmlspecialchars(trim($data['message']));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }

    // Here we can either:
    // 1. Save to database (implemented below)
    // 2. Send email
    // 3. Both
    
    // Save to database
    require_once '../DAL.class.php';
    $dal = new DAL();
    
    // Insert the message
    $insertSql = "INSERT INTO messages (sender_name, sender_email, subject, content) VALUES (?, ?, ?, ?)";
    $params = [$name, $email, $subject, $message];
    
    $result = $dal->executeQueryWithParams($insertSql, $params);
    
    if (!$result) {
        throw new Exception('Failed to save message');
    }

    // For now, just return success
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>