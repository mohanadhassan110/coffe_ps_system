@extends('layouts.app')
@section('title', __('messages.expenses'))
@section('page-title', __('messages.expenses'))

@section('content')
<div class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-slate-900 mb-0"><i class="bi bi-wallet2 me-2 text-rose-600"></i>سجل المصروفات اليومية</h4>
        <div class="d-flex gap-2 align-items-center">
            <form class="d-flex gap-2">
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}">
                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-search"></i></button>
            </form>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>مصروف جديد</a>
        </div>
    </div>

    <div class="card mb-4 border-2 border-rose-400 shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center py-3">
            <span class="fw-bold text-slate-900 fs-6">إجمالي مصروفات يوم ({{ $date }})</span>
            <span class="fs-4 fw-black text-rose-600">{{ number_format($totalExpenses, 2) }} {{ __('messages.currency') }}</span>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('messages.table.id') }}</th>
                            <th>{{ __('messages.table.reason') }}</th>
                            <th>{{ __('messages.table.amount') }}</th>
                            <th>{{ __('messages.table.cashier') }}</th>
                            <th>{{ __('messages.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td class="fw-bold text-slate-500">#{{ $expense->id }}</td>
                            <td class="fw-bold text-slate-900">{{ $expense->reason }}</td>
                            <td><strong class="text-rose-600 fs-6">{{ number_format($expense->amount, 2) }} {{ __('messages.currency') }}</strong></td>
                            <td class="fw-semibold text-slate-700">{{ $expense->user->name }}</td>
                            <td>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="حذف"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5"><div class="empty-state py-4"><i class="bi bi-wallet text-slate-300"></i><p class="text-slate-500">{{ __('messages.no_results') }}</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
