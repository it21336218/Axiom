<?php
/**
 * Debug Profile Handler
 * Add debugging to identify password change issues
 */

define('AXIOM_ACCESS', true);
require_once 'config.php';
require_once 'auth.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log all POST data for debugging
error_log("POST data received: " . print_r($_POST, true));
error_log("Session data: " . print_r($_SESSION, true));

try {
    // Check if user is logged in
    $auth = new Auth();
    
    if (!$auth->isLoggedIn()) {
        error_log("User not logged in");
        sendError('Authentication required', null, 401);
    }
    
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
        sendError('Invalid request method', null, 405);
    }
    
    $userId = $_SESSION['user_id'];
    $action = $_POST['action'] ?? 'update_profile';
    
    error_log("Processing action: $action for user ID: $userId");
    
    switch ($action) {
        case 'update_profile':
            updateProfile($auth, $userId);
            break;
            
        case 'change_password':
            changePassword($auth, $userId);
            break;
            
        default:
            error_log("Invalid action: $action");
            sendError('Invalid action');
    }
    
} catch (Exception $e) {
    error_log("Profile handler exception: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    sendError('Profile operation failed due to server error: ' . $e->getMessage(), null, 500);
}

/**
 * Update user profile
 */
function updateProfile($auth, $userId) {
    error_log("Starting profile update for user: $userId");
    
    // Get POST data
    $data = [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'location' => $_POST['location'] ?? '',
        'website' => $_POST['website'] ?? '',
        'bio' => $_POST['bio'] ?? ''
    ];
    
    error_log("Profile data to update: " . print_r($data, true));
    
    // Sanitize data
    $data = array_map('trim', $data);
    $data = array_map(function($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }, $data);
    
    // Validate required fields
    $errors = [];
    if (empty($data['first_name'])) $errors['first_name'] = 'First name is required';
    if (empty($data['last_name'])) $errors['last_name'] = 'Last name is required';
    if (empty($data['email'])) $errors['email'] = 'Email is required';
    
    // Validate email
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    if (!empty($errors)) {
        error_log("Validation errors: " . print_r($errors, true));
        sendError('Please fix the errors below', $errors);
    }
    
    // Update profile
    $result = $auth->updateProfile($userId, $data);
    error_log("Profile update result: " . print_r($result, true));
    
    if ($result['success']) {
        sendSuccess($result['message'], ['updated_data' => $data]);
    } else {
        sendError($result['message']);
    }
}

/**
 * Change user password
 */
function changePassword($auth, $userId) {
    error_log("Starting password change for user: $userId");
    
    // Get POST data
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Log password field presence (NOT the actual passwords)
    error_log("Current password provided: " . (!empty($currentPassword) ? 'Yes' : 'No'));
    error_log("New password provided: " . (!empty($newPassword) ? 'Yes' : 'No'));
    error_log("Confirm password provided: " . (!empty($confirmPassword) ? 'Yes' : 'No'));
    
    // Validate required fields
    if (empty($currentPassword)) {
        error_log("Current password is empty");
        sendError('Current password is required');
    }
    
    if (empty($newPassword)) {
        error_log("New password is empty");
        sendError('New password is required');
    }
    
    if (empty($confirmPassword)) {
        error_log("Confirm password is empty");
        sendError('Password confirmation is required');
    }
    
    // Check if new passwords match
    if ($newPassword !== $confirmPassword) {
        error_log("New passwords do not match");
        sendError('New passwords do not match');
    }
    
    // Validate new password strength
    $passwordErrors = validatePasswordStrength($newPassword);
    if (!empty($passwordErrors)) {
        error_log("Password strength validation failed: " . implode(', ', $passwordErrors));
        sendError('Password does not meet security requirements: ' . implode(', ', $passwordErrors));
    }
    
    error_log("All validations passed, calling auth->changePassword");
    
    // Change password
    $result = $auth->changePassword($userId, $currentPassword, $newPassword);
    
    error_log("Password change result: " . print_r($result, true));
    
    if ($result['success']) {
        sendSuccess($result['message']);
    } else {
        sendError($result['message']);
    }
}

/**
 * Helper function to send success response
 */
function sendSuccess($message, $data = null) {
    $response = ['success' => true, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    error_log("Sending success response: " . json_encode($response));
    echo json_encode($response);
    exit;
}

/**
 * Helper function to send error response
 */
function sendError($message, $data = null, $statusCode = 400) {
    http_response_code($statusCode);
    $response = ['success' => false, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    error_log("Sending error response: " . json_encode($response));
    echo json_encode($response);
    exit;
}

/**
 * Basic password strength validation
 */
function validatePasswordStrength($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter';
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter';
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number';
    }
    
    return $errors;
}
?>