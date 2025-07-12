<?php
/**
 * Essential Utility Functions for Axiom Project - FIXED VERSION
 * All common functions used throughout the application
 */

if (!defined('AXIOM_ACCESS')) {
    die('Access denied');
}

/**
 * Send JSON success response
 */
function sendSuccess($message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

/**
 * Send JSON error response
 */
function sendError($message, $errors = null, $statusCode = 400) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => $message,
        'errors' => $errors,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

/**
 * Check if request is AJAX
 */
function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate required fields
 */
function validateRequired($data, $requiredFields) {
    $errors = [];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            $fieldName = ucfirst(str_replace('_', ' ', $field));
            $errors[$field] = $fieldName . ' is required';
        }
    }
    return $errors;
}

/**
 * Validate email address
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number
 */
function isValidPhone($phone) {
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($cleanPhone) >= 10 && strlen($cleanPhone) <= 15;
}

/**
 * Validate password strength
 */
function validatePasswordStrength($password) {
    $errors = [];
    
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long';
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter';
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter';
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number';
    }
    
    return $errors;
}

/**
 * Validate uploaded file
 */
function validateUploadedFile($file) {
    $errors = [];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'File size exceeds maximum allowed size';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors[] = 'File was only partially uploaded';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = 'No file was uploaded';
                break;
            default:
                $errors[] = 'Unknown upload error';
        }
        return $errors;
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = 'File size exceeds maximum allowed size of ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB';
    }
    
    // Check file type for images
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_IMAGE_TYPES)) {
        $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', ALLOWED_IMAGE_TYPES);
    }
    
    return $errors;
}

/**
 * Get user's IP address
 */
function getUserIP() {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            $ip = trim($ips[0]);
            
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Rate limiting functions
 */
function checkRateLimit($key, $maxAttempts, $timeWindow) {
    try {
        $db = Database::getInstance();
        
        // Clean old entries
        $db->query("DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL ? SECOND)", [$timeWindow]);
        
        // Count current attempts
        $stmt = $db->query("SELECT COUNT(*) as count FROM rate_limits WHERE rate_key = ?", [$key]);
        $result = $stmt->fetch();
        
        return $result['count'] < $maxAttempts;
    } catch (Exception $e) {
        error_log("Rate limit check error: " . $e->getMessage());
        return true; // Allow on error
    }
}

function updateRateLimit($key, $timeWindow) {
    try {
        $db = Database::getInstance();
        $ip = getUserIP();
        
        $db->query("INSERT INTO rate_limits (rate_key, ip_address, created_at) VALUES (?, ?, NOW())", [$key, $ip]);
        
        // Count total attempts
        $stmt = $db->query("SELECT COUNT(*) as count FROM rate_limits WHERE rate_key = ?", [$key]);
        $result = $stmt->fetch();
        
        return $result['count'];
    } catch (Exception $e) {
        error_log("Rate limit update error: " . $e->getMessage());
        return 1;
    }
}

function clearRateLimit($key) {
    try {
        $db = Database::getInstance();
        $db->query("DELETE FROM rate_limits WHERE rate_key = ?", [$key]);
    } catch (Exception $e) {
        error_log("Rate limit clear error: " . $e->getMessage());
    }
}

/**
 * Log security events
 */
function logSecurityEvent($eventType, $details, $userId = null) {
    try {
        $db = Database::getInstance();
        $ip = getUserIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $db->query("
            INSERT INTO security_logs (event_type, user_id, details, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ", [$eventType, $userId, $details, $ip, $userAgent]);
    } catch (Exception $e) {
        error_log("Security log error: " . $e->getMessage());
    }
}

/**
 * Email functions
 */
function sendEmail($to, $subject, $message, $fromEmail = null, $fromName = null) {
    $fromEmail = $fromEmail ?? FROM_EMAIL;
    $fromName = $fromName ?? FROM_NAME;
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        "From: {$fromName} <{$fromEmail}>",
        "Reply-To: {$fromEmail}",
        'X-Mailer: PHP/' . phpversion()
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

function createEmailTemplate($title, $content) {
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>{$title}</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>" . SITE_NAME . "</h1>
            </div>
            <div class='content'>
                {$content}
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " " . SITE_NAME . ". All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";
}

/**
 * Create default admin user
 */
function createDefaultAdmin() {
    try {
        $db = Database::getInstance();
        
        // Check if admin exists
        $stmt = $db->query("SELECT id FROM users WHERE email = ? OR username = ?", ['admin@axiom.local', 'admin']);
        if ($stmt->fetch()) {
            return false; // Admin already exists
        }
        
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        
        $db->query("
            INSERT INTO users (first_name, last_name, username, email, password, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, 'active', NOW(), NOW())
        ", ['Admin', 'User', 'admin', 'admin@axiom.local', $hashedPassword]);
        
        return true;
    } catch (Exception $e) {
        error_log("Create admin error: " . $e->getMessage());
        return false;
    }
}

/**
 * Create demo user
 */
function createDemoUser() {
    try {
        $db = Database::getInstance();
        
        // Check if demo user exists
        $stmt = $db->query("SELECT id FROM users WHERE email = ? OR username = ?", ['demo@axiom.local', 'demo']);
        if ($stmt->fetch()) {
            return false; // Demo user already exists
        }
        
        $hashedPassword = password_hash('demo123', PASSWORD_DEFAULT);
        
        $db->query("
            INSERT INTO users (first_name, last_name, username, email, password, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, 'active', NOW(), NOW())
        ", ['Demo', 'User', 'demo', 'demo@axiom.local', $hashedPassword]);
        
        return true;
    } catch (Exception $e) {
        error_log("Create demo user error: " . $e->getMessage());
        return false;
    }
}

/**
 * Create rate limits table if not exists
 */
function createRateLimitsTable() {
    try {
        $db = Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS rate_limits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                rate_key VARCHAR(255) NOT NULL,
                ip_address VARCHAR(45) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_rate_key (rate_key),
                INDEX idx_created_at (created_at)
            )
        ");
        return true;
    } catch (Exception $e) {
        error_log("Create rate limits table error: " . $e->getMessage());
        return false;
    }
}

/**
 * Create security logs table if not exists
 */
function createSecurityLogsTable() {
    try {
        $db = Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS security_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                event_type VARCHAR(100) NOT NULL,
                user_id INT NULL,
                details TEXT,
                ip_address VARCHAR(45) NOT NULL,
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_event_type (event_type),
                INDEX idx_user_id (user_id),
                INDEX idx_created_at (created_at)
            )
        ");
        return true;
    } catch (Exception $e) {
        error_log("Create security logs table error: " . $e->getMessage());
        return false;
    }
}

// Initialize required tables
createRateLimitsTable();
createSecurityLogsTable();

// Debug function
function debugLog($message, $data = null) {
    if (DEBUG_MODE) {
        error_log("DEBUG: " . $message . ($data ? " - " . print_r($data, true) : ""));
    }
}
?>