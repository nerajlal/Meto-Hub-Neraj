<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Store Under Maintenance — {{ $tenant->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-primary: {{ $tenant->primary_color ?: '#7c3aed' }};
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #fbfaff 0%, #f4f0ff 100%);
            color: #1f2937;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            padding: 24px;
        }
        .maintenance-card {
            max-width: 520px;
            width: 100%;
            background: #ffffff;
            border: 1px solid #ede9fe;
            border-radius: 24px;
            box-shadow: 0 20px 40px -15px rgba(124, 58, 237, 0.08);
            padding: 44px 36px;
            text-align: center;
        }
        .store-icon {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: #fef2f2;
            color: #dc2626;
            display: grid;
            place-items: center;
            font-size: 32px;
            margin: 0 auto 24px;
        }
        .store-logo {
            max-height: 60px;
            margin: 0 auto 20px;
            display: block;
            object-fit: contain;
        }
        h1 {
            font-size: 26px;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
        }
        .badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 100px;
            background: #fee2e2;
            color: #991b1b;
            margin-bottom: 18px;
        }
        p {
            color: #4b5563;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 28px;
        }
        .contact-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            font-size: 13px;
            color: #6b7280;
        }
        .contact-box a {
            color: var(--brand-primary);
            font-weight: 600;
            text-decoration: none;
        }
        .contact-box a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main class="maintenance-card">
        @if($tenant->logo)
            <img class="store-logo" src="{{ Storage::url($tenant->logo) }}" alt="{{ $tenant->name }}">
        @endif
        <div class="store-icon" aria-hidden="true">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/>
                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/>
                <path d="M2 7h20"/>
            </svg>
        </div>
        <span class="badge">Store Maintenance</span>
        <h1>We'll be right back</h1>
        <p>{{ $message }}</p>

        @if($tenant->contact_email || $tenant->whatsapp_number)
            <div class="contact-box">
                Need urgent assistance?
                @if($tenant->whatsapp_number)
                    Reach out on <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tenant->whatsapp_number) }}" target="_blank">WhatsApp</a>
                @elseif($tenant->contact_email)
                    Email us at <a href="mailto:{{ $tenant->contact_email }}">{{ $tenant->contact_email }}</a>
                @endif
            </div>
        @endif
    </main>
</body>
</html>
