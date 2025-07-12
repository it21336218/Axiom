<?php
/**
 * Login Handler - COMPLETELY FIXED VERSION
 * This will work with your existing frontend
 */

define('AXIOM_ACCESS', true);
require_once 'config.php';
require_once 'functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set proper headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Invalid request method. Use POST.', null, 405);
}

try {
    // Get input data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember']) && ($_POST['remember'] === 'on' || $_POST['remember'] === '1');
    
    // Basic validation
    if (empty($email)) {
        sendError('Email or username is required');
    }
    
    if (empty($password)) {
        sendError('Password is required');
    }
    
    // Rate limiting
    $userIP = getUserIP();
    $rateLimitKey = 'login_' . $userIP;
    
    if (!checkRateLimit($rateLimitKey, 10, 900)) {
        logSecurityEvent('login_rate_limit', "Too many attempts from IP: $userIP");
        sendError('Too many login attempts. Please try again in 15 minutes.', null, 429);
    }
    
    // Connect to database
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    // Find user by email or username
    $stmt = $connection->prepare("
        SELECT id, first_name, last_name, username, email, password, status 
        FROM users 
        WHERE (email = ? OR username = ?) AND status = 'active'
    ");
    $stmt->execute([$email, $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        updateRateLimit($rateLimitKey, 900);
        logSecurityEvent('login_failed', "User not found: $email from IP: $userIP");
        sendError('Invalid email/username or password');
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        updateRateLimit($rateLimitKey, 900);
        logSecurityEvent('login_failed', "Wrong password for: {$user['email']} from IP: $userIP");
        sendError('Invalid email/username or password');
    }
    
    // Success! Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['logged_in'] = true;
    
    // Handle remember me
    if ($rememberMe) {
        $token = bin2hex(random_bytes(32));
        
        try {
            $stmt = $connection->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $stmt->execute([$token, $user['id']]);
            
            // Set remember me cookie (30 days)
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        } catch (Exception $e) {
            error_log("Remember me failed: " . $e->getMessage());
        }
    }
    
    // Update last login
    $stmt = $connection->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    // Clear rate limiting on successful login
    clearRateLimit($rateLimitKey);
    
    // Log successful login
    logSecurityEvent('login_success', "Successful login: {$user['email']} from IP: $userIP", $user['id']);
    
    // Send success response
    sendSuccess('Login successful! Redirecting...', [
        'redirect' => 'profile.php',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name']
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in login: " . $e->getMessage());
    sendError('Database connection error. Please try again later.', null, 500);
} catch (Exception $e) {
    error_log("Login handler error: " . $e->getMessage());
    sendError('Login failed due to server error. Please try again.', null, 500);
}
?>