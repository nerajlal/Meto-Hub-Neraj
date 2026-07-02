<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Forbidden | Fresh Grocery</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #FAF9F6;
            --text-primary: #111111;
            --text-secondary: #555555;
            --accent-color: #C5A880; /* Gold */
            --accent-hover: #b4966e;
            --border-color: rgba(0, 0, 0, 0.08);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #0A0A0A;
                --text-primary: #FFFFFF;
                --text-secondary: #A0A0A0;
                --border-color: rgba(255, 255, 255, 0.08);
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .ambient-glow {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(197, 168, 128, 0.1) 0%, rgba(0, 0, 0, 0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            pointer-events: none;
            filter: blur(50px);
        }

        .container {
            max-width: 600px;
            padding: 40px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .brand-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--text-primary);
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .error-code {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 130px;
            font-weight: 300;
            line-height: 1;
            color: var(--accent-color);
            margin-bottom: 20px;
            letter-spacing: -2px;
            animation: floatAnimation 6s ease-in-out infinite;
        }

        .error-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .error-description {
            font-size: 16px;
            line-height: 1.7;
            color: var(--text-secondary);
            margin-bottom: 40px;
            font-weight: 300;
        }

        .divider {
            width: 60px;
            height: 1px;
            background-color: var(--accent-color);
            margin: 24px auto;
            opacity: 0.6;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: var(--text-primary);
            color: var(--bg-color);
            text-decoration: none;
            padding: 14px 32px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-radius: 4px;
            border: 1px solid var(--text-primary);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .cta-button:hover {
            background-color: transparent;
            color: var(--text-primary);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }

        @keyframes floatAnimation {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 100px;
            }
            .error-title {
                font-size: 22px;
            }
            .container {
                padding: 24px;
            }
        }
    </style>
</head>
<body>

    <div class="ambient-glow"></div>

    @php
        $tenantId = session('active_tenant_id') 
            ?? (auth()->check() ? auth()->user()->tenant_id : null) 
            ?? session('demo_tenant_id') 
            ?? 1;
        $tenant = \App\Models\Tenant::find($tenantId);
        
        $homeUrl = '/';
        $brand = 'Fresh Grocery';
        if ($tenant) {
            $brand = $tenant->name;
            $theme = $tenant->theme;
            if ($theme === 'velvet_dark') {
                $homeUrl = route('velvet.home');
            } elseif ($theme === 'aura_luxe') {
                $homeUrl = route('v3.home');
            } elseif ($theme === 'editorial_cream') {
                $homeUrl = route('v4.home');
            } elseif ($theme === 'modern_minimal') {
                $homeUrl = route('v1.home');
            }
        }
    @endphp

    <div class="container">
        <div class="brand-name">{{ $brand }}</div>
        
        <div class="error-code">403</div>
        
        <h1 class="error-title">Access Restricted</h1>
        
        <div class="divider"></div>
        
        <p class="error-description">
            You do not have authorization to view this page. Let us guide you back to safety.
        </p>
        
        <a href="{{ $homeUrl }}" class="cta-button">
            Back to Storefront
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

</body>
</html>
