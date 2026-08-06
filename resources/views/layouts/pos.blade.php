<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'نقطة البيع') - {{ __('messages.app_name') }}</title>

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
            --pos-primary: #4f46e5;
            --pos-primary-light: #e0e7ff;
            --pos-success: #10b981;
            --pos-success-light: #d1fae5;
            --pos-danger: #ef4444;
            --pos-danger-light: #fee2e2;
            --pos-warning: #f59e0b;
            --pos-warning-light: #fef3c7;
            --pos-blue: #3b82f6;
            --pos-blue-light: #dbeafe;
            --pos-surface: #ffffff;
            --pos-body: #f1f5f9;
            --pos-border: #e2e8f0;
            --pos-text: #0f172a;
            --pos-text-secondary: #475569;
            --pos-text-muted: #94a3b8;
        }

        * {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            background: var(--pos-body);
            color: var(--pos-text);
        }

        /* ======================== */
        /* POS Layout Structure     */
        /* ======================== */
        .pos-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Top Header Bar */
        .pos-header {
            height: 56px;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
            z-index: 100;
        }

        .pos-header .pos-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .pos-header .pos-brand i {
            font-size: 1.4rem;
            color: #a5b4fc;
        }

        .pos-header .pos-nav {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pos-header .pos-nav a,
        .pos-header .pos-nav button {
            color: rgba(255,255,255,0.75);
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 6px 14px;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .pos-header .pos-nav a:hover,
        .pos-header .pos-nav button:hover {
            color: #fff;
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.25);
        }

        .pos-header .pos-clock {
            display: flex;
            align-items: center;
            gap: 14px;
            font-weight: 700;
            font-size: 0.88rem;
        }

        .pos-header .pos-clock .live-time {
            background: rgba(255,255,255,0.1);
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            font-weight: 800;
            color: #a5b4fc;
        }

        .pos-header .pos-stats-bar {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .pos-header .pos-mini-stat {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            color: rgba(255,255,255,0.85);
        }

        .pos-header .pos-mini-stat .stat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Three-panel body */
        .pos-body {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* ======================== */
        /* Panel 1: Workspace Grid  */
        /* ======================== */
        .pos-panel-workspace {
            width: 42%;
            display: flex;
            flex-direction: column;
            background: var(--pos-body);
            border-left: 1px solid var(--pos-border);
            overflow: hidden;
        }

        .pos-panel-header {
            padding: 12px 16px;
            background: var(--pos-surface);
            border-bottom: 1px solid var(--pos-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .pos-panel-header h6 {
            margin: 0;
            font-weight: 800;
            font-size: 0.92rem;
            color: var(--pos-text);
        }

        .pos-tab-switcher {
            display: flex;
            gap: 4px;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 10px;
        }

        .pos-tab-btn {
            padding: 7px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.82rem;
            border: none;
            background: transparent;
            color: var(--pos-text-secondary);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .pos-tab-btn:hover {
            color: var(--pos-text);
            background: rgba(255,255,255,0.6);
        }

        .pos-tab-btn.active {
            background: var(--pos-surface);
            color: var(--pos-primary);
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        .pos-panel-body {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }

        /* Device / Table Cards */
        .pos-device-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
            gap: 10px;
        }

        .pos-device-card {
            background: var(--pos-surface);
            border: 2px solid var(--pos-border);
            border-radius: 14px;
            padding: 14px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
        }

        .pos-device-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }

        .pos-device-card.available {
            border-color: #a7f3d0;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        }

        .pos-device-card.occupied {
            border-color: #93c5fd;
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        }

        .pos-device-card.occupied.selected {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15), 0 6px 18px rgba(79, 70, 229, 0.12);
            transform: translateY(-2px);
        }

        .pos-device-card.maintenance {
            border-color: #fde68a;
            background: linear-gradient(135deg, #fefce8 0%, #ffffff 100%);
            opacity: 0.6;
            cursor: default;
        }

        .pos-device-card .device-name {
            font-weight: 800;
            font-size: 0.92rem;
            color: var(--pos-text);
        }

        .pos-device-card .device-type {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--pos-text-muted);
        }

        .pos-device-card .device-status-dot {
            position: absolute;
            top: 12px;
            left: 12px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .pos-device-card.available .device-status-dot { background: var(--pos-success); box-shadow: 0 0 6px rgba(16,185,129,0.5); }
        .pos-device-card.occupied .device-status-dot { background: var(--pos-blue); box-shadow: 0 0 6px rgba(59,130,246,0.5); animation: pulse-dot 2s infinite; }
        .pos-device-card.maintenance .device-status-dot { background: var(--pos-warning); }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .pos-device-card .device-timer {
            font-weight: 900;
            font-size: 1.05rem;
            color: var(--pos-blue);
        }

        .pos-device-card .device-cost {
            font-weight: 800;
            font-size: 0.88rem;
            color: var(--pos-primary);
            background: var(--pos-primary-light);
            padding: 2px 10px;
            border-radius: 6px;
            display: inline-block;
        }

        .pos-device-card .device-action-btn {
            width: 100%;
            padding: 7px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            transition: all 0.15s;
        }

        .pos-device-card .btn-start-session {
            background: var(--pos-success);
            color: #fff;
        }

        .pos-device-card .btn-start-session:hover {
            background: #059669;
        }

        /* Café table card */
        .pos-cafe-card {
            background: var(--pos-surface);
            border: 2px solid var(--pos-border);
            border-radius: 14px;
            padding: 14px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .pos-cafe-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }

        .pos-cafe-card.active-order {
            border-color: #fdba74;
            background: linear-gradient(135deg, #fff7ed 0%, #ffffff 100%);
        }

        .pos-cafe-card.active-order.selected {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15), 0 6px 18px rgba(79, 70, 229, 0.12);
        }

        .pos-cafe-card.empty-table {
            border-color: #d1d5db;
            border-style: dashed;
            background: #fafafa;
            align-items: center;
            justify-content: center;
            min-height: 100px;
            color: var(--pos-text-muted);
        }

        .pos-cafe-card.empty-table:hover {
            border-color: var(--pos-success);
            color: var(--pos-success);
        }

        /* ======================== */
        /* Panel 2: Product Menu    */
        /* ======================== */
        .pos-panel-products {
            width: 33%;
            display: flex;
            flex-direction: column;
            background: var(--pos-surface);
            border-left: 1px solid var(--pos-border);
            overflow: hidden;
        }

        .pos-category-tabs {
            display: flex;
            gap: 4px;
            padding: 10px 12px;
            overflow-x: auto;
            flex-shrink: 0;
            background: #f8fafc;
            border-bottom: 1px solid var(--pos-border);
        }

        .pos-category-tabs::-webkit-scrollbar { height: 0; }

        .pos-cat-pill {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.78rem;
            border: 1.5px solid var(--pos-border);
            background: var(--pos-surface);
            color: var(--pos-text-secondary);
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .pos-cat-pill:hover {
            border-color: var(--pos-primary);
            color: var(--pos-primary);
        }

        .pos-cat-pill.active {
            background: var(--pos-primary);
            color: #fff;
            border-color: var(--pos-primary);
        }

        .pos-products-grid {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 8px;
            align-content: start;
        }

        .pos-prod-card {
            background: var(--pos-surface);
            border: 1.5px solid var(--pos-border);
            border-radius: 12px;
            padding: 12px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100px;
        }

        .pos-prod-card:hover {
            border-color: var(--pos-primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        }

        .pos-prod-card:active {
            transform: scale(0.96);
        }

        .pos-prod-card.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pos-prod-card .prod-name {
            font-weight: 800;
            font-size: 0.82rem;
            color: var(--pos-text);
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .pos-prod-card .prod-stock {
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--pos-text-muted);
        }

        .pos-prod-card .prod-price {
            font-weight: 900;
            font-size: 0.88rem;
            color: var(--pos-primary);
            background: var(--pos-primary-light);
            padding: 3px 8px;
            border-radius: 6px;
            margin-top: 6px;
        }

        .pos-no-selection-overlay {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--pos-text-muted);
            text-align: center;
            padding: 20px;
        }

        .pos-no-selection-overlay i {
            font-size: 3rem;
            margin-bottom: 12px;
            opacity: 0.3;
        }

        .pos-no-selection-overlay p {
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* ======================== */
        /* Panel 3: Cart / Bill     */
        /* ======================== */
        .pos-panel-cart {
            width: 25%;
            display: flex;
            flex-direction: column;
            background: var(--pos-surface);
            overflow: hidden;
        }

        .pos-cart-header {
            padding: 14px 16px;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #fff;
            flex-shrink: 0;
        }

        .pos-cart-header h6 {
            margin: 0;
            font-weight: 800;
            font-size: 0.95rem;
        }

        .pos-cart-header .cart-entity-info {
            font-size: 0.78rem;
            font-weight: 600;
            opacity: 0.85;
            margin-top: 4px;
        }

        .pos-cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 0;
        }

        .pos-cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
        }

        .pos-cart-item:hover {
            background: #fafbfc;
        }

        .pos-cart-item .item-info {
            flex: 1;
        }

        .pos-cart-item .item-name {
            font-weight: 700;
            color: var(--pos-text);
        }

        .pos-cart-item .item-qty {
            font-size: 0.75rem;
            color: var(--pos-text-muted);
            font-weight: 600;
        }

        .pos-cart-item .item-total {
            font-weight: 800;
            color: var(--pos-primary);
            font-size: 0.88rem;
            margin-left: 10px;
        }

        .pos-cart-item .item-remove {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: none;
            background: var(--pos-danger-light);
            color: var(--pos-danger);
            font-size: 0.75rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            transition: all 0.15s;
        }

        .pos-cart-item .item-remove:hover {
            background: var(--pos-danger);
            color: #fff;
        }

        .pos-cart-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--pos-text-muted);
            text-align: center;
            padding: 20px;
        }

        .pos-cart-empty i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.25;
        }

        .pos-cart-footer {
            flex-shrink: 0;
            border-top: 2px solid var(--pos-border);
            background: #fafbfc;
            padding: 12px 14px;
        }

        .pos-cart-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--pos-text-secondary);
        }

        .pos-cart-total-row.grand-total {
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--pos-text);
            padding-top: 8px;
            border-top: 2px dashed var(--pos-border);
            margin-top: 6px;
        }

        .pos-cart-total-row.grand-total .total-value {
            color: var(--pos-primary);
        }

        .pos-cart-actions {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 10px;
        }

        .pos-btn-checkout {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.92rem;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .pos-btn-checkout.primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .pos-btn-checkout.primary:hover {
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);
            transform: translateY(-1px);
        }

        .pos-btn-checkout.danger {
            background: transparent;
            border: 1.5px solid var(--pos-danger-light);
            color: var(--pos-danger);
            font-size: 0.82rem;
        }

        .pos-btn-checkout.danger:hover {
            background: var(--pos-danger-light);
        }

        /* ======================== */
        /* Modal Styles             */
        /* ======================== */
        .pos-modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.4);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .pos-modal-backdrop.show {
            display: flex;
        }

        .pos-modal {
            background: var(--pos-surface);
            border-radius: 18px;
            padding: 28px;
            width: 90%;
            max-width: 440px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            animation: modalIn 0.2s ease;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .pos-modal h5 {
            font-weight: 800;
            margin-bottom: 20px;
            color: var(--pos-text);
            font-size: 1.1rem;
        }

        .pos-modal .form-label {
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--pos-text-secondary);
            margin-bottom: 6px;
        }

        .pos-modal .form-control,
        .pos-modal .form-select {
            border: 2px solid var(--pos-border);
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--pos-text);
        }

        .pos-modal .form-control:focus,
        .pos-modal .form-select:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .pos-modal .modal-actions {
            display: flex;
            gap: 8px;
            margin-top: 20px;
        }

        .pos-modal .modal-actions button,
        .pos-modal .modal-actions a {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.88rem;
            text-align: center;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .pos-modal .btn-modal-confirm {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
        }

        .pos-modal .btn-modal-cancel {
            background: #f1f5f9;
            color: var(--pos-text-secondary);
        }

        /* ======================== */
        /* Alert Toasts             */
        /* ======================== */
        .pos-toast {
            position: fixed;
            top: 70px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.88rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
            animation: toastIn 0.3s ease, toastOut 0.3s ease 3s forwards;
        }

        .pos-toast.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .pos-toast.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @keyframes toastIn {
            from { opacity: 0; transform: translateX(-50%) translateY(-10px); }
            to { opacity: 1; transform: translateX(-50%) translateY(0); }
        }

        @keyframes toastOut {
            from { opacity: 1; }
            to { opacity: 0; display: none; }
        }

        /* Scrollbar styling */
        .pos-panel-body::-webkit-scrollbar,
        .pos-products-grid::-webkit-scrollbar,
        .pos-cart-items::-webkit-scrollbar {
            width: 4px;
        }

        .pos-panel-body::-webkit-scrollbar-track,
        .pos-products-grid::-webkit-scrollbar-track,
        .pos-cart-items::-webkit-scrollbar-track {
            background: transparent;
        }

        .pos-panel-body::-webkit-scrollbar-thumb,
        .pos-products-grid::-webkit-scrollbar-thumb,
        .pos-cart-items::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Flash alerts --}}
    @if(session('success'))
        <div class="pos-toast success"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="pos-toast error"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="pos-toast error"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}</div>
    @endif

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
