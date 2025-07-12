<?php
// Check if user is logged in
session_start();
define('AXIOM_ACCESS', true);
require_once '../php/auth.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get current user data
$user = $auth->getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Axiom</title>
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/global.css">
</head>
<body>
    <?php include '../parts/navbar.php'; ?>
    
    <div class="main-container">
        <!-- Profile Sidebar -->
        <div class="profile-sidebar">
            <div class="profile-picture-container">
                <?php 
                $profilePicture = $user['profile_picture'] ? 
                    '../uploads/profiles/' . htmlspecialchars($user['profile_picture']) : 
                    'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=120&q=80';
                ?>
                <img src="<?php echo $profilePicture; ?>" 
                     alt="Profile Picture" class="profile-picture" id="profilePicture">
                <form id="profilePictureForm" style="display: inline-block;">
                    <input type="file" name="profile_picture" id="profilePictureInput" accept="image/*" style="display: none;">
                    <button type="button" class="upload-btn" onclick="document.getElementById('profilePictureInput').click();">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7,10 12,15 17,10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Change Photo
                    </button>
                </form>
            </div>
            
            <div class="user-info">
                <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                <div class="user-meta">
                    <?php echo htmlspecialchars($user['email']); ?><br>
                    Member since <?php echo date('M Y', strtotime($user['created_at'])); ?>
                </div>
            </div>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-number">24</div>
                    <div class="stat-label">Projects</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">156</div>
                    <div class="stat-label">Points</div>
                </div>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <div class="tab-navigation">
                <button class="tab-btn active" onclick="showTab('profile')">Profile Settings</button>
                <button class="tab-btn" onclick="showTab('security')">Security</button>
                <button class="tab-btn" onclick="showTab('activity')">Activity</button>
            </div>

            <!-- Profile Settings Tab -->
            <div class="tab-content">
                <div id="profile" class="tab-pane active">
                    <div id="alert-container"></div>

                    <form id="profileForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" name="bio" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17,21 17,13 7,13 7,21"></polyline>
                                <polyline points="7,3 7,8 15,8"></polyline>
                            </svg>
                            Save Changes
                        </button>
                    </form>
                </div>

                <!-- Security Tab -->
                <div id="security" class="tab-pane">
                    <div id="security-alert-container"></div>
                    
                    <h3 style="margin-bottom: 1.5rem;">Change Password</h3>
                    
                    <form id="passwordForm">
                        <div class="form-group">
                            <label for="current_password">Current Password *</label>
                            <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">New Password *</label>
                                <input type="password" id="new_password" name="new_password" required autocomplete="new-password">
                                <div id="passwordStrength" class="password-strength"></div>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password *</label>
                                <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
                                <div id="passwordMatch" class="password-match"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn" id="changePasswordBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <circle cx="12" cy="16" r="1"></circle>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            Change Password
                        </button>
                    </form>

                    <div class="danger-zone">
                        <h4>Danger Zone</h4>
                        <p>Once you delete your account, there is no going back. Please be certain.</p>
                        <button class="btn btn-danger" id="deleteAccountBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3,6 5,6 21,6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2 2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            Delete Account
                        </button>
                    </div>
                </div>

                <!-- Activity Tab -->
                <div id="activity" class="tab-pane">
                    <h3 style="margin-bottom: 1.5rem;">Recent Activity</h3>
                    
                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Profile updated</div>
                            <div class="activity-time">Just now</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                <polyline points="10,17 15,12 10,7"></polyline>
                                <line x1="15" y1="12" x2="3" y2="12"></line>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Logged in</div>
                            <div class="activity-time"><?php echo isset($user['last_login']) ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Today'; ?></div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Account created</div>
                            <div class="activity-time"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../parts/footer.php'; ?>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Global functions for tab navigation
        function showTab(tabName) {
            // Hide all tab panes
            const tabPanes = document.querySelectorAll('.tab-pane');
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Remove active class from all tab buttons
            const tabBtns = document.querySelectorAll('.tab-btn');
            tabBtns.forEach(btn => btn.classList.remove('active'));
            
            // Show selected tab pane
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }

        // Utility functions
        function showAlert(message, type = 'success', container = '#alert-container') {
            const alertContainer = $(container);
            const alert = $('<div class="alert alert-' + type + '">' + message + '</div>');
            
            alertContainer.html(alert);
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                alert.fadeOut(500, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        function setLoadingState(button, loading = true) {
            const $btn = $(button);
            if (loading) {
                $btn.data('original-html', $btn.html());
                $btn.prop('disabled', true);
                $btn.html('<span class="loading"></span> Processing...');
            } else {
                $btn.prop('disabled', false);
                $btn.html($btn.data('original-html'));
            }
        }

        // Password strength calculator
        function calculatePasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength += 20;
            if (password.length >= 12) strength += 10;
            if (/[a-z]/.test(password)) strength += 10;
            if (/[A-Z]/.test(password)) strength += 10;
            if (/[0-9]/.test(password)) strength += 15;
            if (/[^A-Za-z0-9]/.test(password)) strength += 15;
            if (password.length >= 16) strength += 10;
            if (/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/.test(password)) strength += 10;
            
            return Math.min(strength, 100);
        }

        $(document).ready(function() {
            // Profile form submission
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = $(this).find('button[type="submit"]');
                setLoadingState(submitBtn, true);
                
                $.ajax({
                    url: '../php/profile_handler.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=update_profile',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            
                            // Update sidebar info
                            const firstName = $('#first_name').val();
                            const lastName = $('#last_name').val();
                            const email = $('#email').val();
                            
                            $('.user-info h2').text(firstName + ' ' + lastName);
                            $('.user-meta').html(email + '<br>Member since <?php echo date('M Y', strtotime($user['created_at'])); ?>');
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Profile update failed. Please try again.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                        showAlert(errorMessage, 'error');
                    },
                    complete: function() {
                        setLoadingState(submitBtn, false);
                    }
                });
            });

            // Password form submission
            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validate passwords match
                const newPassword = $('#new_password').val();
                const confirmPassword = $('#confirm_password').val();
                
                if (newPassword !== confirmPassword) {
                    showAlert('Passwords do not match', 'error', '#security-alert-container');
                    return;
                }
                
                // Check password strength
                const strength = calculatePasswordStrength(newPassword);
                if (strength < 60) {
                    showAlert('Please choose a stronger password', 'error', '#security-alert-container');
                    return;
                }
                
                const submitBtn = $('#changePasswordBtn');
                setLoadingState(submitBtn, true);
                
                $.ajax({
                    url: '../php/profile_handler.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=change_password',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success', '#security-alert-container');
                            $('#passwordForm')[0].reset();
                            $('#passwordStrength').text('');
                            $('#passwordMatch').text('');
                        } else {
                            showAlert(response.message, 'error', '#security-alert-container');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Password change failed. Please try again.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                        showAlert(errorMessage, 'error', '#security-alert-container');
                    },
                    complete: function() {
                        setLoadingState(submitBtn, false);
                    }
                });
            });

            // Password strength indicator
            $('#new_password').on('input', function() {
                const password = $(this).val();
                const $strengthIndicator = $('#passwordStrength');
                
                if (password.length === 0) {
                    $strengthIndicator.text('');
                    return;
                }
                
                const strength = calculatePasswordStrength(password);
                
                if (strength < 30) {
                    $strengthIndicator.text('Weak password').css('color', '#dc3545');
                } else if (strength < 60) {
                    $strengthIndicator.text('Medium password').css('color', '#ffc107');
                } else {
                    $strengthIndicator.text('Strong password').css('color', '#28a745');
                }
            });

            // Password match indicator
            $('#confirm_password').on('input', function() {
                const newPassword = $('#new_password').val();
                const confirmPassword = $(this).val();
                const $matchIndicator = $('#passwordMatch');
                
                if (confirmPassword.length === 0) {
                    $matchIndicator.text('');
                    return;
                }
                
                if (newPassword === confirmPassword) {
                    $matchIndicator.text('Passwords match').css('color', '#28a745');
                } else {
                    $matchIndicator.text('Passwords do not match').css('color', '#dc3545');
                }
            });

            // Delete account functionality
            $('#deleteAccountBtn').on('click', function() {
                if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                    if (confirm('This will permanently delete all your data. Are you absolutely sure?')) {
                        const $btn = $(this);
                        setLoadingState($btn, true);
                        
                        $.ajax({
                            url: '../php/profile_handler.php',
                            type: 'POST',
                            data: { action: 'delete_account' },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    showAlert('Account deletion initiated. You will be logged out shortly.', 'success', '#security-alert-container');
                                    setTimeout(function() {
                                        window.location.href = '../auth/login.php';
                                    }, 3000);
                                } else {
                                    showAlert(response.message, 'error', '#security-alert-container');
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Account deletion failed. Please try again.';
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.message) {
                                        errorMessage = response.message;
                                    }
                                } catch (e) {
                                    console.error('Error parsing response:', e);
                                }
                                showAlert(errorMessage, 'error', '#security-alert-container');
                            },
                            complete: function() {
                                setLoadingState($btn, false);
                            }
                        });
                    }
                }
            });

            // Profile picture upload
            $('#profilePictureInput').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        showAlert('Please select a valid image file (JPG, PNG, GIF, WebP)', 'error');
                        return;
                    }
                    
                    // Validate file size (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        showAlert('File size must be less than 5MB', 'error');
                        return;
                    }
                    
                    const formData = new FormData();
                    formData.append('profile_picture', file);
                    formData.append('upload_type', 'profile_picture');
                    
                    $.ajax({
                        url: '../php/upload_handler.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Update profile picture preview
                                $('#profilePicture').attr('src', response.data.url);
                                showAlert(response.message, 'success');
                            } else {
                                showAlert(response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'File upload failed. Please try again.';
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                console.error('Error parsing response:', e);
                            }
                            showAlert(errorMessage, 'error');
                        }
                    });
                }
            });

            // Real-time field validation
            $('input[required]').on('input', function() {
                const $field = $(this);
                if ($field.val().trim()) {
                    $field.css('border-color', '#28a745');
                } else {
                    $field.css('border-color', '#dc3545');
                }
            });

            // Email validation
            $('input[type="email"]').on('input', function() {
                const $field = $(this);
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if ($field.val() && emailRegex.test($field.val())) {
                    $field.css('border-color', '#28a745');
                } else if ($field.val()) {
                    $field.css('border-color', '#dc3545');
                }
            });
        });
    </script>

    <style>
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 5px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 500;
            border: 1px solid;
        }
        
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: #155724;
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-color: rgba(220, 53, 69, 0.3);
        }

        .password-strength, .password-match {
            font-size: 12px;
            margin-top: 5px;
            font-weight: 500;
        }

        #security-alert-container {
            margin-bottom: 1rem;
        }
    </style>
</body>
</html>