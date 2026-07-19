<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoSlot Store - Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --primary: #2E7D32;
            --primary-dark: #1B5E20;
            --secondary: #43A047;
            --accent: #FFB300;
            --bg: #F8FAF7;
            --dark: #1F2937;
            --white: #FFFFFF;
            --ink: #16241A;
            --muted: #5B6B60;
            --line: rgba(31,41,55,0.08);
            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --shadow-sm: 0 2px 8px rgba(31,41,55,0.06);
            --shadow-md: 0 12px 32px rgba(31,41,55,0.10);
            --shadow-lg: 0 24px 64px rgba(31,41,55,0.14);
            --ease: cubic-bezier(.16,.84,.44,1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4 {
            font-family: 'Poppins', sans-serif;
            color: var(--dark);
            line-height: 1.15;
            letter-spacing: -0.02em;
        }

        p { color: var(--muted); line-height: 1.7; }
        a { color: inherit; text-decoration: none; }

        /* ---------- Buttons ---------- */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 999px;
            border: 1px solid transparent;
            transition: transform .25s var(--ease), box-shadow .25s var(--ease), background .25s var(--ease), color .25s var(--ease);
            white-space: nowrap;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--white);
            box-shadow: 0 8px 24px rgba(46,125,50,0.28);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 32px rgba(46,125,50,0.36); }

        .btn-ghost { color: var(--dark); font-weight: 500; }
        .btn-ghost:hover { color: var(--primary); }

        /* ---------- Header ---------- */
        .site-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            padding: 12px 8vw;
            background: rgba(248,250,247,0.72);
            backdrop-filter: blur(16px) saturate(160%);
            -webkit-backdrop-filter: blur(16px) saturate(160%);
            box-shadow: 0 4px 24px rgba(31,41,55,0.06);
            border-bottom: 1px solid var(--line);
        }
        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }
        .logo { display: flex; align-items: center; gap: 10px; }
        .logo-text { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--dark); }
        .logo-accent { color: var(--primary); }

        .main-nav { display: flex; gap: 32px; }
        .main-nav a {
            font-size: 0.92rem;
            font-weight: 500;
            color: var(--dark);
            position: relative;
            padding: 4px 0;
        }
        .main-nav a::after {
            content: '';
            position: absolute; left: 0; bottom: -2px;
            width: 0; height: 2px;
            background: var(--primary);
            transition: width .25s var(--ease);
        }
        .main-nav a:hover::after { width: 100%; }

        .header-actions { display: flex; align-items: center; gap: 14px; }

        @media (max-width: 980px) {
            .main-nav { display: none; }
        }

        /* ---------- Main Section ---------- */
        .login-section {
            flex: 1;
            padding-top: 140px;
            padding-bottom: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(ellipse at top right, rgba(67,160,71,0.08), transparent 60%), var(--bg);
            position: relative;
        }

        .login-card {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 46px 40px;
            width: 100%;
            max-width: 460px;
            box-shadow: var(--shadow-lg);
            z-index: 10;
            transition: transform 0.3s var(--ease);
        }
        .login-card:hover { transform: translateY(-4px); }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--dark);
        }
        .login-header p {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-family: 'Poppins', sans-serif;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-group input {
            background: var(--bg);
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--dark);
            width: 100%;
            outline: none;
            transition: border-color 0.25s var(--ease), box-shadow 0.25s var(--ease);
        }
        .form-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46,125,50,0.15);
        }

        .form-row {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 24px;
            font-size: 0.88rem;
        }
        .back-link {
            color: var(--primary);
            font-weight: 600;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            border-radius: 999px;
            border: none;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--white);
            font-family: inherit;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(46,125,50,0.2);
            transition: transform 0.2s var(--ease), box-shadow 0.2s var(--ease);
            margin-bottom: 20px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(46,125,50,0.3);
        }

        .alert-error {
            background: rgba(220,38,38,0.06);
            border-left: 4px solid #dc2626;
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            color: #dc2626;
        }
        .alert-error ul { list-style: none; margin: 0; padding-left: 20px; }
        
        .alert-success {
            background: rgba(46,125,50,0.06);
            border-left: 4px solid var(--primary);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            color: var(--primary-dark);
        }

        /* ---------- Footer ---------- */
        .site-footer {
            background: var(--dark);
            color: rgba(255,255,255,0.7);
            padding: 60px 8vw 30px;
        }
        .footer-top {
            max-width: 1300px; margin: 0 auto 40px;
            display: grid;
            grid-template-columns: 2fr repeat(3, 1fr);
            gap: 32px;
        }
        @media (max-width: 768px) {
            .footer-top { grid-template-columns: 1fr; gap: 24px; text-align: center; }
            .footer-brand { display: flex; flex-direction: column; align-items: center; }
        }
        .footer-brand p { color: rgba(255,255,255,0.55); font-size: 0.88rem; margin-top: 14px; max-width: 320px; }
        .footer-col h4 { color: var(--white); font-size: 0.9rem; margin-bottom: 16px; font-weight: 600; }
        .footer-col { display: flex; flex-direction: column; gap: 10px; }
        .footer-col a { font-size: 0.88rem; color: rgba(255,255,255,0.55); transition: color .25s var(--ease); }
        .footer-col a:hover { color: var(--accent); }
        .footer-bottom {
            max-width: 1300px; margin: 0 auto;
            display: flex; justify-content: space-between; align-items: center;
            padding-top: 26px;
            border-top: 1px solid rgba(255,255,255,0.08);
            font-size: 0.82rem;
            flex-wrap: wrap;
            gap: 16px;
        }
        @media (max-width: 768px) {
            .footer-bottom { justify-content: center; }
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <header class="site-header" id="siteHeader">
      <div class="header-inner">
        <a href="{{ route('landing') }}" class="logo" aria-label="GoSlot Store home">
          <span class="logo-mark" aria-hidden="true">
            <svg viewBox="0 0 32 32" width="30" height="30">
              <rect x="3" y="10" width="26" height="18" rx="4" fill="var(--primary)"/>
              <rect x="3" y="10" width="26" height="6" rx="3" fill="var(--accent)"/>
              <path d="M9 10 L11 4 H21 L23 10" stroke="var(--primary)" stroke-width="2.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
          <span class="logo-text">GoSlot<span class="logo-accent">Store</span></span>
        </a>

        <nav class="main-nav" id="mainNav" aria-label="Primary">
          <a href="{{ route('landing') }}#home">Home</a>
          <a href="{{ route('landing') }}#features">Features</a>
          <a href="{{ route('landing') }}#pricing">Pricing</a>
          <a href="{{ route('landing') }}#themes">Themes</a>
          <a href="{{ route('landing') }}#contact">Contact</a>
        </nav>

        <div class="header-actions">
          <a href="{{ route('admin.common.login') }}" class="btn btn-ghost">Log in</a>
          <a href="{{ route('landing') }}?get_started=1" class="btn btn-primary">Get Started</a>
        </div>
      </div>
    </header>

    <!-- MAIN -->
    <section class="login-section">
        <div class="login-card">
            <div class="login-header">
                <h1>Forgot Password</h1>
                <p>Enter your email and we'll send you a 6-digit verification code</p>
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if (session('status'))
                    <div class="alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com">
                </div>

                <button type="submit" class="btn-login">Send Verification Code</button>

                <div class="form-row">
                    <a href="{{ route('admin.common.login') }}" class="back-link"><i class="fas fa-arrow-left me-1"></i> Back to Login</a>
                </div>
            </form>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="site-footer">
      <div class="footer-top">
        <div class="footer-brand">
          <div class="logo">
            <span class="logo-mark">
              <svg viewBox="0 0 32 32" width="24" height="24">
                <rect x="3" y="10" width="26" height="18" rx="4" fill="var(--primary)"/>
                <rect x="3" y="10" width="26" height="6" rx="3" fill="var(--accent)"/>
                <path d="M9 10 L11 4 H21 L23 10" stroke="var(--primary)" stroke-width="2.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            <span class="logo-text" style="color: #fff;">GoSlot<span class="logo-accent">Store</span></span>
          </div>
          <p>The SaaS platform built for independent grocers, running advanced inventory systems and real-time delivery routes.</p>
        </div>
        <div class="footer-col">
          <h4>Product</h4>
          <a href="{{ route('landing') }}#features">Features</a>
          <a href="{{ route('landing') }}#themes">Themes</a>
          <a href="{{ route('landing') }}#pricing">Pricing</a>
        </div>
        <div class="footer-col">
          <h4>Company</h4>
          <a href="#">About us</a>
          <a href="#">Careers</a>
          <a href="#">Press Kit</a>
        </div>
        <div class="footer-col">
          <h4>Legal</h4>
          <a href="#">Terms of Use</a>
          <a href="#">Privacy Policy</a>
          <a href="#">SLA Agreement</a>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2026 GoSlot Store. All rights reserved.</span>
        <span>Made for grocers worldwide.</span>
      </div>
    </footer>

</body>
</html>
