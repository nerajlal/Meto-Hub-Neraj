<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetoHub Admin - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root{
            --paper:#FDFCFA;--paper-dim:#F7F5F0;--ink:#1C231B;--ink-soft:#4B5245;--ink-faint:#7C8177;
            --green:#3F6C4E;--green-deep:#2A4A35;--green-pale:#E7EFE7;
            --yellow:#E8B93F;--yellow-deep:#8A6414;--line:#DDD8CB;--card:#FFFFFF;--radius:14px;
            --font-display:'Archivo', sans-serif;--font-body:'Inter', sans-serif;--font-mono:'IBM Plex Mono', monospace;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{background:var(--paper);color:var(--ink);font-family:var(--font-body);font-size:16px;line-height:1.6;-webkit-font-smoothing:antialiased;min-height:100vh;display:flex;flex-direction:column;}
        a{color:inherit;text-decoration:none;}
        .wrap{max-width:1180px;margin:0 auto;padding:0 32px;}
        header{position:sticky;top:0;z-index:50;background:rgba(253,252,250,0.88);backdrop-filter:blur(10px);border-bottom:1px solid var(--line);}
        nav{max-width:1180px;margin:0 auto;padding:0 32px;height:76px;display:flex;align-items:center;justify-content:space-between;}
        .logo{font-family:var(--font-display);font-weight:800;font-size:21px;display:flex;align-items:center;gap:8px;}
        .logo-mark{width:22px;height:22px;background:var(--green);clip-path:polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);}
        .nav-links{display:flex;gap:36px;font-size:14.5px;color:var(--ink-soft);}
        .nav-links a:hover{color:var(--ink);}
        .nav-cta{display:flex;align-items:center;gap:20px;}
        .btn{font-family:var(--font-body);font-weight:600;font-size:14.5px;padding:11px 22px;border-radius:9px;display:inline-block;border:1px solid transparent;cursor:pointer;transition:transform .15s ease, background .15s ease;}
        .btn:hover{transform:translateY(-1px);}
        .btn-primary{background:var(--ink);color:var(--paper);}
        .btn-primary:hover{background:var(--green-deep);}
        .btn-ghost{color:var(--ink);}
        .main-content{flex:1;display:flex;align-items:center;justify-content:center;padding:60px 20px;}
        .login-card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);padding:40px;width:100%;max-width:420px;}
        .login-header{text-align:center;margin-bottom:28px;}
        .login-header h1{font-family:var(--font-display);font-size:24px;font-weight:700;letter-spacing:-0.02em;margin-bottom:8px;}
        .login-header p{font-size:14px;color:var(--ink-faint);}
        .form-group{margin-bottom:18px;display:flex;flex-direction:column;}
        .form-group label{font-size:13px;font-weight:600;color:var(--ink);margin-bottom:6px;}
        .form-group input{font-family:var(--font-body);font-size:14px;padding:11px 14px;border:1px solid var(--line);border-radius:9px;background:var(--paper);color:var(--ink);outline:none;transition:border-color .15s ease, box-shadow .15s ease;}
        .form-group input:focus{border-color:var(--green);box-shadow:0 0 0 3px var(--green-pale);}
        .form-group input::placeholder{color:var(--ink-faint);}
        .form-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
        .form-row .remember{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ink-soft);cursor:pointer;}
        .form-row .remember input{accent-color:var(--green);cursor:pointer;}
        .form-row a{font-size:12px;color:var(--ink-faint);transition:color .15s;}
        .form-row a:hover{color:var(--green);}
        .btn-login{width:100%;font-family:var(--font-body);font-weight:600;font-size:14.5px;padding:12px;border-radius:9px;border:none;background:var(--ink);color:var(--paper);cursor:pointer;transition:background .15s ease, transform .15s ease;}
        .btn-login:hover{background:var(--green-deep);transform:translateY(-1px);}
        .alert-error{background:rgba(220,38,38,0.06);border-left:3px solid #dc2626;border-radius:6px;padding:10px 14px;margin-bottom:18px;font-size:13px;color:#dc2626;}
        .alert-error ul{list-style:none;padding:0;margin:0;}
        footer{border-top:1px solid var(--line);padding:56px 0 40px;}
        .footer-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:32px;margin-bottom:48px;}
        .footer-col h5{font-family:var(--font-mono);font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-faint);margin-bottom:16px;font-weight:500;}
        .footer-col a{display:block;font-size:14px;color:var(--ink-soft);margin-bottom:10px;}
        .footer-col a:hover{color:var(--ink);}
        .footer-bottom{display:flex;justify-content:space-between;align-items:center;padding-top:28px;border-top:1px solid var(--line);font-size:13px;color:var(--ink-faint);}
        @media (max-width:920px){.nav-links{display:none;}.footer-grid{grid-template-columns:1fr 1fr;}}
        @media (max-width:480px){.login-card{padding:28px 22px;}.footer-grid{grid-template-columns:1fr;}}
    </style>
</head>
<body>
    <header>
      <nav>
        <a href="{{ route('landing') }}" class="logo"><span class="logo-mark"></span>MetoHub</a>
        <div class="nav-links">
          <a href="{{ route('landing') }}#how">How it works</a>
          <a href="{{ route('landing') }}#templates">Templates</a>
          <a href="{{ route('landing') }}#pricing">Pricing</a>
          <a href="{{ route('landing') }}#faq">FAQ</a>
        </div>
        <div class="nav-cta">
          <a href="{{ route('admin.common.login') }}" class="btn btn-ghost">Log in</a>
          <a href="{{ route('landing') }}?get_started=1" class="btn btn-primary">Start free</a>
        </div>
      </nav>
    </header>

    <div class="main-content">
        <div class="login-card">
            <div class="login-header">
                <h1>Log in to {{ $currentTenant->name ?? 'MetoHub' }} Admin</h1>
                <p>Enter your email and password to access your store's dashboard.</p>
            </div>

            <form method="POST" action="{{ route('admin.login.submit', ['tenant' => $currentTenant->id ?? 1]) }}">
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

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@example.com">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>

                <div class="form-row">
                    <label class="remember">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button type="submit" class="btn-login">Log in</button>
            </form>
        </div>
    </div>

    <footer>
      <div class="wrap">
        <div class="footer-grid">
          <div class="footer-col">
            <div class="logo" style="margin-bottom:14px;"><span class="logo-mark"></span>MetoHub</div>
            <p style="font-size:13.5px;color:var(--ink-faint);max-width:240px;">The storefront builder for independent grocers, made for real inventory and real delivery routes.</p>
          </div>
          <div class="footer-col">
            <h5>Product</h5>
            <a href="{{ route('landing') }}#how">How it works</a>
            <a href="{{ route('landing') }}#templates">Templates</a>
            <a href="{{ route('landing') }}#pricing">Pricing</a>
          </div>
          <div class="footer-col">
            <h5>Company</h5>
            <a href="#">About</a>
            <a href="#">Contact</a>
            <a href="#">Careers</a>
          </div>
          <div class="footer-col">
            <h5>Legal</h5>
            <a href="#">Terms</a>
            <a href="#">Privacy</a>
          </div>
        </div>
        <div class="footer-bottom">
          <span>© 2026 MetoHub. Made for grocers.</span>
          <span>Kayamkulam · Remote</span>
        </div>
      </div>
    </footer>
</body>
</html>
