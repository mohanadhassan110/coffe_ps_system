<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.app_name')) - {{ __('messages.app_name') }}</title>

    {{-- Google Font: Cairo --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 RTL --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #e11d48;
            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;
            --info: #0284c7;
            --body-bg: #f8fafc;
            --sidebar-bg: #0f172a;
            --card-bg: #ffffff;
            --card-hover: #f1f5f9;
            --darker: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --gradient-1: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            --gradient-2: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
            --gradient-3: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --gradient-4: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        }

        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: var(--body-bg);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* ======================== */
        /* الشريط الجانبي (Sidebar) */
        /* ======================== */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 260px;
            height: 100vh;
            background: var(--sidebar-bg);
            border-left: 1px solid #1e293b;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #1e293b;
            background: var(--gradient-1);
        }

        .sidebar-brand h4 {
            color: #fff;
            margin: 0;
            font-weight: 800;
            font-size: 1.3rem;
        }

        .sidebar-brand small {
            color: rgba(255,255,255,0.8);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .sidebar-nav {
            padding: 15px 0;
        }

        .sidebar-nav .nav-section {
            padding: 10px 20px 6px;
            font-size: 0.72rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
            margin: 2px 10px;
            border-radius: 10px;
        }

        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav .nav-link.active {
            color: #fff;
            background: var(--gradient-1);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        /* ======================== */
        /* المحتوى الرئيسي         */
        /* ======================== */
        .main-content {
            margin-right: 260px;
            min-height: 100vh;
        }

        /* شريط علوي */
        .top-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .top-bar .page-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        .top-bar .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #334155;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .top-bar .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--gradient-1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: 0.95rem;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
        }

        .content-area {
            padding: 28px;
        }

        /* ======================== */
        /* البطاقات (Cards)         */
        /* ======================== */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
        }

        .card-header {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 22px;
            font-weight: 800;
            color: #0f172a;
            font-size: 1.05rem;
            border-radius: 16px 16px 0 0 !important;
        }

        .card-body {
            padding: 22px;
        }

        /* بطاقات الإحصائيات */
        .stat-card {
            border: none;
            border-radius: 16px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.12);
            border-radius: 50%;
        }

        .stat-card .stat-icon {
            font-size: 2.2rem;
            opacity: 0.9;
        }

        .stat-card .stat-value {
            font-size: 1.9rem;
            font-weight: 900;
            margin: 6px 0 2px;
        }

        .stat-card .stat-label {
            font-size: 0.88rem;
            opacity: 0.9;
            font-weight: 600;
        }

        /* ======================== */
        /* الجداول (Tables)         */
        /* ======================== */
        .table {
            color: #0f172a;
            vertical-align: middle;
        }

        .table thead th {
            background: #f1f5f9;
            color: #1e293b;
            border-bottom: 2px solid #cbd5e1;
            font-weight: 800;
            font-size: 0.88rem;
            padding: 14px 18px;
            white-space: nowrap;
        }

        .table tbody td {
            border-color: #e2e8f0;
            padding: 14px 18px;
            vertical-align: middle;
            font-size: 0.92rem;
            color: #1e293b;
            font-weight: 500;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        /* ======================== */
        /* الشارات (Badges)         */
        /* ======================== */
        .badge-available { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-occupied { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-maintenance { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-active { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-closed { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-cancelled { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.8rem;
        }

        /* ======================== */
        /* الأزرار (Buttons)        */
        /* ======================== */
        .btn-primary {
            background: var(--gradient-1);
            border: none;
            font-weight: 700;
            padding: 10px 22px;
            border-radius: 10px;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.2);
            color: #ffffff;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
            color: #ffffff;
        }

        .btn-success {
            background: var(--gradient-3);
            border: none;
            font-weight: 700;
            padding: 10px 22px;
            border-radius: 10px;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
        }

        .btn-success:hover {
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);
        }

        .btn-danger {
            background: var(--gradient-2);
            border: none;
            font-weight: 700;
            padding: 10px 22px;
            border-radius: 10px;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(244, 63, 94, 0.2);
        }

        .btn-danger:hover {
            color: #ffffff;
        }

        .btn-outline-light, .btn-outline-secondary {
            border: 2px solid #cbd5e1;
            color: #334155;
            background: #ffffff;
            font-weight: 700;
            border-radius: 10px;
        }

        .btn-outline-light:hover, .btn-outline-secondary:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #0f172a;
        }

        /* ======================== */
        /* النماذج (Forms)          */
        /* ======================== */
        .form-control, .form-select {
            background: #ffffff;
            border: 2px solid #cbd5e1;
            color: #0f172a;
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 600;
        }

        .form-control:focus, .form-select:focus {
            background: #ffffff;
            border-color: var(--primary);
            color: #0f172a;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        }

        .form-label {
            font-weight: 700;
            font-size: 0.88rem;
            color: #1e293b;
            margin-bottom: 8px;
        }

        /* ======================== */
        /* التنبيهات (Alerts)       */
        /* ======================== */
        .alert {
            border-radius: 12px;
            border: none;
            font-weight: 600;
            padding: 14px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }

        .alert-success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .alert-danger, .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .alert-warning {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .alert-info {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        /* ======================== */
        /* POS Tactile Touch UI    */
        /* ======================== */
        .pos-product-card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            user-select: none;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 120px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }

        .pos-product-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.15);
            background: #f8fafc;
        }

        .pos-product-card:active {
            transform: scale(0.97);
        }

        .pos-product-name {
            font-weight: 800;
            font-size: 1rem;
            color: #0f172a;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .pos-product-price {
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--primary);
            background: #e0e7ff;
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-block;
        }

        .pos-category-btn {
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.92rem;
            border: 2px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .pos-category-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #f8fafc;
        }

        .pos-category-btn.active {
            background: var(--gradient-1);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        /* الصفحة الفارغة */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 15px;
            opacity: 0.4;
        }

        .empty-state h5 {
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-right: 0;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeInUp 0.3s ease forwards;
        }

        .pagination .page-link {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #0f172a;
            font-weight: 700;
            border-radius: 8px !important;
        }
        .pagination .page-link:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }
        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- الشريط الجانبي --}}
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4><i class="bi bi-controller me-2"></i>{{ __('messages.app_name') }}</h4>
            <small>PlayStation Lounge & POS Café</small>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section">القائمة الرئيسية</div>

            <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" style="{{ request()->routeIs('pos.*') ? '' : 'background:rgba(79,70,229,0.15);color:#a5b4fc;' }}">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span>نقطة البيع</span>
            </a>

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>{{ __('messages.dashboard') }}</span>
            </a>

            <a href="{{ route('sessions.active') }}" class="nav-link {{ request()->routeIs('sessions.active') ? 'active' : '' }}">
                <i class="bi bi-controller"></i>
                <span>{{ __('messages.active_sessions') }}</span>
            </a>

            <a href="{{ route('sessions.index') }}" class="nav-link {{ request()->routeIs('sessions.index') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>{{ __('messages.sessions') }}</span>
            </a>

            <div class="nav-section mt-3">صالة الكافيه والطلبات</div>

            <a href="{{ route('cafe-orders.active') }}" class="nav-link {{ request()->routeIs('cafe-orders.active') ? 'active' : '' }}">
                <i class="bi bi-cup-hot-fill"></i>
                <span>طاولات وتيك أواي الكافيه</span>
            </a>

            <a href="{{ route('cafe-orders.index') }}" class="nav-link {{ request()->routeIs('cafe-orders.index') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i>
                <span>سجل طلبات الكافيه</span>
            </a>

            @if(auth()->user()->isAdmin())
            <div class="nav-section mt-3">الإدارة</div>

            <a href="{{ route('devices.index') }}" class="nav-link {{ request()->routeIs('devices.*') ? 'active' : '' }}">
                <i class="bi bi-display"></i>
                <span>{{ __('messages.devices') }}</span>
            </a>

            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i>
                <span>{{ __('messages.categories') }}</span>
            </a>

            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i>
                <span>{{ __('messages.products') }}</span>
            </a>
            @endif

            <div class="nav-section mt-3">المالية</div>

            <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>{{ __('messages.expenses') }}</span>
            </a>

            @if(auth()->user()->isAdmin())
            <a href="{{ route('reports.daily') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i>
                <span>{{ __('messages.reports') }}</span>
            </a>
            @endif
        </div>
    </nav>

    {{-- المحتوى الرئيسي --}}
    <div class="main-content">
        {{-- الشريط العلوي --}}
        <div class="top-bar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="page-title">@yield('page-title', __('messages.dashboard'))</h5>
            </div>
            <div class="user-info">
                <span>{{ __('messages.welcome') }}، {{ auth()->user()->name }}</span>
                <div class="user-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm ms-2" title="تسجيل الخروج">
                        <i class="bi bi-box-arrow-left"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- منطقة المحتوى --}}
        <div class="content-area">
            {{-- رسائل النجاح والخطأ --}}
            @if(session('success'))
                <div class="alert alert-success animate-in mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger animate-in mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger animate-in mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0 pe-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
