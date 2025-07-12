<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Axiom</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/contact.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include '../parts/navbar.php'; ?>

    <main class="main-content">
        <!-- Page Header -->
        <section class="page-header">
            <div class="container">
                <h1 class="page-title">Contact Us</h1>
                <p class="page-subtitle">Get in touch with our team. We're here to help and answer any questions you might have.</p>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="contact-section">
            <div class="container">
                <div class="contact-container">
                    <!-- Contact Information -->
                    <div class="contact-info">
                        <div class="contact-card">
                            <div class="contact-item">
                                <div class="contact-icon">📍</div>
                                <div class="contact-details">
                                    <h3>Address</h3>
                                    <p>123 Modern Street<br>Tech City, TC 12345<br>Colombo,Sri Lanka</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">📞</div>
                                <div class="contact-details">
                                    <h3>Phone</h3>
                                    <p><a href="tel:+1234567890">+94 71 263 6236</a></p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">✉️</div>
                                <div class="contact-details">
                                    <h3>Email</h3>
                                    <p><a href="mailto:contact@axiom.local">contact@axiom.local</a></p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">🕒</div>
                                <div class="contact-details">
                                    <h3>Business Hours</h3>
                                    <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
                                </div>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="social-section">
                            <h3>Follow Us</h3>
                            <div class="social-links">
                                <a href="#" class="social-link facebook" title="Facebook">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                <a href="#" class="social-link twitter" title="Twitter">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                </a>
                                <a href="#" class="social-link instagram" title="Instagram">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </a>
                                <a href="#" class="social-link github" title="GitHub">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="contact-form-container">
                        <h2 class="form-title">Send us a Message</h2>
                        <p class="form-subtitle">We'll get back to you within 24 hours</p>
                        
                        <div id="message" class="message"></div>
                        
                        <form id="contactForm" class="contact-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First Name *</label>
                                    <input type="text" id="firstName" name="firstName" class="form-control" placeholder="John" required>
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name *</label>
                                    <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Doe" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="+1 (234) 567-890">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Subject *</label>
                                <input type="text" id="subject" name="subject" class="form-control" placeholder="How can we help you?" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="messageText">Message *</label>
                                <textarea id="messageText" name="messageText" class="form-control" placeholder="Tell us more about your inquiry..." required></textarea>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                <span class="btn-text">Send Message</span>
                                <span class="btn-loading" style="display: none;">
                                    <span class="loading"></span> Sending...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Map Section -->
                <div class="map-section">
                    <h3 class="map-title">Find Us</h3>
                    <div class="map-container">
                        <p>Interactive Map Coming Soon</p>
                        <!-- You can integrate Google Maps or any other map service here -->
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include '../parts/footer.php'; ?>

    <script>
        $(document).ready(function() {
            // Contact form submission
            $('#contactForm').on('submit', function(e) {
                e.preventDefault();
                
                // Basic validation
                if (!validateForm()) {
                    showMessage('Please fill in all required fields correctly.', 'error');
                    return;
                }
                
                const submitBtn = $('.submit-btn');
                const btnText = $('.btn-text');
                const btnLoading = $('.btn-loading');
                
                // Show loading state
                submitBtn.prop('disabled', true);
                btnText.hide();
                btnLoading.show();
                
                // Submit form via AJAX
                $.ajax({
                    url: '../php/contact_handler.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, 'success');
                            
                            // Reset form
                            $('#contactForm')[0].reset();
                            
                            // Reset button state
                            submitBtn.prop('disabled', false);
                            btnText.show();
                            btnLoading.hide();
                        } else {
                            showMessage(response.message, 'error');
                            
                            // Show field-specific errors
                            if (response.errors) {
                                Object.keys(response.errors).forEach(function(field) {
                                    const fieldElement = $('[name="' + field + '"]');
                                    fieldElement.addClass('error');
                                    
                                    // Add error message near field
                                    fieldElement.after('<div class="field-error">' + response.errors[field] + '</div>');
                                });
                            }
                            
                            // Reset button state
                            submitBtn.prop('disabled', false);
                            btnText.show();
                            btnLoading.hide();
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to send message. Please try again.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        showMessage(errorMessage, 'error');
                        
                        // Reset button state
                        submitBtn.prop('disabled', false);
                        btnText.show();
                        btnLoading.hide();
                    }
                });
            });

            // Form validation
            function validateForm() {
                let isValid = true;
                
                // Clear previous errors
                $('.field-error').remove();
                $('.form-control').removeClass('error');
                
                // Check required fields
                const requiredFields = ['firstName', 'lastName', 'email', 'subject', 'messageText'];
                requiredFields.forEach(function(fieldName) {
                    const field = $('[name="' + fieldName + '"]');
                    const value = field.val().trim();
                    
                    if (!value) {
                        field.addClass('error');
                        isValid = false;
                    }
                });
                
                // Validate email
                const email = $('#email').val().trim();
                if (email && !isValidEmail(email)) {
                    $('#email').addClass('error');
                    isValid = false;
                }
                
                // Validate message length
                const message = $('#messageText').val().trim();
                if (message && message.length < 10) {
                    $('#messageText').addClass('error');
                    isValid = false;
                }
                
                return isValid;
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function showMessage(text, type) {
                const messageDiv = $('#message');
                messageDiv.removeClass('success error').addClass(type + ' show');
                messageDiv.text(text);
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    messageDiv.removeClass('show');
                }, 5000);
            }

            // Real-time validation
            $('.form-control').on('blur', function() {
                const field = $(this);
                const value = field.val().trim();
                
                field.removeClass('error');
                
                if (field.prop('required') && !value) {
                    field.addClass('error');
                } else if (field.attr('type') === 'email' && value && !isValidEmail(value)) {
                    field.addClass('error');
                }
            });

            // Remove error styling on input
            $('.form-control').on('input', function() {
                $(this).removeClass('error');
                $(this).next('.field-error').remove();
            });
        });
    </script>

    <style>
        .form-control.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        
        .field-error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</body>
</html>