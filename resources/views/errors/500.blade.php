<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error | MetoHub</title>
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
        .error-content{flex:1;display:flex;align-items:center;justify-content:center;padding:80px 20px;text-align:center;}
        .error-inner{max-width:520px;}
        .error-code{font-family:var(--font-display);font-size:120px;font-weight:800;line-height:1;color:var(--green);margin-bottom:16px;letter-spacing:-4px;animation:float 6s ease-in-out infinite;}
        .error-title{font-family:var(--font-display);font-size:28px;font-weight:700;letter-spacing:-0.02em;margin-bottom:12px;}
        .error-divider{width:48px;height:3px;background:var(--yellow);margin:20px auto;border-radius:2px;}
        .error-desc{font-size:15px;color:var(--ink-soft);margin-bottom:36px;max-width:400px;margin-left:auto;margin-right:auto;}
        .error-btn{display:inline-flex;align-items:center;gap:10px;font-family:var(--font-body);font-weight:600;font-size:14.5px;padding:13px 28px;border-radius:9px;background:var(--ink);color:var(--paper);border:none;cursor:pointer;transition:background .15s ease, transform .15s ease;}
        .error-btn:hover{background:var(--green-deep);transform:translateY(-1px);}
        .error-btn svg{width:16px;height:16px;}
        @keyframes float{0%{transform:translateY(0);}50%{transform:translateY(-8px);}100%{transform:translateY(0);}}
        @media (max-width:920px){.error-code{font-size:90px;}}
        @media (max-width:480px){.error-code{font-size:72px;}.error-title{font-size:22px;}}
    </style>
</head>
<body>
    <div class="error-content">
        <div class="error-inner">
            <div class="error-code">500</div>
            <h1 class="error-title">Internal Server Error</h1>
            <div class="error-divider"></div>
            <p class="error-desc">Something went wrong on our end. Please try refreshing the page or come back in a moment.</p>
            <button onclick="history.back()" class="error-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Go Back
            </button>
        </div>
    </div>
</body>
</html>
