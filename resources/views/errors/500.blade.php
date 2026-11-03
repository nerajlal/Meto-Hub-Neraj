<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Server Error — MetoHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500;600&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Fraunces:ital,opsz,wght,SOFT,WONK@0,9..144,300..900,0..100,0..1;1,9..144,300..900,0..100,0..1&family=Instrument+Serif:ital@0;1&family=Manrope:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/metohub-reference.css') }}?v={{ filemtime(public_path('css/metohub-reference.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/metora-style.css') }}?v={{ filemtime(public_path('css/metora-style.css')) }}">
    <style>
        body {
            background-color: var(--paper, #FDFBF7);
            color: var(--ink, #0F172A);
            font-family: var(--sans, 'DM Sans', system-ui, sans-serif);
        }
        .grain {
            pointer-events: none;
            position: fixed;
            inset: 0;
            z-index: 999;
            opacity: 0.035;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }
        .error-num {
            font-family: var(--serif, 'Fraunces', serif);
            color: var(--red, #D97706);
            letter-spacing: -0.04em;
        }
        .error-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            background: rgba(217, 119, 6, 0.1);
            color: #b45309;
            border: 1px solid rgba(217, 119, 6, 0.2);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .btn-brand-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: var(--ink, #0F172A);
            color: #FFFFFF;
            padding: 0.7rem 1.4rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.12);
        }
        .btn-brand-primary:hover {
            background: #1e293b;
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.18);
        }
        .btn-brand-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: transparent;
            color: var(--ink, #0F172A);
            border: 1px solid rgba(15, 23, 42, 0.15);
            padding: 0.7rem 1.4rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-brand-secondary:hover {
            background: rgba(15, 23, 42, 0.04);
            border-color: rgba(15, 23, 42, 0.3);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="min-h-screen antialiased flex flex-col justify-between selection:bg-amber-500/20">
    <div class="grain" aria-hidden="true"></div>

    <header class="p-6 sm:p-8">
        <a class="inline-flex items-center gap-2.5 text-lg font-semibold tracking-tight text-foreground transition hover:opacity-80" href="{{ route('landing') }}">
            <img src="{{ asset('images/logo.svg') }}?v={{ filemtime(public_path('images/logo.svg')) }}" alt="MetoHub" class="h-8 w-8 object-contain" width="32" height="32">
            <span>MetoHub</span>
        </a>
    </header>

    <main class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="max-w-md w-full text-center">
            <span class="error-num text-8xl sm:text-9xl italic font-normal block leading-none select-none">500</span>
            <div class="mt-4">
                <span class="error-tag">System Notice</span>
            </div>
            <h1 class="mt-4 text-3xl sm:text-4xl font-semibold tracking-[-0.02em] text-foreground" style="font-family: var(--serif, 'Fraunces', serif);">
                Something went sideways
            </h1>
            <p class="mt-3 text-sm leading-relaxed text-muted-foreground">
                Our servers encountered an unexpected hiccup. Our engineering team has been alerted. Please try refreshing in a moment.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button onclick="window.location.reload()" class="btn-brand-secondary">
                    <span aria-hidden="true">↻</span> Refresh page
                </button>
                <a href="{{ route('landing') }}" class="btn-brand-primary">
                    Return to home <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </main>

    <footer class="p-6 text-center text-xs text-muted-foreground border-t border-border/40">
        <span>© {{ date('Y') }} MetoHub. All rights reserved.</span>
    </footer>
</body>
</html>
