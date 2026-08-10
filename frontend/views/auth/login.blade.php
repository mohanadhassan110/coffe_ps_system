<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.login') }} - {{ __('messages.app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { font-family: 'Cairo', sans-serif; }
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 20px;
            padding: 42px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }
        .login-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 2.2rem;
            color: #fff;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.3);
        }
        h3 { color: #0f172a; font-weight: 900; }
        .subtitle { color: #475569; font-size: 0.95rem; font-weight: 600; }
        .form-control {
            background: #ffffff;
            border: 2px solid #cbd5e1;
            color: #0f172a;
            border-radius: 10px;
            padding: 12px 16px;
            font-weight: 600;
        }
        .form-control:focus {
            background: #ffffff;
            border-color: #4f46e5;
            color: #0f172a;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        }
        .form-label { color: #1e293b; font-weight: 700; font-size: 0.9rem; }
        .input-group-text {
            background: #f1f5f9;
            border: 2px solid #cbd5e1;
            color: #475569;
            font-size: 1.1rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            border: none;
            color: #fff;
            font-weight: 800;
            padding: 12px;
            border-radius: 12px;
            width: 100%;
            font-size: 1.05rem;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.45);
            color: #fff;
        }
        .alert-danger {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
            border-radius: 12px;
            font-weight: 600;
        }
        .form-check-label { color: #334155; font-size: 0.9rem; font-weight: 600; }
        .form-check-input:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-icon">
            <i class="bi bi-controller"></i>
        </div>
        <h3 class="text-center mb-1">{{ __('messages.app_name') }}</h3>
        <p class="subtitle text-center mb-4">نظام إدارة صالة البلايستيشن والكافيه</p>

        @if($errors->any())
            <div class="alert alert-danger mb-4">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">اسم المستخدم</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:0 10px 10px 0;">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required autofocus
                           style="border-radius:10px 0 0 10px;" placeholder="أدخل اسم المستخدم">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius:0 10px 10px 0;">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control" required
                           style="border-radius:10px 0 0 10px;" placeholder="أدخل كلمة المرور">
                </div>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">تذكرني</label>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('messages.login') }}
            </button>
        </form>
    </div>
</body>
</html>
