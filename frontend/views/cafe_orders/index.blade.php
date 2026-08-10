@extends('layouts.app')

@section('title', 'سجل طلبات الكافيه والطاولات')
@section('page-title', 'سجل طلبات الكافيه والطاولات')

@section('content')
<div class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-slate-900 mb-0"><i class="bi bi-receipt-cutoff me-2 text-indigo-600"></i>سجل طلبات الكافيه والطاولات</h4>
        <a href="{{ route('cafe-orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>طلب جديد
        </a>
    </div>

    {{-- الفلاتر --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="text-slate-600 fw-bold small">الحالة:</span>
                <a href="{{ route('cafe-orders.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">الكل</a>
                <a href="{{ route('cafe-orders.index', ['status' => 'open']) }}" class="btn btn-sm {{ request('status') == 'open' ? 'btn-primary' : 'btn-outline-secondary' }}">مفتوحة</a>
                <a href="{{ route('cafe-orders.index', ['status' => 'completed']) }}" class="btn btn-sm {{ request('status') == 'completed' ? 'btn-primary' : 'btn-outline-secondary' }}">مكتملة</a>
                <a href="{{ route('cafe-orders.index', ['status' => 'cancelled']) }}" class="btn btn-sm {{ request('status') == 'cancelled' ? 'btn-primary' : 'btn-outline-secondary' }}">ملغاة</a>

                <span class="text-slate-600 fw-bold small ms-3">نوع الطلب:</span>
                <a href="{{ route('cafe-orders.index', ['order_type' => 'table']) }}" class="btn btn-sm {{ request('order_type') == 'table' ? 'btn-warning' : 'btn-outline-secondary' }}">طاولة</a>
                <a href="{{ route('cafe-orders.index', ['order_type' => 'takeaway']) }}" class="btn btn-sm {{ request('order_type') == 'takeaway' ? 'btn-info' : 'btn-outline-secondary' }}">تيك أواي</a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نوع الطلب</th>
                            <th>رقم الطاولة / العميل</th>
                            <th>عدد الأصناف</th>
                            <th>المبلغ النهائي</th>
                            <th>طريقة الدفع</th>
                            <th>الحالة</th>
                            <th>الكاشير</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="fw-bold text-slate-500">#{{ $order->id }}</td>
                            <td>
                                <span class="badge {{ $order->order_type === 'table' ? 'badge-maintenance' : 'badge-active' }} fs-6">
                                    {{ $order->order_type_name }}
                                </span>
                            </td>
                            <td class="fw-bold text-slate-900">{{ $order->table_number ?? $order->client_name ?? 'زائر' }}</td>
                            <td class="fw-semibold text-slate-700">{{ $order->items->count() }} صنف</td>
                            <td><strong class="text-indigo-600 fs-6">{{ number_format($order->final_amount, 2) }} {{ __('messages.currency') }}</strong></td>
                            <td class="fw-semibold text-slate-700">{{ $order->payment_method_name }}</td>
                            <td><span class="badge badge-{{ $order->status }} fs-6">{{ $order->status_name }}</span></td>
                            <td class="fw-semibold text-slate-700">{{ $order->user->name }}</td>
                            <td class="text-slate-600">{{ $order->created_at->format('Y/m/d h:i A') }}</td>
                            <td>
                                @if($order->isOpen())
                                    <a href="{{ route('cafe-orders.show', $order) }}" class="btn btn-outline-secondary btn-sm" title="تعديل / POS"><i class="bi bi-pencil-square"></i></a>
                                @elseif($order->status === 'completed')
                                    <a href="{{ route('cafe-orders.invoice', $order) }}" class="btn btn-outline-secondary btn-sm" title="الفاتورة"><i class="bi bi-receipt"></i></a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10">
                                <div class="empty-state py-4">
                                    <i class="bi bi-receipt text-slate-300"></i>
                                    <p class="text-slate-500">لا توجد طلبات كافيه مطابقة</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">{{ $orders->links() }}</div>
</div>
@endsection
