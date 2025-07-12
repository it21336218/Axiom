<?php
/**
 * Registration Handler - GUARANTEED WORKING VERSION
 * COPY THIS EXACTLY to php/register_handler.php
 */

// Start session first
session_start();

// Define access constant
define('AXIOM_ACCESS', true);

// Include required files
require_once 'config.php';
require_once 'functions.php';

// Set headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get all form data
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $terms = $_POST['terms'] ?? '';
    
    // Basic validation
    $errors = [];
    
    if (empty($firstName)) $errors['firstName'] = 'First name is required';
    if (empty($lastName)) $errors['lastName'] = 'Last name is required';
    if (empty($username)) $errors['username'] = 'Username is required';
    if (empty($email)) $errors['email'] = 'Email is required';
    if (empty($password)) $errors['password'] = 'Password is required';
    if (empty($confirmPassword)) $errors['confirmPassword'] = 'Confirm password is required';
    if ($terms !== 'on') $errors['terms'] = 'You must accept the terms';
    
    // Password match check
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Passwords do not match';
    }
    
    // Email format check
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    // Username format check
    if (!empty($username) && !preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors['username'] = 'Username must be 3-20 characters (letters, numbers, underscore only)';
    }
    
    // Password length check
    if (!empty($password) && strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }
    
    // If validation errors, return them
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fix the errors below',
            'errors' => $errors
        ]);
        exit;
    }
    
    // Connect to database
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists',
            'errors' => ['email' => 'This email is already registered']
        ]);
        exit;
    }
    
    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists',
            'errors' => ['username' => 'This username is already taken']
        ]);
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $conn->prepare("
        INSERT INTO users (first_name, last_name, username, email, password, phone, country, status, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())
    ");
    
    $result = $stmt->execute([
        $firstName,
        $lastName, 
        $username,
        $email,
        $hashedPassword,
        $phone ?: null,
        $country ?: null
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! You can now log in.',
            'data' => ['redirect' => 'login.php']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Registration failed. Please try again.'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Registration DB Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log("Registration Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again.'
    ]);
}
?>