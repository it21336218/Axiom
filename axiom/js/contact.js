// File: js/contact.js
// Description: Contact page JavaScript functionality including form submission, validation, and animations.
        $(document).ready(function() {
            // Import navbar from navbar.html
            $("#navbar-container").load("./navbar.html", function() {
                // Set active navigation item
                $('.nav-links a[href="contact.html"]').addClass('active');
            });
            
            // Import footer from footer.html
            $("#footer-container").load("./footer.html");
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

        // Contact form submission
        $('#contactForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $('.submit-btn');
            const btnText = $('.btn-text');
            const btnLoading = $('.btn-loading');
            const messageDiv = $('#message');
            
            // Show loading state
            submitBtn.prop('disabled', true);
            btnText.hide();
            btnLoading.show();
            
            // Simulate form submission (replace with actual API call)
            setTimeout(() => {
                // Reset button state
                submitBtn.prop('disabled', false);
                btnText.show();
                btnLoading.hide();
                
                // Show success message
                messageDiv.removeClass('error').addClass('success show');
                messageDiv.text('Thank you for your message! We\'ll get back to you soon.');
                
                // Reset form
                this.reset();
                
                // Hide message after 5 seconds
                setTimeout(() => {
                    messageDiv.removeClass('show');
                }, 5000);
                
            }, 2000);
        });

        // Form validation
        $('.form-control').on('blur', function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (field.prop('required') && !value) {
                field.css('border-color', 'var(--error)');
            } else if (field.attr('type') === 'email' && value && !isValidEmail(value)) {
                field.css('border-color', 'var(--error)');
            } else {
                field.css('border-color', 'var(--glass-border)');
            }
        });

        // Email validation
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
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

        // Animate elements on scroll
        function animateOnScroll() {
            $('.contact-card, .contact-form-container, .map-section').each(function() {
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
        $('.contact-card, .contact-form-container, .map-section').css({
            'opacity': '0',
            'transform': 'translateY(30px)',
            'transition': 'all 0.6s ease'
        });

        $(window).on('scroll', animateOnScroll);
        $(document).ready(animateOnScroll);
    