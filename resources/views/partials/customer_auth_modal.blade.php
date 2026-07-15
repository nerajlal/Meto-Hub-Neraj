<!-- Customer Auth Modal Overlay -->
<div id="customer-auth-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    
    <!-- Modal Container -->
    <div id="customer-auth-modal" style="background: #fff; width: 90%; max-width: 400px; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.2); position: relative; animation: slideUp 0.3s ease-out;">
        
        <!-- Close Button -->
        <button type="button" onclick="closeCustomerAuthModal()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 1.5rem; color: #9ca3af; cursor: pointer; z-index: 10;">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Header -->
        <div style="background: #f8fafc; padding: 1.5rem; text-align: center; border-bottom: 1px solid #e5e7eb;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--primary-color);">Welcome to {{ $currentTenant->name ?? 'Our Store' }}</h2>
            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">Login or create an account to track your orders.</p>
        </div>

        <!-- Tabs -->
        <div style="display: flex; border-bottom: 1px solid #e5e7eb;">
            <button id="tab-login" onclick="switchAuthTab('login')" style="flex: 1; padding: 12px; background: none; border: none; border-bottom: 3px solid var(--accent-color); font-weight: 700; color: var(--primary-color); cursor: pointer;">Login</button>
            <button id="tab-register" onclick="switchAuthTab('register')" style="flex: 1; padding: 12px; background: none; border: none; border-bottom: 3px solid transparent; font-weight: 600; color: #9ca3af; cursor: pointer;">Register</button>
        </div>

        <div style="padding: 1.5rem;">
            <!-- Alert Box -->
            <div id="auth-alert" style="display: none; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.85rem; font-weight: 500;"></div>

            <!-- Login Form -->
            <form id="form-login" onsubmit="handleCustomerAuth(event, 'login')">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #4b5563; margin-bottom: 5px;">Email Address</label>
                    <input type="email" name="email" required style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #4b5563; margin-bottom: 5px;">Password</label>
                    <input type="password" name="password" required style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem;">
                </div>
                <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                    <label style="display: flex; align-items: center; gap: 5px; cursor: pointer;">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                </div>
                <button type="submit" id="btn-login-submit" style="width: 100%; padding: 12px; background: var(--accent-color); color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: opacity 0.2s;">
                    Login
                </button>
            </form>

            <!-- Register Form -->
            <form id="form-register" style="display: none;" onsubmit="handleCustomerAuth(event, 'register')">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #4b5563; margin-bottom: 5px;">Full Name</label>
                    <input type="text" name="name" required style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #4b5563; margin-bottom: 5px;">Email Address</label>
                    <input type="email" name="email" required style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #4b5563; margin-bottom: 5px;">Password</label>
                    <input type="password" name="password" required minlength="8" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #4b5563; margin-bottom: 5px;">Confirm Password</label>
                    <input type="password" name="password_confirmation" required minlength="8" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem;">
                </div>
                <button type="submit" id="btn-register-submit" style="width: 100%; padding: 12px; background: var(--accent-color); color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: opacity 0.2s;">
                    Create Account
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

<script>
    function openCustomerAuthModal() {
        $('#customer-auth-overlay').css('display', 'flex');
        $('body').css('overflow', 'hidden');
    }

    function closeCustomerAuthModal() {
        $('#customer-auth-overlay').hide();
        $('body').css('overflow', '');
    }

    // Close on click outside
    $('#customer-auth-overlay').on('click', function(e) {
        if (e.target === this) {
            closeCustomerAuthModal();
        }
    });

    function switchAuthTab(tab) {
        if (tab === 'login') {
            $('#form-login').show();
            $('#form-register').hide();
            $('#tab-login').css({ 'border-bottom-color': 'var(--accent-color)', 'font-weight': '700', 'color': 'var(--primary-color)' });
            $('#tab-register').css({ 'border-bottom-color': 'transparent', 'font-weight': '600', 'color': '#9ca3af' });
        } else {
            $('#form-login').hide();
            $('#form-register').show();
            $('#tab-login').css({ 'border-bottom-color': 'transparent', 'font-weight': '600', 'color': '#9ca3af' });
            $('#tab-register').css({ 'border-bottom-color': 'var(--accent-color)', 'font-weight': '700', 'color': 'var(--primary-color)' });
        }
        $('#auth-alert').hide();
    }

    function handleCustomerAuth(e, type) {
        e.preventDefault();
        const form = $(e.target);
        const submitBtn = form.find('button[type="submit"]');
        const alertBox = $('#auth-alert');
        
        submitBtn.prop('disabled', true).css('opacity', '0.7');
        
        const url = type === 'login' ? '{{ route("customer.login") }}' : '{{ route("customer.register") }}';

        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            success: function(res) {
                if (res.success) {
                    alertBox.css({ 'background': '#d1fae5', 'color': '#065f46', 'display': 'block' }).text(res.message);
                    setTimeout(() => {
                        if (res.redirect) {
                            window.location.href = res.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, 1000);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).css('opacity', '1');
                let msg = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alertBox.css({ 'background': '#fee2e2', 'color': '#991b1b', 'display': 'block' }).text(msg);
            }
        });
    }
</script>
