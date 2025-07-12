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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/auth.css">
    <title>Register - Axiom</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Fixed checkbox styles for better integration */
        .terms-group {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            cursor: pointer;
        }

        .custom-checkbox {
            position: relative;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
            z-index: 1;
        }

        .checkbox-mark {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--glass);
            border: 2px solid var(--glass-border);
            border-radius: 4px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .checkbox-mark::after {
            content: '';
            position: absolute;
            left: 6px;
            top: 2px;
            width: 6px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg) scale(0);
            transition: transform 0.2s ease;
        }

        .custom-checkbox input:checked + .checkbox-mark {
            background: linear-gradient(45deg, var(--primary), var(--accent));
            border-color: var(--accent);
        }

        .custom-checkbox input:checked + .checkbox-mark::after {
            transform: rotate(45deg) scale(1);
        }

        .custom-checkbox input:focus + .checkbox-mark {
            box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.1);
        }

        .terms-text {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.5;
            cursor: pointer;
            user-select: none;
        }

        .terms-text a {
            color: var(--accent);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .terms-text a:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        /* Error state for checkbox */
        .terms-group.error .checkbox-mark {
            border-color: var(--error);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Field validation styles */
        .field-validation {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            display: none;
            min-height: 1.2rem;
        }

        .field-validation.show {
            display: block;
        }

        .field-validation.valid {
            color: var(--success);
        }

        .field-validation.invalid {
            color: var(--error);
        }

        .form-control.valid {
            border-color: var(--success);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .form-control.invalid {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
    </style>
</head>
<body>
    <?php include '../parts/navbar.php'; ?>

    <main class="main-content">
        <!-- Registration Section -->
        <section class="register-section">
            <div class="container">
                <div class="register-container">
                    <!-- Welcome Section -->
                    <div class="welcome-section">
                        <div class="welcome-content">
                            <h1 class="welcome-title">Join Axiom</h1>
                            <p class="welcome-subtitle">Create your account and start your journey with our amazing platform</p>
                            
                            <ul class="features-list">
                                <li>Access to premium features</li>
                                <li>24/7 customer support</li>
                                <li>Secure data protection</li>
                                <li>Cross-platform synchronization</li>
                                <li>Regular updates and improvements</li>
                            </ul>
                            
                            <p class="login-link">Already have an account? <a href="login.php">Sign in here</a></p>
                        </div>
                    </div>

                    <!-- Registration Form -->
                    <div class="register-form-container">
                        <h2 class="form-title">Create Account</h2>
                        <p class="form-subtitle">Fill in your details to get started</p>
                        
                        <div id="message" class="message"></div>
                        
                        <form id="registerForm" class="register-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First Name *</label>
                                    <input type="text" id="firstName" name="firstName" class="form-control" placeholder="John" required>
                                    <div class="field-validation" id="firstNameValidation"></div>
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name *</label>
                                    <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Doe" required>
                                    <div class="field-validation" id="lastNameValidation"></div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="username">Username *</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="johndoe" required>
                                <div class="field-validation" id="usernameValidation"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required>
                                <div class="field-validation" id="emailValidation"></div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="+1 (234) 567-890">
                                </div>
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <select id="country" name="country" class="form-control">
                                        <option value="">Select Country</option>
                                        <option value="US">United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="UK">United Kingdom</option>
                                        <option value="AU">Australia</option>
                                        <option value="DE">Germany</option>
                                        <option value="FR">France</option>
                                        <option value="JP">Japan</option>
                                        <option value="IN">India</option>
                                        <option value="BR">Brazil</option>
                                        <option value="MX">Mexico</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Password *</label>
                                <div class="password-container">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Create a strong password" required>
                                    <button type="button" class="password-toggle" id="passwordToggle">👁️</button>
                                </div>
                                <div class="password-strength" id="passwordStrength">
                                    <div class="strength-bar">
                                        <div class="strength-fill" id="strengthFill"></div>
                                    </div>
                                    <div class="strength-text" id="strengthText">Password strength: Weak</div>
                                </div>
                                <div class="field-validation" id="passwordValidation"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirmPassword">Confirm Password *</label>
                                <div class="password-container">
                                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm your password" required>
                                    <button type="button" class="password-toggle" id="confirmPasswordToggle">👁️</button>
                                </div>
                                <div class="field-validation" id="confirmPasswordValidation"></div>
                            </div>
                            
                            <div class="terms-group" id="termsGroup">
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="terms" name="terms" required>
                                    <div class="checkbox-mark"></div>
                                </div>
                                <div class="terms-text">
                                    I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>
                                </div>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                <span class="btn-text">Create Account</span>
                                <span class="btn-loading" style="display: none;">
                                    <span class="loading"></span> Creating Account...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include '../parts/footer.php'; ?>
    
    <script>
        $(document).ready(function() {
            // Fix checkbox clicking - Make entire terms group clickable
            $('#termsGroup, .terms-text').on('click', function(e) {
                // Don't trigger if clicking on a link
                if (e.target.tagName === 'A') {
                    return;
                }
                
                e.preventDefault();
                const checkbox = $('#terms');
                checkbox.prop('checked', !checkbox.prop('checked'));
                
                // Remove error styling when checked
                if (checkbox.prop('checked')) {
                    $('#termsGroup').removeClass('error');
                }
            });

            // Prevent double-click on actual checkbox
            $('#terms').on('click', function(e) {
                e.stopPropagation();
            });

            // Password toggle functionality
            $('.password-toggle').on('click', function() {
                const passwordField = $(this).siblings('input');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).text(type === 'password' ? '👁️' : '🙈');
            });

            // Password strength checker
            $('#password').on('input', function() {
                const password = $(this).val();
                const strength = calculatePasswordStrength(password);
                updatePasswordStrength(strength);
                
                if (password.length > 0) {
                    $('#passwordStrength').addClass('show');
                } else {
                    $('#passwordStrength').removeClass('show');
                }
            });

            // Confirm password validation
            $('#confirmPassword').on('input', function() {
                const password = $('#password').val();
                const confirmPassword = $(this).val();
                const validation = $('#confirmPasswordValidation');
                
                if (confirmPassword.length === 0) {
                    validation.removeClass('show');
                    $(this).removeClass('valid invalid');
                    return;
                }
                
                if (password === confirmPassword) {
                    validation.removeClass('invalid').addClass('valid show');
                    validation.text('✓ Passwords match');
                    $(this).removeClass('invalid').addClass('valid');
                } else {
                    validation.removeClass('valid').addClass('invalid show');
                    validation.text('Passwords do not match');
                    $(this).removeClass('valid').addClass('invalid');
                }
            });

            // Username validation with backend field names
            let usernameTimeout;
            $('#username').on('input', function() {
                const username = $(this).val();
                const validation = $('#usernameValidation');
                
                clearTimeout(usernameTimeout);
                
                if (username.length >= 3) {
                    if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
                        validation.removeClass('valid').addClass('invalid show');
                        validation.text('Username must be 3-20 characters (letters, numbers, underscore only)');
                        $(this).removeClass('valid').addClass('invalid');
                        return;
                    }
                    
                    usernameTimeout = setTimeout(() => {
                        checkUsernameAvailability(username);
                    }, 500);
                } else if (username.length > 0) {
                    validation.removeClass('valid').addClass('invalid show');
                    validation.text('Username must be at least 3 characters');
                    $(this).removeClass('valid').addClass('invalid');
                } else {
                    validation.removeClass('show');
                    $(this).removeClass('valid invalid');
                }
            });

            // Email validation
            $('#email').on('blur', function() {
                const email = $(this).val();
                const validation = $('#emailValidation');
                
                if (email.length === 0) {
                    validation.removeClass('show');
                    $(this).removeClass('valid invalid');
                    return;
                }
                
                if (isValidEmail(email)) {
                    validation.removeClass('invalid').addClass('valid show');
                    validation.text('✓ Valid email address');
                    $(this).removeClass('invalid').addClass('valid');
                } else {
                    validation.removeClass('valid').addClass('invalid show');
                    validation.text('Please enter a valid email address');
                    $(this).removeClass('valid').addClass('invalid');
                }
            });

            // Form submission with proper backend integration
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validate all fields before submission
                if (!validateForm()) {
                    showMessage('Please fix the errors above', 'error');
                    return;
                }
                
                const submitBtn = $('.submit-btn');
                const btnText = $('.btn-text');
                const btnLoading = $('.btn-loading');
                
                // Show loading state
                submitBtn.prop('disabled', true);
                btnText.hide();
                btnLoading.show();
                
                // Submit form via AJAX to the correct handler
                $.ajax({
                    url: '../php/register_handler.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, 'success');
                            
                            // Redirect after success
                            setTimeout(function() {
                                window.location.href = response.data.redirect || 'login.php';
                            }, 2000);
                        } else {
                            showMessage(response.message, 'error');
                            
                            // Show field-specific errors
                            if (response.errors) {
                                Object.keys(response.errors).forEach(function(field) {
                                    // Map backend field names to frontend field names
                                    const fieldMap = {
                                        'first_name': 'firstName',
                                        'last_name': 'lastName',
                                        'confirm_password': 'confirmPassword'
                                    };
                                    
                                    const frontendField = fieldMap[field] || field;
                                    const validation = $('#' + frontendField + 'Validation');
                                    const fieldElement = $('#' + frontendField);
                                    
                                    if (validation.length) {
                                        validation.removeClass('valid').addClass('invalid show');
                                        validation.text(response.errors[field]);
                                        fieldElement.removeClass('valid').addClass('invalid');
                                    }
                                });
                            }
                            
                            // Reset form state
                            submitBtn.prop('disabled', false);
                            btnText.show();
                            btnLoading.hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'Registration failed. Please try again.';
                        
                        // Try to parse error response
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMessage = errorResponse.message;
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                        
                        showMessage(errorMessage, 'error');
                        
                        // Reset form state
                        submitBtn.prop('disabled', false);
                        btnText.show();
                        btnLoading.hide();
                    }
                });
            });

            function calculatePasswordStrength(password) {
                let score = 0;
                const checks = {
                    length: password.length >= 8,
                    lowercase: /[a-z]/.test(password),
                    uppercase: /[A-Z]/.test(password),
                    numbers: /\d/.test(password),
                    special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
                };
                
                Object.values(checks).forEach(check => {
                    if (check) score++;
                });
                
                if (password.length >= 12) score++;
                
                return Math.min(score, 4);
            }

            function updatePasswordStrength(strength) {
                const strengthFill = $('#strengthFill');
                const strengthText = $('#strengthText');
                
                strengthFill.removeClass('weak fair good strong');
                
                switch(strength) {
                    case 0:
                    case 1:
                        strengthFill.addClass('weak');
                        strengthText.text('Password strength: Weak');
                        break;
                    case 2:
                        strengthFill.addClass('fair');
                        strengthText.text('Password strength: Fair');
                        break;
                    case 3:
                        strengthFill.addClass('good');
                        strengthText.text('Password strength: Good');
                        break;
                    case 4:
                    default:
                        strengthFill.addClass('strong');
                        strengthText.text('Password strength: Strong');
                        break;
                }
            }

            function checkUsernameAvailability(username) {
                const validation = $('#usernameValidation');
                const fieldElement = $('#username');
                
                // Simulate username check (in real app, make AJAX call to check availability)
                setTimeout(() => {
                    const unavailableUsernames = ['admin', 'user', 'test', 'demo', 'root', 'administrator'];
                    const isAvailable = !unavailableUsernames.includes(username.toLowerCase());
                    
                    if (isAvailable) {
                        validation.removeClass('invalid').addClass('valid show');
                        validation.text('✓ Username is available');
                        fieldElement.removeClass('invalid').addClass('valid');
                    } else {
                        validation.removeClass('valid').addClass('invalid show');
                        validation.text('Username is not available');
                        fieldElement.removeClass('valid').addClass('invalid');
                    }
                }, 300);
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function validateForm() {
                let isValid = true;
                
                // Check required fields
                const requiredFields = ['firstName', 'lastName', 'username', 'email', 'password', 'confirmPassword'];
                requiredFields.forEach(function(fieldId) {
                    const field = $('#' + fieldId);
                    if (!field.val().trim()) {
                        field.addClass('invalid');
                        isValid = false;
                    }
                });
                
                // Check terms acceptance
                if (!$('#terms').is(':checked')) {
                    $('#termsGroup').addClass('error');
                    isValid = false;
                }
                
                // Check password match
                if ($('#password').val() !== $('#confirmPassword').val()) {
                    isValid = false;
                }
                
                // Check email validity
                const email = $('#email').val();
                if (email && !isValidEmail(email)) {
                    isValid = false;
                }
                
                // Check username validity
                const username = $('#username').val();
                if (username && !/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
                    isValid = false;
                }
                
                return isValid;
            }

            function showMessage(text, type) {
                const messageDiv = $('#message');
                messageDiv.removeClass('success error').addClass(type + ' show');
                messageDiv.text(text);
                
                // Auto-hide error messages after 5 seconds
                if (type === 'error') {
                    setTimeout(() => {
                        messageDiv.removeClass('show');
                    }, 5000);
                }
            }

            // Real-time validation for all fields
            $('.form-control').on('input', function() {
                const field = $(this);
                const fieldId = field.attr('id');
                
                // Remove error styling on input
                field.removeClass('invalid');
                
                // Clear validation message if it was showing an error
                const validation = $('#' + fieldId + 'Validation');
                if (validation.hasClass('invalid')) {
                    validation.removeClass('show');
                }
            });

            // Keyboard accessibility for checkbox
            $('#terms').on('keypress', function(e) {
                if (e.which === 13 || e.which === 32) {
                    e.preventDefault();
                    $(this).prop('checked', !$(this).prop('checked'));
                    
                    if ($(this).prop('checked')) {
                        $('#termsGroup').removeClass('error');
                    }
                }
            });

            // Phone number formatting
            $('#phone').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                
                if (value.length >= 10) {
                    value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
                } else if (value.length >= 6) {
                    value = value.replace(/(\d{3})(\d{3})/, '($1) $2-');
                } else if (value.length >= 3) {
                    value = value.replace(/(\d{3})/, '($1) ');
                }
                
                $(this).val(value);
            });

            // Auto-focus first field
            setTimeout(() => {
                $('#firstName').focus();
            }, 500);
        });
    </script>
</body>
</html>