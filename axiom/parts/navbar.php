<?php
// Check if user is logged in
$isLoggedIn = false;
$userData = null;
$profilePicture = null;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login status
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id'])) {
    $isLoggedIn = true;
    
    // Safely extract user data from session with fallbacks
    $userData = [
        'first_name' => $_SESSION['first_name'] ?? 'User',
        'last_name' => $_SESSION['last_name'] ?? '',
        'email' => $_SESSION['email'] ?? 'No email',
        'username' => $_SESSION['username'] ?? 'user'
    ];
    
    // Try to get user's profile picture from database
    try {
        // Try different paths to find config.php
        $configFound = false;
        $possiblePaths = [
            __DIR__ . '/../php/config.php',    // From parts/ directory
            __DIR__ . '/php/config.php',       // From root directory
            __DIR__ . '/../../php/config.php', // From deeper directories
            '../php/config.php',               // Relative path
            'php/config.php',                  // Root relative
            './php/config.php'                 // Current directory
        ];
        
        foreach ($possiblePaths as $configPath) {
            if (file_exists($configPath)) {
                require_once $configPath;
                $configFound = true;
                break;
            }
        }
        
        // Only try to connect to database if config was found
        if ($configFound && class_exists('Database')) {
            try {
                $db = Database::getInstance()->getConnection();
                
                // Get user's profile picture
                $stmt = $db->prepare("SELECT profile_picture FROM users WHERE id = ? LIMIT 1");
                $stmt->execute([$_SESSION['user_id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result && !empty($result['profile_picture'])) {
                    // Try different upload paths
                    $uploadPaths = [
                        '../uploads/profiles/',
                        'uploads/profiles/',
                        './uploads/profiles/',
                        '../../uploads/profiles/'
                    ];
                    
                    $profileFileName = htmlspecialchars($result['profile_picture']);
                    
                    // Use the first path that might work (we'll handle with onerror in HTML)
                    $profilePicture = '../uploads/profiles/' . $profileFileName;
                }
            } catch (Exception $e) {
                // Database error - use default image
                error_log("Navbar database error: " . $e->getMessage());
            }
        }
    } catch (Exception $e) {
        // Config file error - use default image
        error_log("Navbar config error: " . $e->getMessage());
    }
    
    // Default profile picture if none found or error occurred
    if (empty($profilePicture)) {
        $profilePicture = 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=40&h=40&q=80';
    }
}

// Helper function to determine correct paths
function getCorrectPath($relativePath) {
    $possiblePaths = [
        $relativePath,
        '../' . $relativePath,
        './' . $relativePath,
        '../../' . $relativePath
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    
    return $relativePath; // Return original if none found
}
?>
<!-- Navigation Bar -->
<nav class="navbar">
    <div class="container">
        <div class="navbar-brand">
            <a href="<?php echo $isLoggedIn ? 'index.php' : 'index.php'; ?>" class="brand-logo">
                <img src="<?php echo getCorrectPath('images/logo.svg'); ?>" class="logo-img" alt="Axiom Logo" 
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                <span style="display:none; color:#00d4ff; font-weight:bold; font-size:1.5rem;">AXIOM</span>
            </a>
        </div>
        
        <div class="navbar-menu" id="navbar-menu">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="index.php" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="about.php" class="nav-link">About</a>
                </li>
                <li class="nav-item">
                    <a href="services.php" class="nav-link">Services</a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link">Contact</a>
                </li>
                <?php if (!$isLoggedIn): ?>
                <li class="nav-item">
                    <a href="login.php" class="nav-link">Login</a>
                </li>
                <li class="nav-item">
                    <a href="register.php" class="nav-link btn-nav">Get Started</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        
        <?php if ($isLoggedIn): ?>
        <!-- Enhanced Profile Icon -->
        <div class="navbar-profile">
            <div class="profile-dropdown">
                <button class="profile-btn" id="profile-btn" 
                        title="<?php echo htmlspecialchars(trim($userData['first_name'] . ' ' . $userData['last_name']) ?: 'User Profile'); ?>">
                    <div class="profile-img-container">
                        <img src="<?php echo $profilePicture; ?>" 
                             alt="<?php echo htmlspecialchars($userData['first_name'] ?: 'User'); ?>'s Profile" 
                             class="profile-img"
                             onerror="this.src='https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=40&h=40&q=80'"
                             loading="lazy">
                        <div class="profile-status-indicator" title="Online"></div>
                    </div>
                    <svg class="dropdown-arrow" width="12" height="8" viewBox="0 0 12 8" fill="none">
                        <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="profile-menu" id="profile-menu">
                    <div class="profile-menu-header">
                        <div class="profile-header-content">
                            <img src="<?php echo $profilePicture; ?>" 
                                 alt="Profile" 
                                 class="profile-header-img"
                                 onerror="this.src='https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=50&h=50&q=80'"
                                 loading="lazy">
                            <div class="profile-header-info">
                                <strong><?php echo htmlspecialchars(trim($userData['first_name'] . ' ' . $userData['last_name']) ?: 'User Name'); ?></strong>
                                <small><?php echo htmlspecialchars($userData['email']); ?></small>
                                <span class="user-status">Active now</span>
                            </div>
                        </div>
                    </div>
                    <div class="profile-menu-items">
                        <a href="profile.php" class="profile-menu-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            My Profile
                        </a>
                        <a href="settings.php" class="profile-menu-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                            Settings
                       
                        </a>
                        <div class="profile-menu-divider"></div>
                        <a href="<?php echo getCorrectPath('php/logout.php'); ?>" class="profile-menu-item logout">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16,17 21,12 16,7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="navbar-toggle" id="navbar-toggle">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </div>
    </div>
</nav>

<script>
// Enhanced Responsive Navbar JavaScript - Error-Free Version
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    const navbarToggle = document.getElementById('navbar-toggle');
    const navbarMenu = document.getElementById('navbar-menu');
    const profileBtn = document.getElementById('profile-btn');
    const profileMenu = document.getElementById('profile-menu');
    const body = document.body;
    
    // Safely handle elements that might not exist
    function safeAddEventListener(element, event, handler) {
        if (element) {
            element.addEventListener(event, handler);
        }
    }
    
    // Mobile menu toggle
    safeAddEventListener(navbarToggle, 'click', function(e) {
        e.stopPropagation();
        navbarToggle.classList.toggle('active');
        if (navbarMenu) {
            navbarMenu.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            if (navbarMenu.classList.contains('active')) {
                body.style.overflow = 'hidden';
            } else {
                body.style.overflow = '';
            }
        }
    });
    
    // Profile dropdown toggle
    safeAddEventListener(profileBtn, 'click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        profileBtn.classList.toggle('active');
        if (profileMenu) {
            profileMenu.classList.toggle('active');
        }
        
        // Close mobile menu if open
        if (navbarMenu && navbarMenu.classList.contains('active')) {
            if (navbarToggle) navbarToggle.classList.remove('active');
            navbarMenu.classList.remove('active');
            body.style.overflow = '';
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        // Close profile menu
        if (profileBtn && profileMenu && 
            !profileBtn.contains(e.target) && 
            !profileMenu.contains(e.target)) {
            profileBtn.classList.remove('active');
            profileMenu.classList.remove('active');
        }
        
        // Close mobile menu
        if (navbarToggle && navbarMenu && 
            !navbarToggle.contains(e.target) && 
            !navbarMenu.contains(e.target)) {
            navbarToggle.classList.remove('active');
            navbarMenu.classList.remove('active');
            body.style.overflow = '';
        }
    });
    
    // Prevent profile menu from closing when clicking inside it
    safeAddEventListener(profileMenu, 'click', function(e) {
        e.stopPropagation();
    });
    
    // Close mobile menu when clicking on a link
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navbarToggle && navbarMenu) {
                navbarToggle.classList.remove('active');
                navbarMenu.classList.remove('active');
                body.style.overflow = '';
            }
        });
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            // Reset mobile menu on desktop
            if (navbarToggle && navbarMenu) {
                navbarToggle.classList.remove('active');
                navbarMenu.classList.remove('active');
                body.style.overflow = '';
            }
        }
    });
    
    // Handle orientation change on mobile
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            if (navbarMenu && navbarMenu.classList.contains('active')) {
                if (navbarToggle) navbarToggle.classList.remove('active');
                navbarMenu.classList.remove('active');
                body.style.overflow = '';
            }
        }, 100);
    });
    
    // Navbar scroll effect
    let lastScrollTop = 0;
    window.addEventListener('scroll', function() {
        if (!navbar) return;
        
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        // Hide navbar on scroll down, show on scroll up (mobile only)
        if (window.innerWidth <= 768) {
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        
        lastScrollTop = scrollTop;
    });
    
    // Enhanced active link highlighting
    try {
        const currentPage = window.location.pathname.split('/').pop() || 'index.php';
        const currentPageWithoutExtension = currentPage.replace('.php', '');
        
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            
            const linkHref = link.getAttribute('href');
            if (linkHref) {
                const linkPage = linkHref.replace('.php', '');
                
                // Check for exact match or if we're on index and link is home
                if (linkHref === currentPage || 
                    linkHref.includes(currentPage) ||
                    (currentPageWithoutExtension === 'index' && linkPage === 'index') ||
                    (currentPage === '' && linkPage === 'index')) {
                    link.classList.add('active');
                }
            }
        });
    } catch (e) {
        console.log('Active link highlighting failed:', e.message);
    }
    
    // Global profile image update function
    window.updateNavbarProfileImage = function(newImageSrc) {
        try {
            const profileImages = document.querySelectorAll('.profile-img, .profile-header-img');
            profileImages.forEach(img => {
                if (img) {
                    img.src = newImageSrc;
                }
            });
        } catch (e) {
            console.log('Profile image update failed:', e.message);
        }
    };
    
    // Profile image error handling
    const profileImages = document.querySelectorAll('.profile-img, .profile-header-img');
    profileImages.forEach(img => {
        if (img) {
            img.addEventListener('error', function() {
                this.src = 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=40&h=40&q=80';
            });
        }
    });
});
</script>

<style>

.profile-menu-header {
    padding: 16px;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 8px;
    background: linear-gradient(135deg, rgba(108, 99, 255, 0.05) 0%, rgba(159, 122, 234, 0.05) 100%);
}

.profile-header-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.profile-header-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e9ecef;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.profile-header-info {
    flex: 1;
    min-width: 0;
}

.profile-header-info strong {
    display: block;
    color: #333;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.profile-header-info small {
    color: #666;
    font-size: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    margin-bottom: 2px;
}

.user-status {
    display: inline-block;
    background: #28a745;
    color: white;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.profile-menu-items {
    padding: 8px 0;
}

.profile-menu-divider {
    height: 1px;
    background-color: #e9ecef;
    margin: 8px 0;
}

.profile-img-container {
    position: relative;
    display: inline-block;
}

.profile-img {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.profile-btn:hover .profile-img {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.profile-status-indicator {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 12px;
    height: 12px;
    background-color: #28a745;
    border: 2px solid #ffffff;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    animation: pulse-status 2s infinite;
}

@keyframes pulse-status {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
    70% { box-shadow: 0 0 0 4px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}

.profile-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 8px;
    transition: all 0.3s ease;
    color: #ffffff;
}

.profile-btn:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.profile-btn.active {
    background-color: rgba(108, 99, 255, 0.2);
}

.dropdown-arrow {
    transition: transform 0.3s ease;
    color: #ffffff;
}

.profile-btn.active .dropdown-arrow {
    transform: rotate(180deg);
}

.profile-dropdown {
    position: relative;
}

.profile-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    min-width: 280px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border: 1px solid #e9ecef;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
    margin-top: 8px;
    overflow: hidden;
}

.profile-menu.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.profile-menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: #333;
    text-decoration: none;
    transition: all 0.2s ease;
    font-size: 14px;
    border-radius: 8px;
    margin: 0 8px;
    font-weight: 500;
}

.profile-menu-item:hover {
    background-color: #f8f9fa;
    color: #6c63ff;
    transform: translateX(4px);
}

.profile-menu-item.logout {
    color: #dc3545;
}

.profile-menu-item.logout:hover {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.profile-menu-item svg {
    flex-shrink: 0;
}

.navbar-toggle {
    display: none;
    flex-direction: column;
    cursor: pointer;
    padding: 4px;
    gap: 4px;
}

.hamburger-line {
    width: 25px;
    height: 3px;
    background-color: #ffffff;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.navbar-toggle.active .hamburger-line:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.navbar-toggle.active .hamburger-line:nth-child(2) {
    opacity: 0;
}

.navbar-toggle.active .hamburger-line:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -6px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .navbar-menu {
        position: fixed;
        top: 100%;
        left: 0;
        right: 0;
        background: rgba(26, 26, 46, 0.98);
        backdrop-filter: blur(20px);
        flex-direction: column;
        padding: 2rem 1rem;
        transform: translateY(-100%);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .navbar-menu.active {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    }
    
    .navbar-nav {
        flex-direction: column;
        gap: 1.5rem;
        width: 100%;
    }
    
    .navbar-toggle {
        display: flex;
    }
    
    .profile-menu {
        min-width: 260px;
        right: -8px;
    }
    
    .profile-header-content {
        gap: 10px;
    }
    
    .profile-header-img {
        width: 45px;
        height: 45px;
    }
    
    .profile-img {
        width: 32px;
        height: 32px;
    }
    
    .profile-status-indicator {
        width: 10px;
        height: 10px;
    }
}

@media (max-width: 480px) {
    .profile-menu {
        min-width: 240px;
        right: -16px;
    }
    
    .profile-header-info strong {
        font-size: 13px;
    }
    
    .profile-header-info small {
        font-size: 11px;
    }
    
    .profile-menu-item {
        padding: 10px 12px;
        font-size: 13px;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .profile-status-indicator {
        animation: none;
    }
    
    .profile-img,
    .profile-btn,
    .profile-menu-item,
    .navbar,
    .profile-menu {
        transition: none;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .profile-status-indicator {
        border-width: 3px;
    }
    
    .profile-menu {
        border-width: 2px;
        border-color: #000;
    }
    
    .navbar {
        border-bottom-width: 2px;
    }
}

/* Error handling styles */
.profile-img.error,
.profile-header-img.error {
    border-color: #dc3545;
    opacity: 0.8;
}

/* Loading states */
.profile-img[src*="uploads/"]:not([src*="?t="]) {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Ensure proper spacing from navbar */
body {
    padding-top: 70px;
}

/* Focus states for accessibility */
.profile-btn:focus-visible,
.nav-link:focus-visible,
.profile-menu-item:focus-visible {
    outline: 2px solid #00d4ff;
    outline-offset: 2px;
}

/* Print styles */
@media print {
    .navbar {
        display: none;
    }
    
    body {
        padding-top: 0;
    }
}
</style>