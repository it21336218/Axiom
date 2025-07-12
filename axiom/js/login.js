 
//          .scroll-reveal {        
 $(document).ready(function() {
            // Import navbar from navbar.html
            $("#navbar-container").load("navbar.html", function() {
                // Set active navigation item
                $('.nav-links a[href="login.html"]').addClass('active');
            });
            
            // Import footer from footer.html
            $("#footer-container").load("footer.html");

            // Initialize animations
            initializeAnimations();
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
        $('#passwordToggle').on('click', function() {
            const passwordField = $('#password');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).text(type === 'password' ? '👁️' : '🙈');
        });

        // Form field focus animations
        $('.form-control').on('focus', function() {
            $(this).closest('.form-group').addClass('focused');
            $(this).addClass('focused');
        }).on('blur', function() {
            $(this).closest('.form-group').removeClass('focused');
            $(this).removeClass('focused');
            
            // Validate on blur
            validateField($(this));
        });

        // Real-time validation
        function validateField(field) {
            const value = field.val().trim();
            const fieldType = field.attr('id');
            
            if (fieldType === 'email') {
                const isValid = value && (isValidEmail(value) || value.length >= 3);
                field.removeClass('valid invalid').addClass(isValid ? 'valid' : 'invalid');
            } else if (fieldType === 'password') {
                const isValid = value.length >= 6;
                field.removeClass('valid invalid').addClass(isValid ? 'valid' : 'invalid');
            }
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Form submission with advanced animations
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            
            const email = $('#email').val().trim();
            const password = $('#password').val();
            
            // Basic validation
            if (!email || !password) {
                showMessage('Please fill in all fields', 'error');
                return;
            }

            if (password.length < 6) {
                showMessage('Password must be at least 6 characters', 'error');
                return;
            }

            // Show loading state with animation
            const submitBtn = $('.submit-btn');
            const btnText = $('.btn-text');
            const btnLoading = $('.btn-loading');
            
            submitBtn.prop('disabled', true).addClass('loading');
            btnText.hide();
            btnLoading.show();

            // Add form shake animation for loading
            $('.login-form-container').addClass('processing');

            // Simulate login API call
            setTimeout(() => {
                // Reset button state
                submitBtn.prop('disabled', false).removeClass('loading');
                btnText.show();
                btnLoading.hide();
                $('.login-form-container').removeClass('processing');

                // Simulate different login scenarios
                const loginScenarios = [
                    { success: true, message: 'Login successful! Redirecting...', probability: 0.7 },
                    { success: false, message: 'Invalid email or password', probability: 0.2 },
                    { success: false, message: 'Account temporarily locked. Please try again later.', probability: 0.1 }
                ];

                const random = Math.random();
                let scenario = loginScenarios[0]; // Default to success

                let cumulativeProbability = 0;
                for (const s of loginScenarios) {
                    cumulativeProbability += s.probability;
                    if (random <= cumulativeProbability) {
                        scenario = s;
                        break;
                    }
                }

                if (scenario.success) {
                    showMessage(scenario.message, 'success');
                    
                    // Success animation
                    $('.login-form-container').addClass('success');
                    
                    // Redirect after success animation
                    setTimeout(() => {
                        window.location.href = '../pages/profile.php';
                    }, 2000);
                } else {
                    showMessage(scenario.message, 'error');
                    
                    // Error animation
                    $('.form-control').addClass('error-shake');
                    setTimeout(() => {
                        $('.form-control').removeClass('error-shake');
                    }, 500);
                }
            }, 2000);
        });

        // Enhanced message display with animations
        function showMessage(text, type) {
            const messageDiv = $('#message');
            messageDiv.removeClass('success error show').addClass(type);
            messageDiv.text(text);
            
            // Add icon based on type
            if (type === 'success') {
                messageDiv.html('<span class="success-icon"></span>' + text);
            }
            
            messageDiv.addClass('show');
            
            // Auto-hide error messages after 5 seconds
            if (type === 'error') {
                setTimeout(() => {
                    messageDiv.removeClass('show');
                }, 5000);
            }
        }

        // Social login animations
        $('.social-btn').on('click', function(e) {
            e.preventDefault();
            
            const btn = $(this);
            const platform = btn.text().trim();
            
            // Add loading animation
            btn.addClass('loading');
            
            showMessage(`Redirecting to ${platform}...`, 'success');
            
            // Simulate OAuth redirect
            setTimeout(() => {
                btn.removeClass('loading');
                showMessage(`${platform} login cancelled or failed`, 'error');
            }, 3000);
        });

        // Forgot password modal simulation
        $('#forgotPassword').on('click', function(e) {
            e.preventDefault();
            
            // Simple prompt for demo (in real app, use a proper modal)
            const email = prompt('Enter your email address to reset password:');
            
            if (email && isValidEmail(email)) {
                showMessage('Password reset link sent to your email!', 'success');
            } else if (email) {
                showMessage('Please enter a valid email address', 'error');
            }
        });

        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            // Ctrl/Cmd + Enter to submit form
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 13) {
                $('#loginForm').submit();
            }
            
            // Escape to clear form
            if (e.keyCode === 27) {
                $('#loginForm')[0].reset();
                $('.form-control').removeClass('valid invalid');
                $('#message').removeClass('show');
            }
        });

        // Auto-fill demo credentials (for demo purposes)
        let demoMode = false;
        $(document).on('keydown', function(e) {
            // Press 'd' twice quickly to enable demo mode
            if (e.key === 'd') {
                if (!demoMode) {
                    demoMode = true;
                    setTimeout(() => { demoMode = false; }, 1000);
                } else {
                    fillDemoCredentials();
                }
            }
        });

        function fillDemoCredentials() {
            $('#email').val('demo@modernapp.com').addClass('valid');
            $('#password').val('demo123').addClass('valid');
            showMessage('Demo credentials filled! You can now sign in.', 'success');
        }

        // Advanced animations initialization
        function initializeAnimations() {
            // Stagger animation for form elements
            $('.form-group').each(function(index) {
                $(this).css({
                    'animation-delay': (index * 0.1) + 's'
                });
            });

            // Floating label effect
            $('.form-control').each(function() {
                const field = $(this);
                const label = field.siblings('label');
                
                if (field.val()) {
                    label.addClass('floating');
                }
                
                field.on('focus', function() {
                    label.addClass('floating');
                });
                
                field.on('blur', function() {
                    if (!field.val()) {
                        label.removeClass('floating');
                    }
                });
            });

            // Parallax effect for background shapes
            $(window).on('mousemove', function(e) {
                const mouseX = e.clientX / window.innerWidth;
                const mouseY = e.clientY / window.innerHeight;
                
                $('.shape').each(function(index) {
                    const speed = (index + 1) * 0.02;
                    const x = (mouseX - 0.5) * speed * 100;
                    const y = (mouseY - 0.5) * speed * 100;
                    
                    $(this).css({
                        'transform': `translate(${x}px, ${y}px)`
                    });
                });
            });

            // Typing effect for welcome title
            typeWriter();
        }

        // Typing animation for title
        function typeWriter() {
            const title = $('.welcome-title');
            const text = title.text();
            title.text('');
            
            let i = 0;
            const timer = setInterval(() => {
                title.text(text.slice(0, i));
                i++;
                
                if (i > text.length) {
                    clearInterval(timer);
                }
            }, 100);
        }

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

        // Performance optimization for animations
        let ticking = false;
        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateAnimations);
                ticking = true;
            }
        }

        function updateAnimations() {
            // Update any smooth animations here
            ticking = false;
        }

        $(window).on('scroll', requestTick);

        // Auto-focus first field with delay for better UX
        setTimeout(() => {
            $('#email').focus();
        }, 1000);

        // Add CSS classes for processing state
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                .login-form-container.processing {
                    animation: processingPulse 2s ease-in-out infinite;
                }
                
                @keyframes processingPulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.02); }
                }
                
                .login-form-container.success {
                    animation: successBounce 0.6s ease;
                }
                
                @keyframes successBounce {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                    100% { transform: scale(1); }
                }
                
                .form-control.error-shake {
                    animation: errorShake 0.5s ease-in-out;
                }
                
                @keyframes errorShake {
                    0%, 100% { transform: translateX(0); }
                    25% { transform: translateX(-10px); }
                    75% { transform: translateX(10px); }
                }
                
                .social-btn.loading {
                    pointer-events: none;
                    opacity: 0.7;
                    animation: loadingPulse 1s ease-in-out infinite;
                }
                
                @keyframes loadingPulse {
                    0%, 100% { opacity: 0.7; }
                    50% { opacity: 1; }
                }
            `)
            .appendTo('head');