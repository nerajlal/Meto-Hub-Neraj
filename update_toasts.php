<?php

$files = [
    'resources/views/template_1/layouts/app.blade.php',
    'resources/views/template_2/layouts/app.blade.php',
    'resources/views/template_3/layouts/app.blade.php',
];

$toastScript = <<<SCRIPT
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const t = document.createElement('div');
            t.textContent = "{{ session('error') }}";
            t.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);background:#EF4444;color:#fff;padding:12px 24px;border-radius:8px;font-weight:600;z-index:99999;box-shadow:0 4px 12px rgba(0,0,0,0.15);transition:opacity 0.3s;';
            document.body.appendChild(t);
            setTimeout(() => { t.style.opacity='0'; setTimeout(()=>t.remove(), 300); }, 5000);
        });
    </script>
    @endif
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const t = document.createElement('div');
            t.textContent = "{{ session('success') }}";
            t.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);background:#10B981;color:#fff;padding:12px 24px;border-radius:8px;font-weight:600;z-index:99999;box-shadow:0 4px 12px rgba(0,0,0,0.15);transition:opacity 0.3s;';
            document.body.appendChild(t);
            setTimeout(() => { t.style.opacity='0'; setTimeout(()=>t.remove(), 300); }, 5000);
        });
    </script>
    @endif
SCRIPT;

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Remove Toastify script blocks if they exist
        $content = preg_replace('/@if\(session\(\'error\'\)\)\s*<script>[\s\S]*?<\/script>\s*@endif/m', '', $content);
        $content = preg_replace('/@if\(session\(\'success\'\)\)\s*<script>[\s\S]*?<\/script>\s*@endif/m', '', $content);
        
        // Find the right place to insert
        $insertStr = "<script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>";
        if (strpos($content, $insertStr) !== false) {
            $content = str_replace($insertStr, $insertStr . "\n" . $toastScript, $content);
        } else {
            // fallback
            $content = str_replace("</body>", $toastScript . "\n</body>", $content);
        }
        
        file_put_contents($file, $content);
    }
}
echo "Done\n";
