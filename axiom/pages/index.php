<?php
// Page configuration
$page_title = "ModernApp - Beautiful Web Application";
$page_description = "Experience the future of web applications with ModernApp's beautiful, modern design and powerful features.";
$page_keywords = "web application, modern design, technology, digital experience";

// Statistics data
$stats = [
    ['number' => '250+', 'label' => 'Happy Users'],
    ['number' => '99.9', 'label' => 'Uptime'],
    ['number' => '24/7', 'label' => 'Support', 'no_counter' => true],
    ['number' => '50', 'label' => 'Countries']
];

// Features data
$features = [
    [
        'icon' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80',
        'title' => 'Secure & Safe',
        'description' => 'Your data is protected with industry-standard security measures and end-to-end encryption technology.'
    ],
    [
        'icon' => 'https://images.unsplash.com/photo-1551650975-87deedd944c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80',
        'title' => 'Lightning Fast',
        'description' => 'Optimized for speed and performance with advanced caching to give you the best user experience possible.'
    ],
    [
        'icon' => 'https://images.unsplash.com/photo-1559526324-593bc073d938?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80',
        'title' => 'Modern Design',
        'description' => 'Beautiful, intuitive interface designed with the latest UI/UX principles for the modern web experience.'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="<?php echo $page_keywords; ?>">
    <meta name="author" content="Axiom Team">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:image" content="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $page_title; ?>">
    <meta name="twitter:description" content="<?php echo $page_description; ?>">
    <meta name="twitter:image" content="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    
    <!-- External Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/home.css">
    
    <!-- Preload critical images -->
    <link rel="preload" as="image" href="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80">
    
    <!-- Schema.org structured data for SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "ModernApp",
        "description": "<?php echo $page_description; ?>",
        "url": "<?php echo 'http://' . $_SERVER['HTTP_HOST']; ?>",
        "applicationCategory": "WebApplication",
        "operatingSystem": "Any",
        "offers": {
            "@type": "Offer",
            "category": "WebApplication"
        },
        "provider": {
            "@type": "Organization",
            "name": "ModernApp",
            "url": "<?php echo 'http://' . $_SERVER['HTTP_HOST']; ?>"
        }
    }
    </script>
</head>
<body>
    <?php include '../parts/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title parallax-element">Welcome to Axiom</h1>
                    <p class="hero-subtitle parallax-element">Experience the future of web applications with our beautiful, modern design and powerful features that will transform your digital experience.</p>
                    <div class="hero-buttons parallax-element">
                        <a href="register.php" class="btn btn-primary">Get Started</a>
                        <a href="contact.php" class="btn btn-outline">Learn More</a>
                    </div>
                </div>
                <div class="hero-image parallax-element">
                    <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" alt="Modern Technology" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features scroll-reveal">
        <div class="container">
            <h2 class="section-title">Amazing Features</h2>
            <div class="features-grid">
                <?php foreach ($features as $feature): ?>
                <div class="feature-card scroll-reveal">
                    <div class="feature-icon">
                        <img src="<?php echo $feature['icon']; ?>" alt="<?php echo $feature['title']; ?>" loading="lazy">
                    </div>
                    <h3><?php echo $feature['title']; ?></h3>
                    <p><?php echo $feature['description']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about scroll-reveal">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>About Axiom</h2>
                    <p>We're passionate about creating beautiful, functional web applications that make a real difference in people's lives. Our platform combines cutting-edge technology with user-friendly design to deliver exceptional experiences that exceed expectations.</p>
                    <p>Join thousands of satisfied users who trust Axiom for their daily digital needs. Whether you're a beginner exploring new possibilities or an expert seeking advanced features, our platform seamlessly adapts to your unique requirements and grows with you.</p>
                    <a href="register.php" class="btn btn-primary">Join Us Today</a>
                </div>
                <div class="about-image scroll-reveal">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Team Working Together" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats scroll-reveal">
        <div class="container">
            <div class="stats-grid">
                <?php foreach ($stats as $stat): ?>
                <div class="stat-card scroll-reveal">
                    <?php if (isset($stat['no_counter']) && $stat['no_counter']): ?>
                        <h3><?php echo $stat['number']; ?></h3>
                    <?php else: ?>
                        <h3 class="counter" data-target="<?php echo $stat['number']; ?>">0</h3>
                    <?php endif; ?>
                    <p><?php echo $stat['label']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include '../parts/footer.php'; ?>

    <!-- External JavaScript -->
    <script src="js/home.js"></script>
    
    <!-- Component JavaScript -->
    <script>
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
    </script>
    
    <!-- Additional CSS for animations and loading states -->
    <style>
        /* Scroll reveal animations */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.6s ease;
        }
        
        .scroll-reveal.animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Parallax base styles */
        .parallax-element {
            transition: transform 0.1s ease-out;
        }
        
        /* Performance optimization for hidden pages */
        .page-hidden * {
            animation-play-state: paused !important;
        }
        
        /* Focus styles for accessibility */
        a:focus,
        button:focus,
        input:focus,
        textarea:focus {
            outline: 2px solid #667eea;
            outline-offset: 2px;
        }
        
        /* Lazy loading styles */
        .lazy-loading {
            filter: blur(5px);
            transition: filter 0.3s ease;
        }
        
        .lazy-loaded {
            filter: blur(0);
        }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
            
            .parallax-element {
                transform: none !important;
            }
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .btn {
                border: 2px solid currentColor;
            }
            
            .feature-card,
            .stat-card {
                border: 1px solid currentColor;
            }
        }
        
        /* Print styles */
        @media print {
            .navbar,
            .footer,
            .btn {
                display: none !important;
            }
            
            body {
                padding-top: 0 !important;
            }
            
            .hero,
            .features,
            .about,
            .stats {
                page-break-inside: avoid;
                margin-bottom: 2rem;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            /* Dark mode styles can be added here */
        }
    </style>
</body>
</html>