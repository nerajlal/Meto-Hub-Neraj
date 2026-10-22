<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error — MetoHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/metohub-reference.css') }}?v={{ filemtime(public_path('css/metohub-reference.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/metohub-home.css') }}?v={{ filemtime(public_path('css/metohub-home.css')) }}">
</head>
<body class="min-h-screen bg-background text-foreground antialiased flex flex-col justify-between selection:bg-bronze/20">
    <header class="p-6 sm:p-8">
        <a class="inline-flex items-center gap-2 text-lg font-semibold tracking-tight text-foreground transition hover:opacity-80" href="{{ route('landing') }}">
            <span class="grid h-8 w-8 place-items-center rounded-md bg-primary font-serif text-xl italic text-primary-foreground">M</span>
            <span>MetoHub</span>
        </a>
    </header>

    <main class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="max-w-md w-full text-center">
            <span class="font-serif text-8xl sm:text-9xl italic font-normal text-bronze block leading-none select-none tracking-tight">500</span>
            <p class="eyebrow mt-4">System Notice</p>
            <h1 class="mt-3 text-3xl font-medium tracking-[-0.02em] text-foreground">Something went sideways</h1>
            <p class="mt-3 text-sm leading-relaxed text-muted-foreground">
                Our servers encountered an unexpected hiccup. Our engineering team has been alerted. Please try refreshing in a moment.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <button onclick="window.location.reload()" class="btn-ghost !px-5 !py-2.5 text-sm cursor-pointer">
                    <span aria-hidden="true">↻</span> Refresh page
                </button>
                <a href="{{ route('landing') }}" class="btn-primary !px-5 !py-2.5 text-sm">
                    Return to home <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </main>

    <footer class="p-6 text-center text-xs text-muted-foreground">
        <span>© {{ date('Y') }} MetoHub. All rights reserved.</span>
    </footer>
</body>
</html>

