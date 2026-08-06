@extends('layouts.app')
@section('title', __('messages.active_sessions'))
@section('page-title', __('messages.active_sessions'))

@section('content')
<div class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-slate-900"><i class="bi bi-controller me-2 text-indigo-600"></i>الجلسات النشطة والأجهزة</h4>
            <span class="text-slate-600 fw-semibold">متابعة وقت اللعب وحساب الأجهزة النشطة في الصالة</span>
        </div>
        <a href="{{ route('sessions.create') }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="bi bi-plus-circle me-1"></i>{{ __('messages.start_session') }}
        </a>
    </div>

    @if($activeSessions->count() > 0)
    <div class="row g-4">
        @foreach($activeSessions as $session)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-2 border-emerald-400 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                @if($session->device)
                                    <h5 class="mb-1 fw-bold text-slate-900">
                                        <i class="bi bi-display me-2 text-indigo-600"></i>
                                        {{ $session->device->name }}
                                    </h5>
                                    <span class="badge bg-slate-100 text-slate-700 border">
                                        {{ $session->device->type_name }} ({{ number_format($session->device->hourly_rate, 2) }} {{ __('messages.currency') }}/ساعة)
                                    </span>
                                @else
                                    <h5 class="mb-1 fw-bold text-slate-900">
                                        <i class="bi bi-cup-hot me-2 text-amber-600"></i>
                                        {{ __('messages.cafe_only') }}
                                    </h5>
                                @endif
                            </div>
                            <span class="badge badge-active fs-6">{{ $session->session_type_name }}</span>
                        </div>

                        <div class="rounded-3 p-3 mb-3" style="background:#f1f5f9; border: 1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-slate-600 fw-semibold">{{ __('messages.table.client') }}:</span>
                                <span class="fw-bold text-slate-900">{{ $session->client_name ?? __('messages.guest') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-slate-600 fw-semibold">{{ __('messages.table.duration') }}:</span>
                                <span class="fw-bold text-emerald-600 fs-6">
                                    <i class="bi bi-stopwatch me-1"></i>{{ $session->elapsed_time_formatted }}
                                </span>
                            </div>
                            @if($session->device)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-slate-600 fw-semibold">تكلفة الوقت الحالية:</span>
                                <span class="fw-bold text-indigo-600 fs-6">
                                    {{ number_format($session->calculatePlaystationCost(), 2) }} {{ __('messages.currency') }}
                                </span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between">
                                <span class="text-slate-600 fw-semibold">{{ __('messages.invoice.cafe_orders') }}:</span>
                                <span class="fw-bold text-slate-900">{{ $session->items->count() }} صنف ({{ number_format($session->items->sum('total_price'), 2) }} ج.م)</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <a href="{{ route('sessions.show', $session) }}" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-pencil-square me-1"></i>{{ __('messages.details') }} والطلبات
                        </a>
                        <a href="{{ route('sessions.checkout', $session) }}" class="btn btn-success flex-grow-1">
                            <i class="bi bi-cash-stack me-1"></i>{{ __('messages.checkout') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card border-1">
        <div class="card-body py-5 text-center">
            <div class="empty-state">
                <i class="bi bi-controller text-slate-400" style="font-size: 4rem;"></i>
                <h4 class="fw-bold text-slate-800 mt-2">لا توجد جلسات بلايستيشن نشطة</h4>
                <p class="text-slate-500">ابدأ جلسة جديدة على أي جهاز متوفر في الصالة</p>
                <a href="{{ route('sessions.create') }}" class="btn btn-primary btn-lg mt-2">
                    <i class="bi bi-plus-circle me-1"></i>بدء جلسة جديدة
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
setInterval(() => location.reload(), 60000);
</script>
@endpush
@endsection
