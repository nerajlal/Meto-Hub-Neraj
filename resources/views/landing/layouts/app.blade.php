<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vespr Grocery SaaS - Premium Grocery Business Solution</title>
    <meta name="description"
        content="The ultimate e-commerce SaaS platform for grocery stores and supermarkets. Multi-theme, multi-tenant, and optimized for high-conversion grocery sales.">
    <meta name="keywords"
        content="grocery saas, grocery e-commerce, supermarket website, vespr, grocery business solution">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Vespr Grocery SaaS - Premium Grocery Business Solution">
    <meta property="og:description" content="The ultimate e-commerce SaaS platform for grocery stores and supermarkets. Multi-theme, multi-tenant, and optimized for high-conversion grocery sales.">
    <meta property="og:image" content="{{ asset('Images/landing/v3-template.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="Vespr Grocery SaaS - Premium Grocery Business Solution">
    <meta property="twitter:description" content="The ultimate e-commerce SaaS platform for grocery stores and supermarkets. Multi-theme, multi-tenant, and optimized for high-conversion grocery sales.">
    <meta property="twitter:image" content="{{ asset('Images/landing/v3-template.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-color: #F4F4F4;
            --text-main: #1A1A1A;
            --text-light: #555555;
            --accent-gold: #B88E2F;
            --accent-gold-light: #DFD3BD;
            --white: #FFFFFF;
            --transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            line-height: 1.7;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        .serif {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 1.5rem 0;
            background: rgba(253, 251, 247, 0.8);
            backdrop-filter: blur(10px);
            z-index: 1000;
            border-bottom: 1px solid rgba(197, 160, 89, 0.1);
            transition: var(--transition);
        }

        .navbar.scrolled {
            padding: 1rem 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
            color: var(--text-main);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .logo span {
            color: var(--accent-gold);
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-main);
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
        }

        .nav-link:hover {
            color: var(--accent-gold);
        }

        .menu-toggle {
            display: none;
            font-size: 1.5rem;
            color: var(--text-main);
            cursor: pointer;
            z-index: 1001;
        }

        .btn-premium {
            background: var(--accent-gold);
            color: var(--white);
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
            border: 1px solid var(--accent-gold);
            display: inline-block;
        }

        .btn-premium:hover {
            background: transparent;
            color: var(--accent-gold);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: var(--accent-gold);
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
            border: 1px solid var(--accent-gold);
        }

        .btn-outline:hover {
            background: var(--accent-gold);
            color: var(--white);
        }

        footer {
            padding: 8rem 0 4rem;
            background: #F4F1ED;
            border-top: 1px solid rgba(197, 160, 89, 0.1);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 2fr 1.5fr;
            gap: 4rem;
            margin-bottom: 6rem;
        }

        .footer-links-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .footer-logo {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            display: block;
        }

        .footer-info p {
            color: var(--text-light);
            font-size: 0.95rem;
            margin-bottom: 2rem;
            max-width: 300px;
        }

        .footer-social {
            display: flex;
            gap: 1.5rem;
        }

        .social-icon {
            color: var(--accent-gold);
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .social-icon:hover {
            transform: translateY(-3px);
            color: var(--text-main);
        }

        .footer-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .footer-nav {
            list-style: none;
        }

        .footer-nav li {
            margin-bottom: 1rem;
        }

        .footer-nav a {
            text-decoration: none;
            color: var(--text-light);
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .footer-nav a:hover {
            color: var(--accent-gold);
            padding-left: 5px;
        }

        .newsletter-form {
            display: flex;
            gap: 0;
            margin-top: 1.5rem;
        }

        .newsletter-input {
            padding: 1rem 1.5rem;
            border: 1px solid var(--accent-gold-light);
            border-radius: 50px 0 0 50px;
            background: var(--white);
            flex-grow: 1;
            font-family: inherit;
            outline: none;
        }

        .newsletter-btn {
            background: var(--accent-gold);
            color: var(--white);
            border: none;
            padding: 0 1.5rem;
            border-radius: 0 50px 50px 0;
            cursor: pointer;
            transition: var(--transition);
        }

        .newsletter-btn:hover {
            background: var(--text-main);
        }

        .footer-bottom {
            padding-top: 3rem;
            border-top: 1px solid rgba(197, 160, 89, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .copyright {
            color: var(--text-light);
            font-size: 0.85rem;
        }

        @media (max-width: 992px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }

            .footer-newsletter {
                grid-column: span 2;
            }
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 4rem;
            }

            .footer-newsletter {
                grid-column: span 1;
            }
        }

        @media (max-width: 576px) {
            .footer-bottom {
                flex-direction: column;
                gap: 1.5rem;
                text-align: center;
            }
        }

        /* Animations */
        [data-reveal] {
            opacity: 0;
            transform: translateY(30px);
            transition: all 1s ease-out;
        }

        [data-reveal].revealed {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-links {
                position: fixed;
                top: 0;
                right: -100%;
                width: 80%;
                height: 100vh;
                background: var(--bg-color);
                flex-direction: column;
                justify-content: center;
                gap: 2rem;
                padding: 4rem;
                transition: var(--transition);
                box-shadow: -10px 0 30px rgba(0, 0, 0, 0.05);
                display: flex;
                /* Overriding display:none from previous turn */
            }

            .nav-links.active {
                right: 0;
            }

            .nav-links .btn-premium {
                width: 100%;
                text-align: center;
            }
        }
        /* Demo Modal */
        .demo-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .demo-modal.active {
            display: flex;
            opacity: 1;
        }

        .demo-modal-content {
            background: var(--white);
            padding: 3rem;
            border-radius: 30px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }

        .demo-modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.5rem;
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition);
        }

        .demo-modal-close:hover {
            color: var(--accent-gold);
        }

        .demo-form-group {
            margin-bottom: 1.5rem;
        }

        .demo-form-group label {
            display: block;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            color: var(--text-light);
        }

        .demo-input {
            width: 100%;
            padding: 1rem;
            border: 1px solid rgba(197, 160, 89, 0.2);
            border-radius: 10px;
            background: #fcfcfc;
            font-family: 'Montserrat', sans-serif;
            transition: var(--transition);
        }

        .demo-input:focus {
            outline: none;
            border-color: var(--accent-gold);
            background: white;
        }
    </style>
    @yield('styles')
</head>

<body>
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="{{ route('landing') }}" class="logo">VESPR<span>.</span>SAAS</a>
                <div class="menu-toggle" id="menu-toggle">
                    <i class="fa-solid fa-bars-staggered"></i>
                </div>
                <div class="nav-links" id="nav-links">
                    <a href="https://vespr.com" target="_blank" class="nav-link">VESPR</a>
                    <a href="{{ route('landing.old') }}#features" class="nav-link">Features</a>
                    <a href="{{ route('landing.templates') }}" class="nav-link">Templates</a>
                    <a href="{{ route('landing.old') }}#pricing" class="nav-link">Pricing</a>
                    <a href="{{ route('landing.old') }}#contact" class="nav-link">Enquiry</a>
                    <a href="{{ route('admin.common.login') }}" class="btn-premium">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-info">
                    <a href="#" class="logo footer-logo">Vespr<span>.</span>SAAS</a>
                    <p>The ultimate e-commerce solution for the modern grocery industry. Elevating brands with premium
                        digital experiences.</p>
                    <div class="footer-social">
                        <a href="#" class="social-icon"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="footer-links-row">
                    <div>
                        <h4 class="footer-title">Solutions</h4>
                        <ul class="footer-nav">
                            <li><a href="{{ route('v5.home') }}">Aura Theme</a></li>
                            <li><a href="{{ route('velvet.home') }}">Velvet Theme</a></li>
                            <li><a href="{{ route('landing.templates') }}">Theme Catalog</a></li>
                            <!-- <li><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li> -->
                        </ul>
                    </div>

                    <div>
                        <h4 class="footer-title">Company</h4>
                        <ul class="footer-nav">
                            <li><a href="{{ route('landing.old') }}#features">Features</a></li>
                            <li><a href="{{ route('landing.old') }}#pricing">Pricing</a></li>
                            <li><a href="{{ route('landing.old') }}#contact">Contact Us</a></li>
                            <li><a href="{{ route('landing.templates') }}">Live Demos</a></li>
                        </ul>
                    </div>
                </div>

                <div class="footer-newsletter">
                    <h4 class="footer-title">Stay Inspired</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Receive exclusive insights into the digital
                        grocery landscape.</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Email address" class="newsletter-input">
                        <button class="newsletter-btn"><i class="fa-solid fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="copyright">&copy; {{ date('Y') }} Vespr Grocery SaaS. All rights reserved.</p>
                <div class="footer-nav" style="display: flex; gap: 2rem;">
                    <a href="#" style="margin-bottom: 0;">Privacy Policy</a>
                    <a href="#" style="margin-bottom: 0;">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Demo Access Modal -->
    <div class="demo-modal" id="demoModal">
        <div class="demo-modal-content">
            <div class="demo-modal-close" id="closeDemoModal">&times;</div>
            <div style="text-align: center; margin-bottom: 2rem;">
                <h3 style="font-size: 1.8rem; margin-bottom: 0.5rem;">Experience VESPR</h3>
                <p style="color: var(--text-light); font-size: 0.9rem;">Please provide your details to access the live demo.</p>
            </div>
            <form id="demoAccessForm">
                <input type="hidden" id="targetDemoUrl">
                <div class="demo-form-group">
                    <label>Business Name</label>
                    <input type="text" name="business_name" class="demo-input" required placeholder="Your Brand Name">
                </div>
                <div class="demo-form-group">
                    <label>Business Email</label>
                    <input type="email" name="email" class="demo-input" required placeholder="name@company.com">
                </div>
                <button type="submit" class="btn-premium" style="width: 100%; border: none; cursor: pointer;">Access Live Demo</button>
            </form>
        </div>
    </div>

    <script>
        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Reveal Animations
        const revealElements = document.querySelectorAll('[data-reveal]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                }
            });
        }, { threshold: 0.1 });

        revealElements.forEach(el => observer.observe(el));

        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menu-toggle');
        const navLinks = document.getElementById('nav-links');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            const icon = menuToggle.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.replace('fa-bars-staggered', 'fa-xmark');
            } else {
                icon.classList.replace('fa-xmark', 'fa-bars-staggered');
            }
        });

        // Close menu on link click
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                menuToggle.querySelector('i').classList.replace('fa-xmark', 'fa-bars-staggered');
            });
        });

        // Demo Modal Logic
        const demoModal = document.getElementById('demoModal');
        const demoForm = document.getElementById('demoAccessForm');
        const closeDemoBtn = document.getElementById('closeDemoModal');
        const targetInput = document.getElementById('targetDemoUrl');

        // Function to open modal
        window.openDemoAccess = function(e, url) {
            if(e) e.preventDefault();
            
            // Check if user has already provided details
            if (localStorage.getItem('vespr_demo_accessed')) {
                window.open(url, '_blank');
                return;
            }

            targetInput.value = url;
            demoModal.style.display = 'flex';
            setTimeout(() => {
                demoModal.classList.add('active');
            }, 10);
        };

        closeDemoBtn.addEventListener('click', () => {
            demoModal.classList.remove('active');
            setTimeout(() => {
                demoModal.style.display = 'none';
            }, 300);
        });

        window.addEventListener('click', (e) => {
            if (e.target === demoModal) {
                closeDemoBtn.click();
            }
        });

        demoForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Save access flag to local storage
            localStorage.setItem('vespr_demo_accessed', 'true');
            
            const targetUrl = targetInput.value;
            window.open(targetUrl, '_blank');
            closeDemoBtn.click();
            demoForm.reset();
        });
    </script>
    @yield('scripts')
</body>

</html>