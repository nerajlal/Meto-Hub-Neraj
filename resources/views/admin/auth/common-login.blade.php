@extends('layouts.landing')
@section('content')
    <style>
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
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 0.88rem;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            cursor: pointer;
            font-weight: 500;
        }
        .remember input {
            accent-color: var(--primary);
            cursor: pointer;
            width: 16px;
            height: 16px;
        }
        .forgot-link {
            color: var(--primary);
            font-weight: 600;
            transition: color 0.2s;
        }
        .forgot-link:hover {
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
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(46,125,50,0.3);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        .btn-google {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: inherit;
            font-size: 0.92rem;
            font-weight: 600;
            padding: 12px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: var(--white);
            color: var(--dark);
            cursor: pointer;
            transition: border-color 0.2s, background-color 0.2s, transform 0.2s;
        }
        .btn-google:hover {
            border-color: var(--primary);
            background-color: var(--bg);
            transform: translateY(-1px);
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
        .alert-error ul { list-style: none; }

        .alert-error ul { list-style: none; }
    </style>

    <!-- MAIN -->
    <section class="login-section">
        <div class="login-card">
            <div class="login-header">
                <h1>Log in to GoSlot Store</h1>
                <p>Access your centralized merchant dashboard</p>
            </div>

            <form method="POST" action="{{ route('admin.common.login.submit') }}">
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
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@example.com">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>

                <div class="form-row">
                    <label class="remember">
                        <input type="checkbox" name="remember"> Keep me logged in
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-login">Log in to dashboard</button>

                <div class="divider">or</div>

                <a href="{{ route('google.login') }}" class="btn-google">
                    <svg width="18" height="18" viewBox="0 0 18 18">
                        <path fill="#4285F4" d="M17.64 9.2c0-.63-.06-1.25-.16-1.84H9v3.47h4.84a4.14 4.14 0 0 1-1.8 2.71v2.26h2.9c1.7-1.57 2.7-3.88 2.7-6.6z"/>
                        <path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.2l-2.9-2.26c-.8.54-1.85.87-3.06.87-2.35 0-4.33-1.58-5.04-3.71H.94v2.33A9 9 0 0 0 9 18z"/>
                        <path fill="#FBBC05" d="M3.96 10.7a5.4 5.4 0 0 1 0-3.4V4.97H.94a9 9 0 0 0 0 8.06l3.02-2.33z"/>
                        <path fill="#EA4335" d="M9 3.58c1.32 0 2.5.45 3.44 1.35L15 2.1A9 9 0 0 0 .94 4.97l3.02 2.33C4.67 5.16 6.65 3.58 9 3.58z"/>
                    </svg>
                    Continue with Google
                </a>
            </form>
        </div>
    </section>
@endsection
