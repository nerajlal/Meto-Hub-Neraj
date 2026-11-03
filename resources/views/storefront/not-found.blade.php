<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex,nofollow">
    <title>Store Not Found — MetoHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Fraunces:ital,opsz,wght@0,9..144,400..700;1,9..144,400..700&display=swap">
    <style>
        :root {
            --paper: #FDFBF7;
            --ink: #0F172A;
            --muted: #64748B;
            --amber: #D97706;
            --card-border: rgba(15, 23, 42, 0.08);
            --card-bg: #FFFFFF;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            background-color: var(--paper);
            color: var(--ink);
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 24px;
        }
        .grain {
            pointer-events: none;
            position: fixed;
            inset: 0;
            z-index: 999;
            opacity: 0.035;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }
        .brand-header {
            width: 100%;
            max-width: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 12px;
        }
        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--ink);
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: -0.02em;
        }
        .card {
            max-width: 500px;
            width: 100%;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 44px 36px;
            text-align: center;
            box-shadow: 0 16px 40px -12px rgba(15, 23, 42, 0.06);
            position: relative;
            z-index: 1;
        }
        .icon-circle {
            width: 68px;
            height: 68px;
            border-radius: 20px;
            background: rgba(217, 119, 6, 0.1);
            color: var(--amber);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }
        .icon-circle svg {
            width: 32px;
            height: 32px;
        }
        .badge {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 9999px;
            background: rgba(217, 119, 6, 0.1);
            color: #b45309;
            border: 1px solid rgba(217, 119, 6, 0.2);
            margin-bottom: 16px;
        }
        h1 {
            font-family: 'Fraunces', serif;
            font-size: 1.85rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
            color: var(--ink);
        }
        p {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 22px;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-primary {
            background: var(--ink);
            color: #FFFFFF;
            border: 1px solid var(--ink);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.1);
        }
        .btn-primary:hover {
            background: #1e293b;
            color: #FFFFFF;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: transparent;
            color: var(--ink);
            border: 1px solid rgba(15, 23, 42, 0.15);
        }
        .btn-secondary:hover {
            background: rgba(15, 23, 42, 0.04);
            border-color: rgba(15, 23, 42, 0.3);
            transform: translateY(-1px);
        }
        footer {
            font-size: 0.8rem;
            color: var(--muted);
            padding: 16px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="grain" aria-hidden="true"></div>

    <header class="brand-header">
        <a class="brand-link" href="{{ route('landing') }}">
            <img src="{{ asset('images/logo.svg') }}?v={{ filemtime(public_path('images/logo.svg')) }}" alt="MetoHub" width="30" height="30">
            <span>MetoHub</span>
        </a>
    </header>

    <main class="card">
        <div class="icon-circle" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div>
            <span class="badge">Storefront Offline</span>
        </div>
        <h1>Store not found</h1>
        <p>There is no active store at this address. Please check the spelling of your URL or return to explore our store directory.</p>
        <div class="btn-group">
            <button onclick="history.back()" class="btn btn-secondary">
                ← Go back
            </button>
            <a href="{{ route('landing') }}" class="btn btn-primary">
                Explore MetoHub →
            </a>
        </div>
    </main>

    <footer>
        <span>© {{ date('Y') }} MetoHub. All rights reserved.</span>
    </footer>
</body>
</html>
