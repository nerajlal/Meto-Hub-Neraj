<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Greengrocer SuperAdmin - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0b0f19; color: #f3f4f6; }
        .card { background-color: #111827; border: 1px solid #1f2937; }
        .form-control { background-color: #1f2937; border: 1px solid #374151; color: #fff; }
        .form-control:focus { background-color: #1f2937; border-color: #10b981; color: #fff; box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25); }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="w-100 p-4" style="max-width: 448px;">
        <div class="card shadow-lg p-4 p-md-5 rounded-4">
            <div class="d-flex justify-content-center mb-4">
                <div class="bg-success text-white d-flex align-items-center justify-content-center rounded-3 fw-bold fs-4" style="width: 48px; height: 48px;">SA</div>
            </div>
            <h2 class="h4 fw-bold text-center text-white mb-2">SuperAdmin Portal</h2>
            <p class="text-muted small text-center mb-4">Log in to manage tenants and system settings.</p>
            
            <form method="POST" action="{{ route('super_admin.login.submit') }}">
                @csrf
                
                @if ($errors->any())
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger px-3 py-2 small mb-4">
                        <ul class="list-unstyled mb-0 list-disc ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="email" class="form-label fw-medium small text-muted mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="form-control">
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-medium small text-muted mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="form-control">
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input id="remember_me" type="checkbox" name="remember" class="form-check-input" style="background-color: #1f2937; border-color: #374151;">
                        <label for="remember_me" class="form-check-label small text-muted">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 fw-semibold py-2 rounded-3">
                    Access Portal
                </button>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
