@extends('layouts.app')

@section('title', 'إدارة صالة الكافيه والطاولات')
@section('page-title', 'صالة الكافيه والطلبات الخارجية')

@section('content')
<div class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-slate-900"><i class="bi bi-cup-hot-fill me-2 text-amber-600"></i>صالة الكافيه والطلبات الخارجية</h4>
            <span class="text-slate-600 fw-semibold">متابعة الطاولات الحالية وطلبات التيك أواي المستقلة</span>
        </div>
        <a href="{{ route('cafe-orders.create') }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="bi bi-plus-circle me-1"></i>فتح طلب جديد (طاولة / تيك أواي)
        </a>
    </div>

    @if($openOrders->count() > 0)
    <div class="row g-4">
        @foreach($openOrders as $order)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-2 {{ $order->order_type === 'table' ? 'border-amber-400' : 'border-sky-400' }} shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1 fw-bold text-slate-900">
                                    @if($order->order_type === 'table')
                                        <i class="bi bi-table me-2 text-amber-600"></i>
                                        {{ $order->order_type_name }}
                                    @else
                                        <i class="bi bi-bag-check me-2 text-sky-600"></i>
                                        {{ $order->order_type_name }}
                                    @endif
                                </h5>
                                <small class="text-slate-500 fw-semibold">
                                    الكاشير المسؤول: {{ $order->user->name }}
                                </small>
                            </div>
                            <span class="badge {{ $order->order_type === 'table' ? 'badge-maintenance' : 'badge-active' }} fs-6">
                                {{ $order->order_type === 'table' ? 'طاولة' : 'تيك أواي' }}
                            </span>
                        </div>

                        <div class="rounded-3 p-3 mb-3" style="background:#f1f5f9; border:1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-slate-600 fw-semibold">العميل:</span>
                                <span class="fw-bold text-slate-900">{{ $order->client_name ?? 'زائر' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-slate-600 fw-semibold">عدد الأصناف:</span>
                                <span class="fw-bold text-slate-900">{{ $order->items->count() }} صنف</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-slate-600 fw-semibold">الإجمالي الحالي:</span>
                                <span class="fw-bold text-indigo-600 fs-6">
                                    {{ number_format($order->items->sum('total_price'), 2) }} {{ __('messages.currency') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <a href="{{ route('cafe-orders.show', $order) }}" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-pencil-square me-1"></i>تعديل / POS
                        </a>
                        <a href="{{ route('cafe-orders.checkout', $order) }}" class="btn btn-success flex-grow-1">
                            <i class="bi bi-cash-stack me-1"></i>تحصيل وإغلاق
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
            <div class="empty-state py-3">
                <i class="bi bi-cup-hot text-slate-400" style="font-size: 4rem;"></i>
                <h4 class="fw-bold text-slate-800 mt-2">لا توجد طلبات كافيه مفتوحة حالياً</h4>
                <p class="text-slate-500">انقر على زر فتح طلب جديد لفتح طاولة في الصالة أو طلب سفري جديد</p>
                <a href="{{ route('cafe-orders.create') }}" class="btn btn-primary btn-lg mt-2">
                    <i class="bi bi-plus-circle me-1"></i>فتح طلب جديد
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
