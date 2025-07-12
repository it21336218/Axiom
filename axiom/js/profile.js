// Tab functionality
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

// Profile picture preview and auto-submit
document.addEventListener('DOMContentLoaded', function() {
    const profilePictureInput = document.getElementById('profilePictureInput');
    const profilePicture = document.getElementById('profilePicture');
    
    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePicture.src = e.target.result;
                };
                reader.readAsDataURL(file);
                
                // Show success message
                showAlert('Profile picture updated successfully!', 'success');
            }
        });
    }
});

// Password strength indicator
document.addEventListener('DOMContentLoaded', function() {
    const newPasswordField = document.getElementById('new_password');
    if (newPasswordField) {
        newPasswordField.addEventListener('input', function() {
            const password = this.value;
            let strengthIndicator = document.getElementById('passwordStrength');
            
            if (!strengthIndicator) {
                strengthIndicator = document.createElement('div');
                strengthIndicator.id = 'passwordStrength';
                strengthIndicator.className = 'password-strength';
                this.parentNode.appendChild(strengthIndicator);
            }
            
            if (password.length === 0) {
                strengthIndicator.textContent = '';
                return;
            }
            
            const strength = calculatePasswordStrength(password);
            
            if (strength < 30) {
                strengthIndicator.textContent = 'Weak password';
                strengthIndicator.style.color = '#dc3545';
            } else if (strength < 60) {
                strengthIndicator.textContent = 'Medium password';
                strengthIndicator.style.color = '#ffc107';
            } else {
                strengthIndicator.textContent = 'Strong password';
                strengthIndicator.style.color = '#28a745';
            }
        });
    }
});

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

// Confirm password match
document.addEventListener('DOMContentLoaded', function() {
    const confirmPasswordField = document.getElementById('confirm_password');
    if (confirmPasswordField) {
        confirmPasswordField.addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            let matchIndicator = document.getElementById('passwordMatch');
            
            if (!matchIndicator) {
                matchIndicator = document.createElement('div');
                matchIndicator.id = 'passwordMatch';
                matchIndicator.className = 'password-match';
                this.parentNode.appendChild(matchIndicator);
            }
            
            if (confirmPassword.length === 0) {
                matchIndicator.textContent = '';
                return;
            }
            
            if (newPassword === confirmPassword) {
                matchIndicator.textContent = 'Passwords match';
                matchIndicator.style.color = '#28a745';
            } else {
                matchIndicator.textContent = 'Passwords do not match';
                matchIndicator.style.color = '#dc3545';
            }
        });
    }
});

// Delete account confirmation
function confirmDeleteAccount() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
        if (confirm('This will permanently delete all your data. Are you absolutely sure?')) {
            showAlert('Account deletion request submitted. You will receive a confirmation email.', 'success');
        }
    }
}

// Alert system
function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alert-container');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    alertContainer.innerHTML = '';
    alertContainer.appendChild(alert);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
}

// Back to Top Button
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('backToTop');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });
    
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    field.style.borderColor = '#e1e5e9';
                }
            });
            
            // Email validation
            const emailField = form.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    emailField.style.borderColor = '#dc3545';
                    isValid = false;
                }
            }
            
            // Password confirmation validation
            const newPasswordField = form.querySelector('#new_password');
            const confirmPasswordField = form.querySelector('#confirm_password');
            
            if (newPasswordField && confirmPasswordField) {
                if (newPasswordField.value !== confirmPasswordField.value) {
                    confirmPasswordField.style.borderColor = '#dc3545';
                    isValid = false;
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                showAlert('Please fill in all required fields correctly.', 'error');
            } else {
                e.preventDefault(); // Prevent actual form submission for demo
                
                // Simulate form submission
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalHTML = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle></svg> Processing...';
                
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                    
                    if (form.id === 'profileForm') {
                        showAlert('Profile updated successfully!', 'success');
                        updateUserInfo();
                    } else if (form.id === 'passwordForm') {
                        showAlert('Password changed successfully!', 'success');
                        form.reset();
                    }
                }, 2000);
            }
        });
    });
});

// Update user info in sidebar
function updateUserInfo() {
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    const email = document.getElementById('email').value;
    
    const userNameElement = document.querySelector('.user-info h2');
    const userEmailElement = document.querySelector('.user-meta');
    
    if (userNameElement) {
        userNameElement.textContent = `${firstName} ${lastName}`;
    }
    
    if (userEmailElement) {
        userEmailElement.innerHTML = `${email}<br>Member since Jan 2024`;
    }
}

// Real-time field validation
document.addEventListener('input', function(e) {
    if (e.target.hasAttribute('required')) {
        if (e.target.value.trim()) {
            e.target.style.borderColor = '#28a745';
        } else {
            e.target.style.borderColor = '#dc3545';
        }
    }
    
    // Email validation
    if (e.target.type === 'email' && e.target.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailRegex.test(e.target.value)) {
            e.target.style.borderColor = '#28a745';
        } else {
            e.target.style.borderColor = '#dc3545';
        }
    }
});

// Keyboard navigation for tabs
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
        const activeTab = document.querySelector('.tab-btn.active');
        const allTabs = document.querySelectorAll('.tab-btn');
        const currentIndex = Array.from(allTabs).indexOf(activeTab);
        
        if (e.key === 'ArrowLeft' && currentIndex > 0) {
            e.preventDefault();
            allTabs[currentIndex - 1].click();
            allTabs[currentIndex - 1].focus();
        } else if (e.key === 'ArrowRight' && currentIndex < allTabs.length - 1) {
            e.preventDefault();
            allTabs[currentIndex + 1].click();
            allTabs[currentIndex + 1].focus();
        }
    }
});

// Profile picture drag and drop
document.addEventListener('DOMContentLoaded', function() {
    const profilePictureContainer = document.querySelector('.profile-picture-container');
    const profilePictureInput = document.getElementById('profilePictureInput');

    if (profilePictureContainer && profilePictureInput) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            profilePictureContainer.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            profilePictureContainer.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            profilePictureContainer.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            profilePictureContainer.style.opacity = '0.7';
            profilePictureContainer.style.transform = 'scale(1.05)';
        }

        function unhighlight(e) {
            profilePictureContainer.style.opacity = '1';
            profilePictureContainer.style.transform = 'scale(1)';
        }

        profilePictureContainer.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                profilePictureInput.files = files;
                profilePictureInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    }
});

// Auto-save draft functionality
let autoSaveTimeout;
const draftKey = 'profile_draft_user';

function saveDraft() {
    const formData = {
        first_name: document.getElementById('first_name')?.value || '',
        last_name: document.getElementById('last_name')?.value || '',
        email: document.getElementById('email')?.value || '',
        phone: document.getElementById('phone')?.value || '',
        location: document.getElementById('location')?.value || '',
        website: document.getElementById('website')?.value || '',
        bio: document.getElementById('bio')?.value || '',
        timestamp: Date.now()
    };
    
    localStorage.setItem(draftKey, JSON.stringify(formData));
}

function loadDraft() {
    const saved = localStorage.getItem(draftKey);
    if (saved) {
        const formData = JSON.parse(saved);
        // Only load if draft is less than 1 hour old
        if (Date.now() - formData.timestamp < 3600000) {
            Object.keys(formData).forEach(key => {
                if (key !== 'timestamp') {
                    const field = document.getElementById(key);
                    if (field && !field.value) {
                        field.value = formData[key];
                    }
                }
            });
        }
    }
}

// Load draft on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDraft();
    
    // Auto-save on input
    const profileTab = document.getElementById('profile');
    if (profileTab) {
        profileTab.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(saveDraft, 2000); // Save after 2 seconds of inactivity
        });
    }
});

// Clear draft on successful submission
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function() {
            localStorage.removeItem(draftKey);
        });
    }
});

// Theme toggle functionality
function toggleTheme() {
    document.body.classList.toggle('dark-theme');
    localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
}

// Load saved theme
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
});

// Print profile functionality
function printProfile() {
    window.print();
}

// Export profile data
function exportProfile() {
    const profileData = {
        name: document.querySelector('.user-info h2')?.textContent || 'John Doe',
        email: document.getElementById('email')?.value || 'john.doe@example.com',
        phone: document.getElementById('phone')?.value || '',
        location: document.getElementById('location')?.value || '',
        website: document.getElementById('website')?.value || '',
        bio: document.getElementById('bio')?.value || '',
        memberSince: 'Jan 2024'
    };

    const dataStr = JSON.stringify(profileData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'profile_data.json';
    link.click();
    URL.revokeObjectURL(url);
}

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S to save profile
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        const activeForm = document.querySelector('.tab-pane.active form');
        if (activeForm) {
            activeForm.dispatchEvent(new Event('submit'));
        }
    }
    
    // Escape to close any open modals or return to profile tab
    if (e.key === 'Escape') {
        const profileTab = document.querySelector('.tab-btn');
        if (profileTab) {
            profileTab.click();
        }
    }
});

// Animate stats on page load
document.addEventListener('DOMContentLoaded', function() {
    const stats = document.querySelectorAll('.stat-number');
    stats.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        let currentValue = 0;
        const increment = finalValue / 50;
        
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                stat.textContent = finalValue;
                clearInterval(timer);
            } else {
                stat.textContent = Math.floor(currentValue);
            }
        }, 30);
    });
});

// Add tooltip functionality
function addTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = e.target.getAttribute('data-tooltip');
    document.body.appendChild(tooltip);
    
    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + rect.width / 2 + 'px';
    tooltip.style.top = rect.top - 40 + 'px';
}

function hideTooltip() {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', addTooltips);

// Smooth scrolling for navigation
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Add mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelector('.nav-links');
    
    // Create mobile menu toggle button
    const mobileToggle = document.createElement('button');
    mobileToggle.className = 'mobile-toggle';
    mobileToggle.innerHTML = '☰';
    mobileToggle.style.display = 'none';
    mobileToggle.style.background = 'none';
    mobileToggle.style.border = 'none';
    mobileToggle.style.color = 'white';
    mobileToggle.style.fontSize = '1.5rem';
    mobileToggle.style.cursor = 'pointer';
    
    // Add mobile styles
    const style = document.createElement('style');
    style.textContent = `
        @media (max-width: 768px) {
            .mobile-toggle {
                display: block !important;
            }
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(26, 26, 46, 0.95);
                flex-direction: column;
                padding: 1rem;
            }
            .nav-links.active {
                display: flex !important;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Insert toggle button
    const navbar = document.querySelector('.navbar .container');
    navbar.appendChild(mobileToggle);
    
    // Toggle functionality
    mobileToggle.addEventListener('click', function() {
        navLinks.classList.toggle('active');
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!navbar.contains(e.target)) {
            navLinks.classList.remove('active');
        }
    });
});

// Add loading states
function addLoadingState(button) {
    const originalHTML = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle></svg> Loading...';
    
    return function() {
        button.disabled = false;
        button.innerHTML = originalHTML;
    };
}

// Performance optimization - Lazy load images
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
});

// Add search functionality for activities
function addActivitySearch() {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search activities...';
    searchInput.className = 'form-control';
    searchInput.style.marginBottom = '1rem';
    
    const activityTab = document.getElementById('activity');
    const activityTitle = activityTab.querySelector('h3');
    
    if (activityTitle) {
        activityTitle.insertAdjacentElement('afterend', searchInput);
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const activities = activityTab.querySelectorAll('.activity-item');
            
            activities.forEach(activity => {
                const title = activity.querySelector('.activity-title').textContent.toLowerCase();
                if (title.includes(searchTerm)) {
                    activity.style.display = 'flex';
                } else {
                    activity.style.display = 'none';
                }
            });
        });
    }
}

// Initialize activity search
document.addEventListener('DOMContentLoaded', addActivitySearch);

// Add notification system
class NotificationSystem {
    constructor() {
        this.container = this.createContainer();
    }
    
    createContainer() {
        const container = document.createElement('div');
        container.className = 'notification-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            pointer-events: none;
        `;
        document.body.appendChild(container);
        return container;
    }
    
    show(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            background: white;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-left: 4px solid ${this.getColor(type)};
            pointer-events: auto;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
        `;
        notification.textContent = message;
        
        this.container.appendChild(notification);
        
        // Trigger animation
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }
    
    getColor(type) {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        return colors[type] || colors.info;
    }
}

// Initialize notification system
const notifications = new NotificationSystem();

// Console log for debugging
console.log('ModernApp Profile Page - JavaScript loaded successfully');

// Service Worker registration for PWA capabilities
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('ServiceWorker registration successful');
            })
            .catch(function(err) {
                console.log('ServiceWorker registration failed');
            });
    });
}