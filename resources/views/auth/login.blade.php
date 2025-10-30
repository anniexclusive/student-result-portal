@extends('pages/layout')

@section('content')

<style>
    .auth-section {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
    }

    .auth-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
        background-size: cover;
    }

    .auth-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        max-width: 480px;
        width: 100%;
        animation: slideUp 0.5s ease-out;
        position: relative;
        z-index: 1;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .auth-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px 30px;
        text-align: center;
        color: white;
    }

    .auth-header h2 {
        margin: 0 0 10px 0;
        font-size: 32px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .auth-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 16px;
    }

    .auth-body {
        padding: 40px 30px;
    }

    .alert-modern {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        border: none;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-success-modern {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-danger-modern {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .alert-modern::before {
        content: '✓';
        font-weight: bold;
        font-size: 18px;
    }

    .alert-danger-modern::before {
        content: '⚠';
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #344055;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    .input-wrapper {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        font-size: 18px;
        pointer-events: none;
        transition: color 0.3s;
    }

    .form-control-modern {
        width: 100%;
        padding: 14px 15px 14px 45px;
        border: 2px solid #E5E7EB;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s;
        background: #F9FAFB;
        color: #344055;
    }

    .form-control-modern:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .form-control-modern:focus + .input-icon {
        color: #667eea;
    }

    .remember-wrapper {
        display: flex;
        align-items: center;
        margin: 20px 0;
    }

    .remember-wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 8px;
        cursor: pointer;
        accent-color: #667eea;
    }

    .remember-wrapper label {
        margin: 0;
        font-weight: 500;
        color: #6B7280;
        cursor: pointer;
        user-select: none;
    }

    .btn-login {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        letter-spacing: 0.5px;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .auth-footer {
        margin-top: 30px;
        text-align: center;
        padding-top: 25px;
        border-top: 1px solid #E5E7EB;
    }

    .auth-footer p {
        margin: 0;
        color: #6B7280;
        font-size: 15px;
    }

    .auth-footer a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s;
    }

    .auth-footer a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    @media (max-width: 576px) {
        .auth-card {
            margin: 20px;
        }

        .auth-header {
            padding: 30px 20px;
        }

        .auth-header h2 {
            font-size: 26px;
        }

        .auth-body {
            padding: 30px 20px;
        }
    }
</style>

<section class="auth-section">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Welcome Back</h2>
            <p>Login to access your student portal</p>
        </div>

        <div class="auth-body">
            @if (session('success'))
                <div class="alert-modern alert-success-modern">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-modern alert-danger-modern">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            <form id="login-form" method="post" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            class="form-control-modern"
                            value="{{ old('email') }}"
                            placeholder="Enter your email"
                            required
                            autofocus
                        >
                        <span class="input-icon">📧</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-control-modern"
                            placeholder="Enter your password"
                            required
                        >
                        <span class="input-icon">🔒</span>
                    </div>
                </div>

                <div class="remember-wrapper">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me for 30 days</label>
                </div>

                <button type="submit" class="btn-login">
                    Sign In
                </button>

                <div class="auth-footer">
                    <p>Don't have an account? <a href="{{ route('register') }}">Create one now</a></p>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection
