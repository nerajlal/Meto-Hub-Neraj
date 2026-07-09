<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentTenant->name ?? 'VESPR' }} Admin - @yield('title', 'Dashboard')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter for Shopify-like look -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
            background-color: #f6f6f7; 
            color: #202223;
            -webkit-font-smoothing: antialiased;
        }
        
        body, h1, h2, h3, h4, h5, h6, p, span, a, input, button, select, textarea, td, th {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        
        /* Shopify style variables */
        :root {
            --sf-bg: #f6f6f7;
            --sf-sidebar-bg: #f1f1f1; /* Shopify neutral light gray */
            --sf-card-bg: #ffffff;
            --sf-border: #e1e3e5;
            --sf-text: #202223;
            --sf-text-secondary: #6d7175;
            --sf-accent: #008060; /* Shopify Green */
            --sf-accent-hover: #006e52;
            --sf-btn-dark: #1a1a1a;
        }

        .shopify-green { color: var(--sf-accent); }
        .bg-shopify-green { background-color: var(--sf-accent); }
        .bg-shopify-green:hover { background-color: var(--sf-accent-hover); }
        
        /* Flat clean cards */
        .card { 
            background-color: var(--sf-card-bg);
            border: 1px solid var(--sf-border) !important;
            border-radius: 8px !important;
            box-shadow: none !important;
        }

        .card-header {
            background-color: transparent !important;
            border-bottom: 1px solid var(--sf-border) !important;
        }
        
        /* Layout overrides for Bootstrap to match previous layout */
        .wrapper { display: flex; height: calc(100vh - 48px); overflow: hidden; }
        .sidebar { 
            width: 240px; 
            flex-shrink: 0; 
            overflow-y: auto; 
            background-color: #f1f1f1 !important; /* Shopify neutral light gray */
            border-right: none !important; 
        }
        .main-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .content-scroll { flex: 1; overflow-y: auto; padding: 2rem 0; }
        
        .content-scroll .container-fluid {
            max-width: 1060px !important;
            margin: 0 auto !important;
            padding: 0 2rem !important;
        }
        
        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.1);
            border-radius: 3px;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1050;
                width: 240px;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }
        }

        /* Sidebar Item Shopify Redesign */
        .sidebar-item {
            margin: 2px 8px !important;
            padding: 8px 12px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #202223 !important;
            border-left: none !important;
            transition: background-color 0.15s ease, color 0.15s ease;
            border-radius: 6px !important;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .sidebar-item i {
            font-size: 16px !important;
            color: #6d7175 !important;
            width: 20px;
            text-align: center;
            margin-right: 12px;
            transition: color 0.15s ease;
        }

        .sidebar-item:hover {
            background-color: rgba(0, 0, 0, 0.05) !important;
            color: #202223 !important;
        }

        .sidebar-item:hover i {
            color: #202223 !important;
        }

        .sidebar-item.active {
            background-color: #ffffff !important;
            color: #202223 !important;
            font-weight: 600 !important;
            box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.08) !important;
        }

        .sidebar-item.active i {
            color: #202223 !important;
        }

        .sidebar-item .badge {
            background-color: rgba(0, 0, 0, 0.08) !important;
            color: #202223 !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            padding: 2px 8px !important;
            border-radius: 10px !important;
        }

        .sidebar-heading {
            padding: 16px 20px 4px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            color: #6d7175 !important;
        }
    </style>
</head>
<body class="text-secondary">

    <!-- Top Bar (Full Width) -->
    @include('admin.partials.header')

    <div class="wrapper">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <!-- Scrollable Content -->
            <main class="content-scroll">
                <div class="container-fluid max-w-xxl p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')

                    <div class="mt-5 pt-4 border-top text-center small text-muted">
                        @if(isset($currentTenant))
                            @php
                                $displayDomain = $currentTenant->domain ?? (strtolower(str_replace(' ', '', $currentTenant->name)) . '.vespr.com');
                            @endphp
                            vespr store is running on <a href="http://{{ $displayDomain }}" target="_blank" class="text-decoration-none fw-medium text-secondary">{{ $displayDomain }}</a>
                        @else
                            vespr store
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
