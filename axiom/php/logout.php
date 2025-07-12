<?php
/**
 * Logout Handler - Fixed Version
 * Processes user logout requests
 */

define('AXIOM_ACCESS', true);
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';

try {
    // Initialize auth
    $auth = new Auth();
    
    // Check if user is logged in
    if ($auth->isLoggedIn()) {
        // Get user info before logout for logging
        $userId = $_SESSION['user_id'] ?? null;
        $userEmail = $_SESSION['email'] ?? 'Unknown';
        
        // Log logout activity
        if ($userId) {
            logActivity($userId, 'logout', 'User logged out successfully');
            logSecurityEvent('logout', "User: {$userEmail} logged out", $userId);
        }
        
        // Perform logout
        $result = $auth->logout();
        
        if (isAjaxRequest()) {
            // Send JSON response for AJAX requests
            header('Content-Type: application/json');
            
            if ($result['success']) {
                sendSuccess($result['message'], ['redirect' => '../pages/index.php']);
            } else {
                sendError($result['message']);
            }
        } else {
            // Redirect for regular requests
            if ($result['success']) {
                // Set a success message in session for the next page
                session_start();
                $_SESSION['logout_message'] = 'You have been logged out successfully.';
                header('Location: ../pages/index.php');
                exit;
            } else {
                // Set error message and redirect back
                $_SESSION['error_message'] = $result['message'];
                header('Location: ../pages/login.php');
                exit;
            }
        }
    } else {
        if (isAjaxRequest()) {
            sendError('Not logged in', null, 401);
        } else {
            header('Location: ../pages/login.php');
            exit;
        }
    }
    
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    
    if (isAjaxRequest()) {
        sendError('Logout failed due to server error', null, 500);
    } else {
        // Force session destruction and redirect
        session_destroy();
        header('Location: ../pages/index.php');
        exit;
    }
}
?>