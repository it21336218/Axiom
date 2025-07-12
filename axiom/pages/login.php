<?php
// Check if user is already logged in
session_start();
define('AXIOM_ACCESS', true);
require_once '../php/auth.php';

$auth = new Auth();
if ($auth->isLoggedIn()) {
    header('Location: profile.php');
    exit;
}

// Check for messages
$message = $_SESSION['logout_message'] ?? $_SESSION['error_message'] ?? '';
$messageType = isset($_SESSION['logout_message']) ? 'success' : 'error';
unset($_SESSION['logout_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/auth.css">
    <title>Login - Axiom</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
    </div>

    <?php include '../parts/navbar.php'; ?>

    <main class="main-content">
        <!-- Login Section -->
        <section class="login-section">
            <div class="container">
                <div class="login-container">
                    <!-- Welcome Section -->
                    <div class="welcome-section">
                        <div class="welcome-content">
                            <h1 class="welcome-title">Welcome Back!</h1>
                            <p class="welcome-subtitle">Sign in to access your account and continue your journey with Axiom</p>
                            
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-number">10k+</div>
                                    <div class="stat-label">Active Users</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">99.9%</div>
                                    <div class="stat-label">Uptime</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">24/7</div>
                                    <div class="stat-label">Support</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">50+</div>
                                    <div class="stat-label">Countries</div>
                                </div>
                            </div>
                            
                            <p class="register-link">Don't have an account? <a href="register.php">Create one here</a></p>
                        </div>
                    </div>

                    <!-- Login Form -->
                    <div class="login-form-container">
                        <h2 class="form-title">Sign In</h2>
                        <p class="form-subtitle">Enter your credentials to access your account</p>
                        
                        <div id="message" class="message <?php echo $messageType; ?> <?php echo $message ? 'show' : ''; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                        
                        <form id="loginForm" class="login-form">
                            <div class="form-group">
                                <label for="email">Email Address or Username</label>
                                <input type="text" id="email" name="email" class="form-control" placeholder="Enter your email or username" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="password-container">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                                    <button type="button" class="password-toggle" id="passwordToggle">👁️</button>
                                </div>
                            </div>
                            
                            <div class="form-options">
                                <div class="remember-me">
                                    <div class="custom-checkbox">
                                        <input type="checkbox" id="remember" name="remember">
                                        <div class="checkbox-mark"></div>
                                    </div>
                                    <label for="remember" class="remember-text">Remember me</label>
                                </div>
                                <a href="#" class="forgot-password" id="forgotPassword">Forgot Password?</a>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                <span class="btn-text">Sign In</span>
                                <span class="btn-loading" style="display: none;">
                                    <span class="loading"></span> Signing In...
                                </span>
                            </button>
                        </form>

                        <!-- Social Login -->
                        <div class="social-divider">
                            <div class="divider-line"></div>
                            <span class="divider-text">or continue with</span>
                            <div class="divider-line"></div>
                        </div>

                        <div class="social-buttons">
                            <a href="#" class="social-btn" id="googleLogin">
                                <span>🔍</span>
                                Google
                            </a>
                            <a href="#" class="social-btn" id="facebookLogin">
                                <span>📘</span>
                                Facebook
                            </a>
                        </div>
                        
                        <!-- Demo Credentials -->
                        <div style="margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px; text-align: center;">
                            <small style="color: var(--text-muted);">Demo Credentials:</small><br>
                            <small style="color: var(--accent);">admin@axiom.local / admin123</small><br>
                            <small style="color: var(--accent);">demo@axiom.local / demo123</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include '../parts/footer.php'; ?>
    
    <script>
        $(document).ready(function() {
            // Auto-hide messages after 5 seconds
            setTimeout(function() {
                $('#message').removeClass('show');
            }, 5000);

            // Password toggle functionality
            $('#passwordToggle').on('click', function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).text(type === 'password' ? '👁️' : '🙈');
            });

            // Form submission with proper AJAX integration
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = $('.submit-btn');
                const btnText = $('.btn-text');
                const btnLoading = $('.btn-loading');
                const messageDiv = $('#message');
                
                // Clear previous messages
                messageDiv.removeClass('show');
                
                // Show loading state
                submitBtn.prop('disabled', true);
                btnText.hide();
                btnLoading.show();
                
                // Submit form via AJAX to the correct handler
                $.ajax({
                    url: '../php/login_handler.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            messageDiv.removeClass('error').addClass('success show');
                            messageDiv.text(response.message);
                            
                            // Redirect after success
                            setTimeout(function() {
                                window.location.href = response.data.redirect || 'profile.php';
                            }, 1500);
                        } else {
                            messageDiv.removeClass('success').addClass('error show');
                            messageDiv.text(response.message);
                            
                            // Reset form state
                            submitBtn.prop('disabled', false);
                            btnText.show();
                            btnLoading.hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'Login failed. Please try again.';
                        
                        // Try to parse error response
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMessage = errorResponse.message;
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                        
                        messageDiv.removeClass('success').addClass('error show');
                        messageDiv.text(errorMessage);
                        
                        // Reset form state
                        submitBtn.prop('disabled', false);
                        btnText.show();
                        btnLoading.hide();
                    }
                });
            });

            // Demo credentials functionality - press 'd' twice quickly
            let demoKeyCount = 0;
            $(document).on('keydown', function(e) {
                if (e.key === 'd') {
                    demoKeyCount++;
                    if (demoKeyCount === 2) {
                        fillDemoCredentials();
                        demoKeyCount = 0;
                    }
                    setTimeout(() => { demoKeyCount = 0; }, 1000);
                }
            });

            function fillDemoCredentials() {
                $('#email').val('admin@axiom.local');
                $('#password').val('admin123');
                showMessage('Demo credentials filled! You can now sign in.', 'success');
            }

            function showMessage(text, type) {
                const messageDiv = $('#message');
                messageDiv.removeClass('success error').addClass(type + ' show');
                messageDiv.text(text);
                
                setTimeout(() => {
                    messageDiv.removeClass('show');
                }, 5000);
            }

            // Social login placeholders
            $('.social-btn').on('click', function(e) {
                e.preventDefault();
                const platform = $(this).text().trim();
                showMessage(platform + ' login not yet implemented', 'error');
            });

            // Forgot password placeholder
            $('#forgotPassword').on('click', function(e) {
                e.preventDefault();
                showMessage('Password reset feature coming soon!', 'error');
            });

            // Real-time validation
            $('.form-control').on('blur', function() {
                const field = $(this);
                const value = field.val().trim();
                
                field.removeClass('valid invalid');
                
                if (field.prop('required') && !value) {
                    field.addClass('invalid');
                } else if (value) {
                    field.addClass('valid');
                }
            });

            // Remove validation styling on input
            $('.form-control').on('input', function() {
                $(this).removeClass('invalid');
            });
        });
    </script>
</body>
</html>