@extends('layouts.app')
@section('title', __('messages.checkout'))
@section('page-title', __('messages.checkout') . ' - جلسة #' . $session->id)

@section('content')
<div class="animate-in">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white text-slate-900 fw-bold fs-5"><i class="bi bi-cash-stack me-2 text-emerald-600"></i>{{ __('messages.checkout') }} وإصدار الفاتورة</div>
                <div class="card-body p-4">
                    {{-- ملخص --}}
                    <div class="rounded-3 p-4 mb-4" style="background:#f1f5f9; border: 1px solid #cbd5e1;">
                        @if($session->device)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-slate-700 fw-bold">{{ __('messages.invoice.play_cost') }} ({{ $session->elapsed_time_formatted }}):</span>
                            <strong class="text-indigo-600 fs-6">{{ number_format($session->calculatePlaystationCost(), 2) }} {{ __('messages.currency') }}</strong>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-slate-700 fw-bold">{{ __('messages.invoice.cafe_orders') }} ({{ $session->items->count() }} صنف):</span>
                            <strong class="text-amber-700 fs-6">{{ number_format($session->items->sum('total_price'), 2) }} {{ __('messages.currency') }}</strong>
                        </div>
                        <hr style="border-color:#cbd5e1;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-black text-slate-900">{{ __('messages.invoice.subtotal') }}:</span>
                            <span class="fs-4 fw-black text-indigo-600">
                                {{ number_format($session->calculatePlaystationCost() + $session->items->sum('total_price'), 2) }} {{ __('messages.currency') }}
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('sessions.close', $session) }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-slate-900 fw-bold">{{ __('messages.invoice.discount') }} ({{ __('messages.currency') }})</label>
                                <input type="number" name="discount" class="form-control form-control-lg" value="0" min="0" step="0.5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-slate-900 fw-bold">{{ __('messages.invoice.payment_method') }}</label>
                                <select name="payment_method" class="form-select form-select-lg" required>
                                    @foreach(__('messages.payment_methods') as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-lg flex-grow-1 fw-bold shadow-sm">
                                <i class="bi bi-check-circle me-1"></i>{{ __('messages.close_session') }} وإصدار الفاتورة
                            </button>
                            <a href="{{ route('sessions.show', $session) }}" class="btn btn-outline-secondary btn-lg">رجوع</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
