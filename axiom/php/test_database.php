<?php
/**
 * Database Connection Test Script
 * Use this to test if your database is working properly
 */

define('AXIOM_ACCESS', true);
require_once 'config.php';

// Set content type to JSON for easy reading
header('Content-Type: application/json');

$response = [
    'database_connection' => false,
    'tables_exist' => false,
    'admin_user_exists' => false,
    'users_count' => 0,
    'errors' => [],
    'info' => []
];

try {
    // Test database connection
    $db = Database::getInstance();
    $connection = $db->getConnection();
    $response['database_connection'] = true;
    $response['info'][] = "✅ Database connection successful";
    
    // Check if tables exist
    $tables = $connection->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $expectedTables = ['users', 'contact_submissions', 'activity_logs'];
    $missingTables = [];
    
    foreach ($expectedTables as $table) {
        if (!in_array($table, $tables)) {
            $missingTables[] = $table;
        }
    }
    
    if (empty($missingTables)) {
        $response['tables_exist'] = true;
        $response['info'][] = "✅ All required tables exist";
    } else {
        $response['errors'][] = "❌ Missing tables: " . implode(', ', $missingTables);
    }
    
    // Check users table
    if (in_array('users', $tables)) {
        $userCount = $connection->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $response['users_count'] = $userCount;
        $response['info'][] = "📊 Total users in database: " . $userCount;
        
        // Check for admin user
        $adminUser = $connection->query("SELECT * FROM users WHERE email = 'admin@axiom.local' OR username = 'admin'")->fetch();
        
        if ($adminUser) {
            $response['admin_user_exists'] = true;
            $response['info'][] = "✅ Admin user exists";
            $response['info'][] = "👤 Admin details: " . $adminUser['first_name'] . " " . $adminUser['last_name'] . " (" . $adminUser['email'] . ")";
            
            // Test password verification
            if (password_verify('admin123', $adminUser['password'])) {
                $response['info'][] = "✅ Admin password verification successful";
            } else {
                $response['errors'][] = "❌ Admin password verification failed";
                $response['info'][] = "🔑 Try updating admin password in database";
            }
        } else {
            $response['errors'][] = "❌ Admin user not found";
            $response['info'][] = "💡 Run the SQL import script to create default users";
        }
        
        // List all users
        $allUsers = $connection->query("SELECT id, first_name, last_name, username, email, status FROM users ORDER BY id")->fetchAll();
        $response['users_list'] = $allUsers;
    }
    
    // Database configuration info
    $response['config'] = [
        'DB_HOST' => DB_HOST,
        'DB_NAME' => DB_NAME,
        'DB_USER' => DB_USER,
        'SITE_URL' => SITE_URL,
        'DEBUG_MODE' => DEBUG_MODE
    ];
    
    $response['info'][] = "🔧 Database: " . DB_NAME . " on " . DB_HOST;
    
} catch (PDOException $e) {
    $response['errors'][] = "❌ Database error: " . $e->getMessage();
    
    if ($e->getCode() == 1049) {
        $response['errors'][] = "💡 Database '" . DB_NAME . "' does not exist. Create it first.";
    } elseif ($e->getCode() == 1045) {
        $response['errors'][] = "💡 Access denied. Check DB_USER and DB_PASS in config.php";
    } elseif ($e->getCode() == 2002) {
        $response['errors'][] = "💡 Can't connect to MySQL server. Is XAMPP/MySQL running?";
    }
    
} catch (Exception $e) {
    $response['errors'][] = "❌ General error: " . $e->getMessage();
}

// Create admin user if missing (only if tables exist)
if ($response['tables_exist'] && !$response['admin_user_exists'] && $response['database_connection']) {
    try {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $connection->prepare("
            INSERT INTO users (first_name, last_name, username, email, password, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, 'active', NOW(), NOW())
        ");
        
        $result = $stmt->execute(['Admin', 'User', 'admin', 'admin@axiom.local', $hashedPassword]);
        
        if ($result) {
            $response['info'][] = "✅ Admin user created successfully";
            $response['admin_user_exists'] = true;
        }
    } catch (Exception $e) {
        $response['errors'][] = "❌ Failed to create admin user: " . $e->getMessage();
    }
}

// Summary
if ($response['database_connection'] && $response['tables_exist'] && $response['admin_user_exists']) {
    $response['status'] = 'success';
    $response['message'] = '🎉 Database is ready! You can now use login: admin@axiom.local / admin123';
} else {
    $response['status'] = 'error';
    $response['message'] = '⚠️ Database setup incomplete. Check errors above.';
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>