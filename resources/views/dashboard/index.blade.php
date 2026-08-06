@extends('layouts.app')

@section('title', __('messages.dashboard'))
@section('page-title', __('messages.dashboard'))

@section('content')
<div class="animate-in">
    {{-- رابط سريع لنقطة البيع --}}
    <div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,#1e1b4b,#312e81);border-radius:16px;overflow:hidden;">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-white mb-1"><i class="bi bi-display me-2"></i>نقطة البيع الموحدة</h5>
                <p class="mb-0" style="color:rgba(255,255,255,0.7);font-weight:600;font-size:0.88rem;">إدارة الأجهزة والكافيه والطلبات من شاشة واحدة</p>
            </div>
            <a href="{{ route('pos.index') }}" class="btn btn-light btn-lg fw-bold shadow-sm" style="border-radius:12px;padding:12px 28px;">
                <i class="bi bi-grid-3x3-gap-fill me-2 text-indigo-600"></i>فتح نقطة البيع
            </a>
        </div>
    </div>

    {{-- بطاقات الإحصائيات --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="background: var(--gradient-1);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">{{ __('messages.dashboard_stats.today_revenue') }}</div>
                        <div class="stat-value">{{ number_format($stats['today_revenue'], 2) }}</div>
                        <small class="fw-bold">{{ __('messages.currency') }}</small>
                    </div>
                    <i class="bi bi-cash-stack stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="background: var(--gradient-3);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">{{ __('messages.dashboard_stats.active_sessions') }}</div>
                        <div class="stat-value">{{ $stats['active_sessions']->count() }}</div>
                        <small class="fw-bold">جلسة نشطة</small>
                    </div>
                    <i class="bi bi-play-circle stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="background: var(--gradient-4);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">{{ __('messages.dashboard_stats.today_sessions') }}</div>
                        <div class="stat-value">{{ $stats['today_sessions'] }}</div>
                        <small class="fw-bold">جلسة اليوم</small>
                    </div>
                    <i class="bi bi-clock-history stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="background: var(--gradient-2);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">{{ __('messages.dashboard_stats.today_expenses') }}</div>
                        <div class="stat-value">{{ number_format($stats['today_expenses'], 2) }}</div>
                        <small class="fw-bold">{{ __('messages.currency') }}</small>
                    </div>
                    <i class="bi bi-wallet2 stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- صف ثاني من الإحصائيات --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="bi bi-controller text-indigo-600 mb-2" style="font-size:2.4rem;"></i>
                    <h6 class="text-slate-600 fw-bold mb-1">{{ __('messages.dashboard_stats.ps_revenue') }}</h6>
                    <h3 class="fw-black text-slate-900 mb-0">{{ number_format($stats['today_ps_revenue'], 2) }} <small class="fs-6 text-slate-500">{{ __('messages.currency') }}</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="bi bi-cup-hot text-amber-600 mb-2" style="font-size:2.4rem;"></i>
                    <h6 class="text-slate-600 fw-bold mb-1">{{ __('messages.dashboard_stats.cafe_revenue') }}</h6>
                    <h3 class="fw-black text-slate-900 mb-0">{{ number_format($stats['today_cafe_revenue'], 2) }} <small class="fs-6 text-slate-500">{{ __('messages.currency') }}</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="bi bi-graph-up-arrow text-emerald-600 mb-2" style="font-size:2.4rem;"></i>
                    <h6 class="text-slate-600 fw-bold mb-1">{{ __('messages.dashboard_stats.net_revenue') }}</h6>
                    <h3 class="fw-black mb-0 {{ $stats['net_revenue'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ number_format($stats['net_revenue'], 2) }} <small class="fs-6 text-slate-500">{{ __('messages.currency') }}</small>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- الجلسات النشطة --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-slate-900"><i class="bi bi-play-circle-fill me-2 text-emerald-600"></i>{{ __('messages.active_sessions') }}</span>
                    <a href="{{ route('sessions.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('messages.start_session') }}
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($stats['active_sessions']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.table.device') }}</th>
                                        <th>{{ __('messages.table.client') }}</th>
                                        <th>{{ __('messages.table.duration') }}</th>
                                        <th>{{ __('messages.table.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['active_sessions'] as $session)
                                    <tr>
                                        <td class="fw-bold text-slate-900">
                                            @if($session->device)
                                                <i class="bi bi-display me-1 text-indigo-600"></i>
                                                {{ $session->device->name }}
                                            @else
                                                <i class="bi bi-cup-hot me-1 text-amber-600"></i>
                                                {{ __('messages.cafe_only') }}
                                            @endif
                                        </td>
                                        <td>{{ $session->client_name ?? __('messages.guest') }}</td>
                                        <td><span class="badge badge-active fs-6"><i class="bi bi-stopwatch me-1"></i>{{ $session->elapsed_time_formatted }}</span></td>
                                        <td>
                                            <a href="{{ route('sessions.show', $session) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-pencil-square me-1"></i>تفاصيل / POS
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state py-4">
                            <i class="bi bi-controller text-slate-300"></i>
                            <p class="mb-0 fw-semibold text-slate-500">لا توجد جلسات بلايستيشن نشطة حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- تنبيهات المخزون --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white text-slate-900 fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2 text-amber-600"></i>
                    {{ __('messages.dashboard_stats.low_stock_alert') }}
                </div>
                <div class="card-body p-0">
                    @if($lowStockProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.table.name') }}</th>
                                        <th>{{ __('messages.table.stock') }}</th>
                                        <th>{{ __('messages.table.min_alert') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                    <tr>
                                        <td class="fw-bold text-slate-900">{{ $product->name }}</td>
                                        <td>
                                            <span class="badge {{ $product->stock_quantity == 0 ? 'badge-cancelled' : 'badge-maintenance' }}">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        </td>
                                        <td>{{ $product->min_stock_alert }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state py-4">
                            <i class="bi bi-check-circle text-emerald-500"></i>
                            <p class="mb-0 fw-semibold text-slate-600">جميع المنتجات متوفرة والمخزون جيدة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
