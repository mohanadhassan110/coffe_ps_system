@extends('layouts.app')
@section('title', __('messages.add_new') . ' - ' . __('messages.expenses'))
@section('page-title', 'إضافة مصروف جديد')

@section('content')
<div class="animate-in">
    <div class="row justify-content-center"><div class="col-lg-5">
        <div class="card border-0 shadow-sm"><div class="card-header bg-white text-slate-900 fw-bold fs-5"><i class="bi bi-plus-circle me-2 text-rose-600"></i>مصروف جديد</div>
            <div class="card-body p-4">
                <form action="{{ route('expenses.store') }}" method="POST">@csrf
                    <div class="mb-3"><label class="form-label text-slate-900 fw-bold">{{ __('messages.table.amount') }} ({{ __('messages.currency') }})</label>
                        <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" step="0.01" min="0.01" required></div>
                    <div class="mb-3"><label class="form-label text-slate-900 fw-bold">{{ __('messages.table.reason') }}</label>
                        <input type="text" name="reason" class="form-control" value="{{ old('reason') }}" placeholder="مثال: فاتورة كهرباء" required></div>
                    <div class="mb-4"><label class="form-label text-slate-900 fw-bold">{{ __('messages.table.date') }}</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required></div>
                    <div class="d-flex gap-2"><button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-bold shadow-sm"><i class="bi bi-check-lg me-1"></i>{{ __('messages.save') }}</button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-lg">{{ __('messages.back') }}</a></div>
                </form>
            </div>
        </div>
    </div></div>
</div>
@endsection
