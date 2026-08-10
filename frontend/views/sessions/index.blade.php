@extends('layouts.app')
@section('title', __('messages.sessions'))
@section('page-title', __('messages.sessions'))

@section('content')
<div class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-slate-900 mb-0"><i class="bi bi-clock-history me-2 text-indigo-600"></i>أرشيف جلسات البلايستيشن</h4>
        <a href="{{ route('sessions.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>{{ __('messages.start_session') }}</a>
    </div>

    {{-- فلاتر --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="d-flex gap-2">
                <a href="{{ route('sessions.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">{{ __('messages.all') }}</a>
                <a href="{{ route('sessions.index', ['status' => 'active']) }}" class="btn btn-sm {{ request('status') == 'active' ? 'btn-primary' : 'btn-outline-secondary' }}">{{ __('messages.session_status.active') }}</a>
                <a href="{{ route('sessions.index', ['status' => 'closed']) }}" class="btn btn-sm {{ request('status') == 'closed' ? 'btn-primary' : 'btn-outline-secondary' }}">{{ __('messages.session_status.closed') }}</a>
                <a href="{{ route('sessions.index', ['status' => 'cancelled']) }}" class="btn btn-sm {{ request('status') == 'cancelled' ? 'btn-primary' : 'btn-outline-secondary' }}">{{ __('messages.session_status.cancelled') }}</a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('messages.table.id') }}</th>
                            <th>{{ __('messages.table.device') }}</th>
                            <th>{{ __('messages.table.client') }}</th>
                            <th>{{ __('messages.table.start_time') }}</th>
                            <th>{{ __('messages.table.duration') }}</th>
                            <th>{{ __('messages.table.total') }}</th>
                            <th>{{ __('messages.table.status') }}</th>
                            <th>{{ __('messages.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr>
                            <td class="fw-bold text-slate-500">#{{ $session->id }}</td>
                            <td class="fw-bold text-slate-900">{{ $session->device?->name ?? __('messages.cafe_only') }}</td>
                            <td class="fw-semibold text-slate-700">{{ $session->client_name ?? __('messages.guest') }}</td>
                            <td class="text-slate-600">{{ $session->start_time->format('h:i A') }}</td>
                            <td class="fw-semibold text-emerald-700">{{ $session->elapsed_time_formatted }}</td>
                            <td><strong class="text-indigo-600 fs-6">{{ number_format($session->final_amount, 2) }} {{ __('messages.currency') }}</strong></td>
                            <td><span class="badge badge-{{ $session->status }} fs-6">{{ $session->status_name }}</span></td>
                            <td>
                                @if($session->isActive())
                                    <a href="{{ route('sessions.show', $session) }}" class="btn btn-outline-secondary btn-sm" title="عرض التفاصيل"><i class="bi bi-pencil-square"></i></a>
                                @elseif($session->status === 'closed')
                                    <a href="{{ route('sessions.invoice', $session) }}" class="btn btn-outline-secondary btn-sm" title="الفاتورة"><i class="bi bi-receipt"></i></a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8"><div class="empty-state py-4"><i class="bi bi-clock text-slate-300"></i><p class="text-slate-500">{{ __('messages.no_results') }}</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">{{ $sessions->links() }}</div>
</div>
@endsection
