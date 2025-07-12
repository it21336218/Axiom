$(document).ready(function() {
    // Hide loading overlay after page load
    setTimeout(() => {
        $('#loadingOverlay').addClass('hidden');
    }, 1000);

    // Import navbar from navbar.html
    $("#navbar-container").load("navbar.html", function(response, status, xhr) {
        if (status == "error") {
            console.log("Error loading navbar.html: " + xhr.status + " " + xhr.statusText);
        } else {
            // Set active navigation item
            $('.nav-links a[href="index.html"]').addClass('active');
            initializeNavbar();
        }
    });
    
    // Import footer from footer.html
    $("#footer-container").load("footer.html", function(response, status, xhr) {
        if (status == "error") {
            console.log("Error loading footer.html: " + xhr.status + " " + xhr.statusText);
        } else {
            initializeFooter();
        }
    });

    // Initialize animations and interactions
    initializeScrollAnimations();
    initializeParallax();
    initializeCounters();
    initializeNavbarEffects();
});

// Mobile menu toggle (for when navbar is loaded)
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

// Initialize navbar functionality
function initializeNavbar() {
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
}

// Initialize footer functionality
function initializeFooter() {
    // Newsletter form submission
    $('.newsletter-form').on('submit', function(e) {
        e.preventDefault();
        const email = $(this).find('.newsletter-input').val();
        
        if (email) {
            const btn = $(this).find('.newsletter-btn');
            const originalText = btn.text();
            
            btn.text('Subscribing...').prop('disabled', true);
            
            setTimeout(() => {
                btn.text('Subscribed!').css('background', 'linear-gradient(45deg, #22c55e, #16a34a)');
                
                setTimeout(() => {
                    btn.text(originalText).prop('disabled', false);
                    btn.css('background', 'linear-gradient(45deg, var(--primary), var(--accent))');
                    $(this).find('.newsletter-input').val('');
                }, 2000);
            }, 1000);
        }
    });

    // Social links animation
    $('.social-link').on('mouseenter', function() {
        $(this).css('transform', 'translateY(-3px) scale(1.1)');
    }).on('mouseleave', function() {
        $(this).css('transform', 'translateY(0) scale(1)');
    });
}

// Scroll animations
function initializeScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                
                // Trigger counter animation if element has counter
                const counter = entry.target.querySelector('.counter');
                if (counter && !counter.classList.contains('animated')) {
                    animateCounter(counter);
                }
            }
        });
    }, observerOptions);

    // Observe all scroll reveal elements
    $('.scroll-reveal').each(function() {
        observer.observe(this);
    });
}

// Counter animation
function initializeCounters() {
    // This will be called by scroll animation
}

function animateCounter(element) {
    element.classList.add('animated');
    const target = parseFloat(element.getAttribute('data-target'));
    const isDecimal = element.getAttribute('data-target').includes('.');
    const duration = 2000;
    const increment = target / (duration / 16);
    let current = 0;

    const timer = setInterval(() => {
        current += increment;
        
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }

        if (isDecimal) {
            element.textContent = current.toFixed(1) + '%';
        } else if (target >= 1000) {
            element.textContent = Math.floor(current).toLocaleString() + '+';
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Parallax effect
function initializeParallax() {
    $(window).on('mousemove', function(e) {
        const mouseX = e.clientX / window.innerWidth;
        const mouseY = e.clientY / window.innerHeight;
        
        $('.parallax-element').each(function(index) {
            const speed = (index + 1) * 0.5;
            const x = (mouseX - 0.5) * speed;
            const y = (mouseY - 0.5) * speed;
            
            $(this).css({
                'transform': `translate(${x}px, ${y}px)`
            });
        });

        // Floating shapes parallax
        $('.shape').each(function(index) {
            const speed = (index + 1) * 0.02;
            const x = (mouseX - 0.5) * speed * 100;
            const y = (mouseY - 0.5) * speed * 100;
            
            $(this).css({
                'transform': `translate(${x}px, ${y}px)`
            });
        });
    });
}

// Navbar effects
function initializeNavbarEffects() {
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
}

// Advanced button interactions
$('.btn').on('mouseenter', function() {
    $(this).find('::before').css('left', '0');
}).on('mouseleave', function() {
    $(this).find('::before').css('left', '-100%');
});

// Feature cards advanced hover
$('.feature-card').on('mouseenter', function() {
    $(this).siblings().css('opacity', '0.7');
}).on('mouseleave', function() {
    $(this).siblings().css('opacity', '1');
});

// Typing animation for hero title
function typeWriter() {
    const title = $('.hero-title');
    if (title.length > 0) {
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
}

// Initialize typing animation after delay
setTimeout(typeWriter, 1500);

// Performance optimization
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

// Easter egg - press 'm' three times quickly
let mKeyCount = 0;
$(document).on('keydown', function(e) {
    if (e.key === 'm' || e.key === 'M') {
        mKeyCount++;
        
        if (mKeyCount === 3) {
            // Activate special effects
            $('body').addClass('party-mode');
            
            // Add rainbow animation to all text
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    .party-mode * {
                        animation: rainbow 2s linear infinite !important;
                    }
                    
                    @keyframes rainbow {
                        0% { color: #ff0000; }
                        16.66% { color: #ff8000; }
                        33.33% { color: #ffff00; }
                        50% { color: #00ff00; }
                        66.66% { color: #0080ff; }
                        83.33% { color: #8000ff; }
                        100% { color: #ff0000; }
                    }
                `)
                .appendTo('head');
            
            setTimeout(() => {
                $('body').removeClass('party-mode');
                $('style').last().remove();
            }, 5000);
            
            mKeyCount = 0;
        }
        
        setTimeout(() => {
            mKeyCount = 0;
        }, 1000);
    }
});

// Add some random floating elements for extra visual appeal
function createFloatingElement() {
    const element = $('<div class="floating-element"></div>');
    const size = Math.random() * 10 + 5;
    const left = Math.random() * 100;
    const duration = Math.random() * 10 + 10;
    
    element.css({
        position: 'fixed',
        width: size + 'px',
        height: size + 'px',
        background: 'linear-gradient(45deg, var(--primary), var(--accent))',
        borderRadius: '50%',
        left: left + '%',
        bottom: '-20px',
        opacity: '0.3',
        animation: `floatUp ${duration}s linear infinite`,
        zIndex: '-1',
        pointerEvents: 'none'
    });
    
    $('body').append(element);
    
    setTimeout(() => {
        element.remove();
    }, duration * 1000);
}

// Create floating elements periodically
setInterval(createFloatingElement, 3000);

// Add CSS for floating elements
$('<style>')
    .prop('type', 'text/css')
    .html(`
        @keyframes floatUp {
            from {
                transform: translateY(0) rotate(0deg);
                opacity: 0.3;
            }
            to {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }
    `)
    .appendTo('head');
    
// Advanced JavaScript for Home Page Interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Page loading performance optimization
            const loadStartTime = performance.now();
            
            window.addEventListener('load', function() {
                const loadEndTime = performance.now();
                console.log(`Page loaded in ${(loadEndTime - loadStartTime).toFixed(2)} milliseconds`);
            });
            
            // Intersection Observer for scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const scrollObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                    }
                });
            }, observerOptions);
            
            // Observe elements with scroll-reveal class
            document.querySelectorAll('.scroll-reveal').forEach(element => {
                scrollObserver.observe(element);
            });
            
            // Counter animation for statistics
            function animateCounters() {
                const counters = document.querySelectorAll('.counter');
                const counterStates = new Map();
                
                counters.forEach(counter => {
                    const target = parseFloat(counter.getAttribute('data-target'));
                    const current = parseFloat(counter.textContent) || 0;
                    const increment = target / 100;
                    
                    if (current < target) {
                        const newValue = current + increment;
                        counter.textContent = Math.min(newValue, target).toFixed(target % 1 !== 0 ? 1 : 0);
                        counterStates.set(counter, false);
                    } else {
                        counter.textContent = target;
                        if (target === 99.9) {
                            counter.textContent = '99.9%';
                        } else if (target >= 1000) {
                            counter.textContent = target.toLocaleString() + '+';
                        }
                        counterStates.set(counter, true);
                    }
                });
                
                // Continue animation if any counter is still animating
                if (Array.from(counterStates.values()).some(state => !state)) {
                    requestAnimationFrame(animateCounters);
                }
            }
            
            // Start counter animation when stats section comes into view
            const statsSection = document.querySelector('.stats');
            if (statsSection) {
                const statsObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animateCounters();
                            statsObserver.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.5 });
                
                statsObserver.observe(statsSection);
            }
            
            // Parallax effect for hero elements
            function handleParallax() {
                const parallaxElements = document.querySelectorAll('.parallax-element');
                const scrolled = window.pageYOffset;
                const rate = scrolled * -0.3;
                
                parallaxElements.forEach(element => {
                    element.style.transform = `translateY(${rate}px)`;
                });
            }
            
            // Throttled scroll event for performance
            let isScrolling = false;
            window.addEventListener('scroll', function() {
                if (!isScrolling) {
                    window.requestAnimationFrame(function() {
                        handleParallax();
                        isScrolling = false;
                    });
                    isScrolling = true;
                }
            });
            
            // Smooth scrolling for anchor links
            document.addEventListener('click', function(e) {
                if (e.target.matches('a[href^="#"]')) {
                    e.preventDefault();
                    const target = document.querySelector(e.target.getAttribute('href'));
                    if (target) {
                        const offsetTop = target.offsetTop - 80; // Account for fixed navbar
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                    }
                }
            });
            
            // Keyboard navigation support
            document.addEventListener('keydown', function(e) {
                // Press 'H' to go to top
                if (e.key === 'h' || e.key === 'H') {
                    if (!e.target.matches('input, textarea')) {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                }
            });
            
            // Page visibility API for performance optimization
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // Pause animations when page is not visible
                    document.body.classList.add('page-hidden');
                } else {
                    // Resume animations when page becomes visible
                    document.body.classList.remove('page-hidden');
                }
            });
            
            // Add loading states for better UX
            const images = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy-loading');
                        img.classList.add('lazy-loaded');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            images.forEach(img => {
                img.classList.add('lazy-loading');
                imageObserver.observe(img);
            });
        });
        
        // Service Worker registration for offline support (optional)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(function(error) {
                        console.log('ServiceWorker registration failed: ', error);
                    });
            });
        }


    