<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - @yield('title', 'Dashboard')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>S</text></svg>">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f2f3; }
        .sidebar-item:hover { background-color: #f6f6f7; color: #202223; }
        .sidebar-item.active { background-color: #edeeef; color: #000; font-weight: 600; border-left: 3px solid #000; }
        .card { box-shadow: 0 0 5px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1); border: none; }
        
        .wrapper { display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 260px; flex-shrink: 0; overflow-y: auto; background: white; border-right: 1px solid #dee2e6; }
        .main-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .content-scroll { flex: 1; overflow-y: auto; padding: 1.5rem; }
        
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1050;
                width: 280px;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }
        }
    </style>
</head>
<body class="text-secondary">

    <div class="wrapper">
        <!-- Sidebar -->
        @include('super_admin.partials.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            @include('super_admin.partials.header')

            <!-- Scrollable Content -->
            <main class="content-scroll">
                <div class="container-fluid max-w-xxl p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @yield('content')

                    <div class="mt-5 pt-4 border-top text-center small text-muted">
                        Super Admin Panel &copy; {{ date('Y') }}
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
