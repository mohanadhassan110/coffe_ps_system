@extends('layouts.app')

@section('title', 'تحصيل وتأكيد الطلب #' . $order->id)
@section('page-title', 'تحصيل ' . $order->order_type_name)

@section('content')
<div class="animate-in">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white text-slate-900 fw-bold fs-5"><i class="bi bi-cash-stack me-2 text-emerald-600"></i>دفع وتحصيل الطلب</div>
                <div class="card-body p-4">
                    {{-- ملخص الحساب --}}
                    <div class="rounded-3 p-4 mb-4" style="background:#f1f5f9; border: 1px solid #cbd5e1;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-slate-700 fw-bold">نوع الطلب:</span>
                            <strong class="text-slate-900 fs-6">{{ $order->order_type_name }}</strong>
                        </div>
                        @if($order->table_number)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-slate-700 fw-bold">رقم الطاولة:</span>
                            <strong class="text-slate-900 fs-6">{{ $order->table_number }}</strong>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-slate-700 fw-bold">عدد الأصناف:</span>
                            <strong class="text-slate-900">{{ $order->items->count() }} صنف</strong>
                        </div>
                        <hr style="border-color:#cbd5e1;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-black text-slate-900">إجمالي الطلب:</span>
                            <span class="fs-4 fw-black text-indigo-600">
                                {{ number_format($order->items->sum('total_price'), 2) }} {{ __('messages.currency') }}
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('cafe-orders.close', $order) }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-slate-900 fw-bold">مبلغ الخصم ({{ __('messages.currency') }})</label>
                                <input type="number" name="discount" class="form-control form-control-lg" value="0" min="0" step="0.5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-slate-900 fw-bold">طريقة الدفع</label>
                                <select name="payment_method" class="form-select form-select-lg" required>
                                    @foreach(__('messages.payment_methods') as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-lg flex-grow-1 fw-bold shadow-sm">
                                <i class="bi bi-check-circle me-1"></i>إغلاق الطلب وإصدار الفاتورة
                            </button>
                            <a href="{{ route('cafe-orders.show', $order) }}" class="btn btn-outline-secondary btn-lg">رجوع</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
