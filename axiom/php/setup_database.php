<?php
/**
 * Database Setup Script
 * Run this once to initialize your database with all required tables and default users
 * Place this file in: /php/setup_database.php
 * Then visit: http://localhost/axiom/php/setup_database.php
 */

define('AXIOM_ACCESS', true);
require_once 'config.php';
require_once 'functions.php';

// Set content type
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Axiom Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { color: #17a2b8; background: #d1ecf1; padding: 10px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #eee; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Axiom Database Setup</h1>
        <p>This script will initialize your database with all required tables and create default users.</p>";

try {
    echo "<h2>📊 Database Connection Test</h2>";
    
    // Test database connection
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    // Test with a simple query
    $stmt = $connection->query("SELECT VERSION() as version, NOW() as current_time");
    $result = $stmt->fetch();
    
    echo "<div class='success'>✅ Database connection successful!</div>";
    echo "<div class='info'>Database Version: " . $result['version'] . "</div>";
    echo "<div class='info'>Server Time: " . $result['current_time'] . "</div>";
    
    echo "<h2>🗃️ Creating Tables</h2>";
    
    // Create users table
    echo "<p>Creating users table...</p>";
    $db->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NULL,
        country VARCHAR(100) NULL,
        website VARCHAR(255) NULL,
        bio TEXT NULL,
        profile_picture VARCHAR(255) NULL,
        status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
        remember_token VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        INDEX idx_email (email),
        INDEX idx_username (username),
        INDEX idx_status (status),
        INDEX idx_remember_token (remember_token)
    )");
    echo "<div class='success'>✅ Users table created</div>";
    
    // Create rate_limits table
    echo "<p>Creating rate_limits table...</p>";
    $db->query("CREATE TABLE IF NOT EXISTS rate_limits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rate_key VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_rate_key (rate_key),
        INDEX idx_created_at (created_at)
    )");
    echo "<div class='success'>✅ Rate limits table created</div>";
    
    // Create security_logs table
    echo "<p>Creating security_logs table...</p>";
    $db->query("CREATE TABLE IF NOT EXISTS security_logs (
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
    )");
    echo "<div class='success'>✅ Security logs table created</div>";
    
    // Create activity_logs table
    echo "<p>Creating activity_logs table...</p>";
    $db->query("CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        action VARCHAR(100) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45) NOT NULL,
        user_agent TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_action (action),
        INDEX idx_created_at (created_at)
    )");
    echo "<div class='success'>✅ Activity logs table created</div>";
    
    echo "<h2>👤 Creating Default Users</h2>";
    
    // Create admin user
    echo "<p>Creating admin user...</p>";
    $adminExists = $db->fetchOne("SELECT id FROM users WHERE email = ? OR username = ?", 
                                ['admin@axiom.local', 'admin']);
    
    if (!$adminExists) {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $db->query("INSERT INTO users (first_name, last_name, username, email, password, status, created_at) 
                   VALUES (?, ?, ?, ?, ?, ?, NOW())", [
            'Admin',
            'User',
            'admin',
            'admin@axiom.local',
            $adminPassword,
            'active'
        ]);
        echo "<div class='success'>✅ Admin user created</div>";
        echo "<div class='info'>👤 Username: admin | Email: admin@axiom.local | Password: admin123</div>";
    } else {
        echo "<div class='info'>ℹ️ Admin user already exists</div>";
        
        // Update password to ensure it's correct
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $db->query("UPDATE users SET password = ? WHERE email = ? OR username = ?", 
                  [$adminPassword, 'admin@axiom.local', 'admin']);
        echo "<div class='success'>🔄 Admin password updated</div>";
    }
    
    // Create demo user
    echo "<p>Creating demo user...</p>";
    $demoExists = $db->fetchOne("SELECT id FROM users WHERE email = ? OR username = ?", 
                               ['demo@axiom.local', 'demo']);
    
    if (!$demoExists) {
        $demoPassword = password_hash('demo123', PASSWORD_DEFAULT);
        $db->query("INSERT INTO users (first_name, last_name, username, email, password, status, created_at) 
                   VALUES (?, ?, ?, ?, ?, ?, NOW())", [
            'Demo',
            'User',
            'demo',
            'demo@axiom.local',
            $demoPassword,
            'active'
        ]);
        echo "<div class='success'>✅ Demo user created</div>";
        echo "<div class='info'>👤 Username: demo | Email: demo@axiom.local | Password: demo123</div>";
    } else {
        echo "<div class='info'>ℹ️ Demo user already exists</div>";
        
        // Update password to ensure it's correct
        $demoPassword = password_hash('demo123', PASSWORD_DEFAULT);
        $db->query("UPDATE users SET password = ? WHERE email = ? OR username = ?", 
                  [$demoPassword, 'demo@axiom.local', 'demo']);
        echo "<div class='success'>🔄 Demo password updated</div>";
    }
    
    echo "<h2>🧹 Database Cleanup</h2>";
    
    // Clear any existing rate limits
    echo "<p>Clearing old rate limits...</p>";
    $deleted = $db->query("DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    echo "<div class='success'>✅ Old rate limits cleared</div>";
    
    echo "<h2>🎯 Verification Tests</h2>";
    
    // Test admin login
    echo "<p>Testing admin user authentication...</p>";
    $adminUser = $db->fetchOne("SELECT * FROM users WHERE email = ?", ['admin@axiom.local']);
    if ($adminUser && password_verify('admin123', $adminUser['password'])) {
        echo "<div class='success'>✅ Admin authentication test passed</div>";
    } else {
        echo "<div class='error'>❌ Admin authentication test failed</div>";
    }
    
    // Test demo login
    echo "<p>Testing demo user authentication...</p>";
    $demoUser = $db->fetchOne("SELECT * FROM users WHERE email = ?", ['demo@axiom.local']);
    if ($demoUser && password_verify('demo123', $demoUser['password'])) {
        echo "<div class='success'>✅ Demo authentication test passed</div>";
    } else {
        echo "<div class='error'>❌ Demo authentication test failed</div>";
    }
    
    // Show user count
    $userCount = $db->fetchOne("SELECT COUNT(*) as count FROM users");
    echo "<div class='info'>📊 Total users in database: " . $userCount['count'] . "</div>";
    
    echo "<h2>🎉 Setup Complete!</h2>";
    echo "<div class='success'>
        <h3>✅ Database setup completed successfully!</h3>
        <p><strong>You can now use the following credentials to log in:</strong></p>
        <ul>
            <li><strong>Admin:</strong> admin@axiom.local / admin123</li>
            <li><strong>Demo:</strong> demo@axiom.local / demo123</li>
        </ul>
    </div>";
    
    echo "<div class='info'>
        <h3>📝 Next Steps:</h3>
        <ol>
            <li>Try logging in with the credentials above</li>
            <li>Check that rate limiting is working properly</li>
            <li>Test the remember me functionality</li>
            <li>Review the error logs if any issues occur</li>
        </ol>
    </div>";
    
    // Security recommendations
    echo "<div class='info'>
        <h3>🔒 Security Recommendations:</h3>
        <ul>
            <li>Change the default admin password after first login</li>
            <li>Delete this setup file after running it</li>
            <li>Make sure DEBUG_MODE is set to false in production</li>
            <li>Regularly review the security and activity logs</li>
        </ul>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Setup failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    
    // Show detailed database connection info
    echo "<h3>🔍 Debug Information:</h3>";
    echo "<pre>";
    echo "Database Host: " . DB_HOST . "\n";
    echo "Database Name: " . DB_NAME . "\n";
    echo "Database User: " . DB_USER . "\n";
    echo "Database Password: " . (empty(DB_PASS) ? '(empty)' : '(set)') . "\n";
    echo "PHP PDO Available: " . (extension_loaded('pdo') ? 'Yes' : 'No') . "\n";
    echo "PHP PDO MySQL Available: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "\n";
    echo "Current working directory: " . getcwd() . "\n";
    echo "Config file exists: " . (file_exists('config.php') ? 'Yes' : 'No') . "\n";
    echo "</pre>";
}

echo "
    </div>
</body>
</html>";
?>