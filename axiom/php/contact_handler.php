<?php
/**
 * Contact Handler - FINAL WORKING VERSION
 * Replace php/contact_handler.php with this
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to prevent any output before headers
ob_start();

// Start session
session_start();

// Define access
define('AXIOM_ACCESS', true);

// Include config
require_once 'config.php';

// Set JSON header first
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Clear any output buffer
ob_clean();

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Log the incoming data for debugging
    error_log("Contact form data received: " . print_r($_POST, true));
    
    // Get form data - handle both possible field names
    $firstName = trim($_POST['firstName'] ?? $_POST['first_name'] ?? '');
    $lastName = trim($_POST['lastName'] ?? $_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['messageText'] ?? $_POST['message'] ?? '');
    
    // Debug: Log extracted data
    error_log("Extracted data - First: $firstName, Last: $lastName, Email: $email, Subject: $subject");
    
    // Simple validation
    if (empty($firstName)) {
        echo json_encode(['success' => false, 'message' => 'First name is required']);
        exit;
    }
    
    if (empty($lastName)) {
        echo json_encode(['success' => false, 'message' => 'Last name is required']);
        exit;
    }
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }
    
    if (empty($subject)) {
        echo json_encode(['success' => false, 'message' => 'Subject is required']);
        exit;
    }
    
    if (empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Message is required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Database connection
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        if (!$conn) {
            throw new Exception("Database connection failed");
        }
        
        error_log("Database connection successful");
        
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Insert into database
    try {
        $sql = "INSERT INTO contact_submissions (first_name, last_name, email, phone, subject, message, status, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, ?, 'new', ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->errorInfo()[2]);
        }
        
        $userIP = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $result = $stmt->execute([
            $firstName,
            $lastName,
            $email,
            $phone ?: null,
            $subject,
            $message,
            $userIP,
            $userAgent
        ]);
        
        if ($result) {
            error_log("Contact form submitted successfully");
            echo json_encode([
                'success' => true, 
                'message' => 'Thank you for your message! We\'ll get back to you soon.'
            ]);
        } else {
            error_log("Database insert failed: " . print_r($stmt->errorInfo(), true));
            echo json_encode(['success' => false, 'message' => 'Failed to save message']);
        }
        
    } catch (Exception $e) {
        error_log("Database insert error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    
} catch (Exception $e) {
    error_log("Contact handler error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>