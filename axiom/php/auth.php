<?php
if (!defined('AXIOM_ACCESS')) {
    die('Access denied');
}

require_once 'config.php';

class Auth {
    private $db;
    
    public function __construct() {
        try {
            $this->db = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            error_log("Auth constructor error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    /**
     * Register a new user
     */
    public function register($data) {
        try {
            // Validate required fields
            $required = ['first_name', 'last_name', 'username', 'email', 'password'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'message' => 'All required fields must be filled',
                        'errors' => [$field => ucfirst(str_replace('_', ' ', $field)) . ' is required']
                    ];
                }
            }
            
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Email address is already registered',
                    'errors' => ['email' => 'This email address is already in use']
                ];
            }
            
            // Check if username already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Username is already taken',
                    'errors' => ['username' => 'This username is already taken']
                ];
            }
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $this->db->prepare("
                INSERT INTO users (
                    first_name, last_name, username, email, password, 
                    phone, country, status, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())
            ");
            
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['username'],
                $data['email'],
                $hashedPassword,
                $data['phone'] ?? null,
                $data['country'] ?? null
            ]);
            
            $userId = $this->db->lastInsertId();
            
            return [
                'success' => true,
                'message' => 'Registration successful',
                'user_id' => $userId
            ];
            
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            
            // Check for duplicate entry errors
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'email') !== false) {
                    return [
                        'success' => false,
                        'message' => 'Email address is already registered',
                        'errors' => ['email' => 'This email address is already in use']
                    ];
                } elseif (strpos($e->getMessage(), 'username') !== false) {
                    return [
                        'success' => false,
                        'message' => 'Username is already taken',
                        'errors' => ['username' => 'This username is already taken']
                    ];
                }
            }
            
            return [
                'success' => false,
                'message' => 'Registration failed due to database error'
            ];
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Login user
     */
    public function login($emailOrUsername, $password, $rememberMe = false) {
        try {
            // Try to find user by email or username
            $stmt = $this->db->prepare("
                SELECT id, first_name, last_name, username, email, password, status 
                FROM users WHERE email = ? OR username = ?
            ");
            $stmt->execute([$emailOrUsername, $emailOrUsername]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Invalid email/username or password'
                ];
            }
            
            if ($user['status'] !== 'active') {
                return [
                    'success' => false,
                    'message' => 'Account is not active'
                ];
            }
            
            if (!password_verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid email/username or password'
                ];
            }
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['logged_in'] = true;
            
            // Handle remember me functionality
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                
                // Store remember token in database
                try {
                    $stmt = $this->db->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $stmt->execute([$token, $user['id']]);
                    
                    // Set remember me cookie (30 days)
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                } catch (Exception $e) {
                    // Remember me failed, but login succeeded
                    error_log("Remember me token storage failed: " . $e->getMessage());
                }
            }
            
            // Update last login
            $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Login failed due to system error'
            ];
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            return true;
        }
        
        // Check remember me token
        if (isset($_COOKIE['remember_token']) && !empty($_COOKIE['remember_token'])) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE remember_token = ? AND status = 'active'");
                $stmt->execute([$_COOKIE['remember_token']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    // Re-establish session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['logged_in'] = true;
                    
                    return true;
                }
            } catch (Exception $e) {
                error_log("Remember me check error: " . $e->getMessage());
            }
        }
        
        return false;
    }
    
    /**
     * Get current user data
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT id, first_name, last_name, username, email, phone, country, 
                       website, bio, profile_picture, created_at, last_login, status 
                FROM users WHERE id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get current user error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        try {
            // Check if email is already taken by another user
            if (!empty($data['email'])) {
                $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$data['email'], $userId]);
                if ($stmt->fetch()) {
                    return [
                        'success' => false,
                        'message' => 'Email address is already in use by another account'
                    ];
                }
            }
            
            // Update user profile
            $stmt = $this->db->prepare("
                UPDATE users SET 
                    first_name = ?, last_name = ?, email = ?, phone = ?, 
                    country = ?, website = ?, bio = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['location'] ?? null, // Map location to country
                $data['website'] ?? null,
                $data['bio'] ?? null,
                $userId
            ]);
            
            if (!$result) {
                return [
                    'success' => false,
                    'message' => 'Failed to update profile in database'
                ];
            }
            
            // Update session data
            $_SESSION['first_name'] = $data['first_name'];
            $_SESSION['last_name'] = $data['last_name'];
            $_SESSION['email'] = $data['email'];
            
            return [
                'success' => true,
                'message' => 'Profile updated successfully'
            ];
            
        } catch (PDOException $e) {
            error_log("Profile update error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error occurred while updating profile'
            ];
        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update profile'
            ];
        }
    }
    
    /**
     * Change user password - FIXED VERSION
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Log the attempt for debugging
            error_log("Password change attempt for user ID: " . $userId);
            
            // Get user's current password hash
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ? AND status = 'active'");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                error_log("User not found or inactive for ID: " . $userId);
                return [
                    'success' => false,
                    'message' => 'User not found or account is inactive'
                ];
            }
            
            // Verify current password
            if (!password_verify($currentPassword, $user['password'])) {
                error_log("Current password verification failed for user ID: " . $userId);
                return [
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ];
            }
            
            // Check if new password is different from current
            if (password_verify($newPassword, $user['password'])) {
                return [
                    'success' => false,
                    'message' => 'New password must be different from current password'
                ];
            }
            
            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            if (!$hashedPassword) {
                error_log("Password hashing failed for user ID: " . $userId);
                return [
                    'success' => false,
                    'message' => 'Failed to process new password'
                ];
            }
            
            // Update password in database
            $stmt = $this->db->prepare("
                UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?
            ");
            $result = $stmt->execute([$hashedPassword, $userId]);
            
            if (!$result) {
                error_log("Database update failed for user ID: " . $userId);
                return [
                    'success' => false,
                    'message' => 'Failed to update password in database'
                ];
            }
            
            // Verify the update was successful
            $rowsAffected = $stmt->rowCount();
            if ($rowsAffected === 0) {
                error_log("No rows affected during password update for user ID: " . $userId);
                return [
                    'success' => false,
                    'message' => 'Password update failed - no changes made'
                ];
            }
            
            error_log("Password successfully changed for user ID: " . $userId);
            
            // Clear any remember tokens for security
            try {
                $stmt = $this->db->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
                $stmt->execute([$userId]);
                
                // Clear remember me cookie if it exists
                if (isset($_COOKIE['remember_token'])) {
                    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
                }
            } catch (Exception $e) {
                // Token clearing failed, but password change succeeded
                error_log("Remember token clearing failed: " . $e->getMessage());
            }
            
            return [
                'success' => true,
                'message' => 'Password changed successfully'
            ];
            
        } catch (PDOException $e) {
            error_log("Password change database error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error occurred while changing password'
            ];
        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to change password: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete user account - NEW METHOD
     */
    public function deleteAccount($userId) {
        try {
            error_log("Account deletion attempt for user ID: " . $userId);
            
            // Start transaction
            $this->db->beginTransaction();
            
            // Get user data before deletion for cleanup
            $stmt = $this->db->prepare("SELECT username, email, profile_picture FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$userData) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }
            
            // Delete user's profile picture if exists
            if (!empty($userData['profile_picture'])) {
                $picturePath = '../uploads/profiles/' . $userData['profile_picture'];
                if (file_exists($picturePath)) {
                    unlink($picturePath);
                    error_log("Deleted profile picture: " . $picturePath);
                }
            }
            
            // Delete user's related data (add more tables as needed)
            
            // Delete from user_sessions table if exists
            try {
                $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE user_id = ?");
                $stmt->execute([$userId]);
            } catch (Exception $e) {
                error_log("Error deleting user sessions: " . $e->getMessage());
            }
            
            // Add more related data deletion here as needed
            // Example:
            // $stmt = $this->db->prepare("DELETE FROM user_posts WHERE user_id = ?");
            // $stmt->execute([$userId]);
            
            // Delete the user account
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $result = $stmt->execute([$userId]);
            
            if (!$result) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Failed to delete account from database'
                ];
            }
            
            $rowsAffected = $stmt->rowCount();
            if ($rowsAffected === 0) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Account deletion failed - user not found'
                ];
            }
            
            // Commit transaction
            $this->db->commit();
            
            error_log("Account successfully deleted for user ID: " . $userId);
            
            return [
                'success' => true,
                'message' => 'Account deleted successfully'
            ];
            
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Account deletion database error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error occurred while deleting account'
            ];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Account deletion error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete account: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Clear remember me token
        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $this->db->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
            } catch (Exception $e) {
                error_log("Logout token clear error: " . $e->getMessage());
            }
        }
        
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        // Destroy session
        session_destroy();
        session_start();
        
        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch() !== false;
        } catch (Exception $e) {
            error_log("Email check error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch() !== false;
        } catch (Exception $e) {
            error_log("Username check error: " . $e->getMessage());
            return false;
        }
    }
}
?>