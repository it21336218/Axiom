 $(document).ready(function() {
            // Import navbar from navbar.html
            $("#navbar-container").load("navbar.html", function() {
                // Set active navigation item
                $('.nav-links a[href="register.html"]').addClass('active');
            });
            
            // Import footer from footer.html
            $("#footer-container").load("footer.html");
        });

        // Mobile menu toggle
        $(document).on('click', '.nav-toggle', function() {
            $('.mobile-menu').toggleClass('active');
            
            if ($('.mobile-menu').hasClass('active')) {
                $(this).html('✕');
            } else {
                $(this).html('☰');
            }
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.nav-toggle, .mobile-menu').length) {
                $('.mobile-menu').removeClass('active');
                $('.nav-toggle').html('☰');
            }
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

        // Form validation
        const validators = {
            firstName: {
                validate: (value) => value.trim().length >= 2,
                message: 'First name must be at least 2 characters'
            },
            lastName: {
                validate: (value) => value.trim().length >= 2,
                message: 'Last name must be at least 2 characters'
            },
            username: {
                validate: (value) => /^[a-zA-Z0-9_]{3,20}$/.test(value),
                message: 'Username must be 3-20 characters (letters, numbers, underscore only)'
            },
            email: {
                validate: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
                message: 'Please enter a valid email address'
            },
            password: {
                validate: (value) => value.length >= 8,
                message: 'Password must be at least 8 characters'
            },
            confirmPassword: {
                validate: (value) => value === $('#password').val(),
                message: 'Passwords do not match'
            }
        };

        // Real-time validation
        Object.keys(validators).forEach(fieldName => {
            $(`#${fieldName}`).on('blur input', function() {
                validateField(fieldName, $(this).val());
            });
        });

        function validateField(fieldName, value) {
            const validator = validators[fieldName];
            const field = $(`#${fieldName}`);
            const validation = $(`#${fieldName}Validation`);
            
            if (validator && value.trim()) {
                const isValid = validator.validate(value);
                
                field.removeClass('valid invalid').addClass(isValid ? 'valid' : 'invalid');
                validation.removeClass('valid invalid show').addClass(isValid ? 'valid' : 'invalid');
                
                if (!isValid) {
                    validation.text(validator.message).addClass('show');
                } else {
                    validation.text('✓ Valid').addClass('show');
                }
            } else {
                field.removeClass('valid invalid');
                validation.removeClass('show');
            }
        }

        // Username availability check (simulated)
        let usernameTimeout;
        $('#username').on('input', function() {
            const username = $(this).val();
            
            clearTimeout(usernameTimeout);
            
            if (username.length >= 3) {
                usernameTimeout = setTimeout(() => {
                    checkUsernameAvailability(username);
                }, 500);
            }
        });

        function checkUsernameAvailability(username) {
            const validation = $('#usernameValidation');
            
            // Simulate API call
            setTimeout(() => {
                const isAvailable = !['admin', 'user', 'test', 'demo'].includes(username.toLowerCase());
                
                if (isAvailable) {
                    validation.removeClass('invalid').addClass('valid show');
                    validation.text('✓ Username is available');
                    $('#username').removeClass('invalid').addClass('valid');
                } else {
                    validation.removeClass('valid').addClass('invalid show');
                    validation.text('Username is not available');
                    $('#username').removeClass('valid').addClass('invalid');
                }
            }, 300);
        }

        // Form submission
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            
            // Validate all fields
            let isFormValid = true;
            const formData = {};
            
            Object.keys(validators).forEach(fieldName => {
                const value = $(`#${fieldName}`).val();
                formData[fieldName] = value;
                
                if (!validators[fieldName].validate(value)) {
                    isFormValid = false;
                    validateField(fieldName, value);
                }
            });

            // Check terms and conditions
            if (!$('#terms').is(':checked')) {
                showMessage('Please accept the Terms of Service and Privacy Policy', 'error');
                isFormValid = false;
            }

            if (!isFormValid) {
                showMessage('Please fix the errors above', 'error');
                return;
            }

            // Show loading state
            const submitBtn = $('.submit-btn');
            const btnText = $('.btn-text');
            const btnLoading = $('.btn-loading');
            
            submitBtn.prop('disabled', true);
            btnText.hide();
            btnLoading.show();

            // Simulate registration API call
            setTimeout(() => {
                // Reset button state
                submitBtn.prop('disabled', false);
                btnText.show();
                btnLoading.hide();

                // Simulate success (replace with actual API call)
                const success = Math.random() > 0.2; // 80% success rate for demo

                if (success) {
                    showMessage('Account created successfully! Redirecting to login...', 'success');
                    
                    // Redirect after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'login.html';
                    }, 2000);
                } else {
                    showMessage('Registration failed. Please try again.', 'error');
                }
            }, 2000);
        });

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

        // Smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $(this.getAttribute('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 800);
            }
        });

        // Navbar scroll effect
        $(window).on('scroll', function() {
            const navbar = $('.navbar');
            if ($(window).scrollTop() > 50) {
                navbar.css({
                    'background': 'rgba(15, 15, 35, 0.95)',
                    'box-shadow': '0 5px 20px rgba(0, 0, 0, 0.3)'
                });
            } else {
                navbar.css({
                    'background': 'rgba(15, 15, 35, 0.8)',
                    'box-shadow': 'none'
                });
            }
        });

        // Animate elements on scroll
        function animateOnScroll() {
            $('.welcome-content, .register-form-container').each(function() {
                const element = $(this);
                const elementTop = element.offset().top;
                const windowBottom = $(window).scrollTop() + $(window).height();
                
                if (elementTop < windowBottom - 100) {
                    element.css({
                        'opacity': '1',
                        'transform': 'translateY(0)'
                    });
                }
            });
        }

        // Initial setup for animations
        $('.welcome-content, .register-form-container').css({
            'opacity': '0',
            'transform': 'translateY(30px)',
            'transition': 'all 0.6s ease'
        });

        $(window).on('scroll', animateOnScroll);
        $(document).ready(animateOnScroll);

        // Auto-focus first field
        $(document).ready(function() {
            setTimeout(() => {
                $('#firstName').focus();
            }, 500);
        });

        // Prevent form submission on Enter in non-submit elements
        $('.form-control').on('keypress', function(e) {
            if (e.which === 13 && $(this).attr('type') !== 'submit') {
                e.preventDefault();
                const formElements = $('.form-control');
                const currentIndex = formElements.index(this);
                const nextElement = formElements.eq(currentIndex + 1);
                
                if (nextElement.length) {
                    nextElement.focus();
                } else {
                    $('#registerForm').submit();
                }
            }
        });

        // Enhanced security features
        function detectPasswordPatterns(password) {
            const patterns = {
                sequential: /(?:abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz|123|234|345|456|567|678|789)/i,
                repeated: /(.)\1{2,}/,
                keyboard: /(?:qwe|wer|ert|rty|tyu|yui|uio|iop|asd|sdf|dfg|fgh|ghj|hjk|jkl|zxc|xcv|cvb|vbn|bnm)/i
            };
            
            const warnings = [];
            
            if (patterns.sequential.test(password)) {
                warnings.push('Avoid sequential characters');
            }
            if (patterns.repeated.test(password)) {
                warnings.push('Avoid repeated characters');
            }
            if (patterns.keyboard.test(password)) {
                warnings.push('Avoid keyboard patterns');
            }
            
            return warnings;
        }

        $('#password').on('input', function() {
            const password = $(this).val();
            const warnings = detectPasswordPatterns(password);
            
            if (warnings.length > 0 && password.length > 4) {
                const validation = $('#passwordValidation');
                validation.removeClass('valid').addClass('invalid show');
                validation.text('⚠️ ' + warnings[0]);
            }
        });

        // Check for password leaks (simulated - in real app, use HaveIBeenPwned API)
        function checkPasswordLeak(password) {
            const commonPasswords = [
                'password', '123456', 'password123', 'admin', 'qwerty',
                'letmein', 'welcome', 'monkey', '1234567890', 'abc123'
            ];
            
            return commonPasswords.includes(password.toLowerCase());
        }

        $('#password').on('blur', function() {
            const password = $(this).val();
            
            if (password.length > 0 && checkPasswordLeak(password)) {
                const validation = $('#passwordValidation');
                validation.removeClass('valid').addClass('invalid show');
                validation.text('🚨 This password has been found in data breaches. Please choose a different one.');
            }
        });

        // Accessibility improvements
        $('.form-control').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            $(this).parent().removeClass('focused');
        });

        // Keyboard navigation for custom checkbox
        $('#terms').on('keypress', function(e) {
            if (e.which === 13 || e.which === 32) {
                e.preventDefault();
                $(this).prop('checked', !$(this).prop('checked'));
            }
        });