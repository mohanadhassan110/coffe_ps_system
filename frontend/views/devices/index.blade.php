@extends('layouts.app')
@section('title', __('messages.devices'))
@section('page-title', __('messages.devices'))

@section('content')
<div class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-slate-900 mb-0"><i class="bi bi-display me-2 text-indigo-600"></i>{{ __('messages.devices') }}</h4>
        <a href="{{ route('devices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>{{ __('messages.add_new') }} جهاز
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('messages.table.id') }}</th>
                            <th>{{ __('messages.table.name') }}</th>
                            <th>{{ __('messages.table.type') }}</th>
                            <th>{{ __('messages.table.hourly_rate') }}</th>
                            <th>{{ __('messages.table.status') }}</th>
                            <th>{{ __('messages.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                        <tr>
                            <td class="fw-bold text-slate-500">#{{ $device->id }}</td>
                            <td><strong class="text-slate-900 fs-6">{{ $device->name }}</strong></td>
                            <td class="fw-semibold text-slate-700">{{ $device->type_name }}</td>
                            <td class="fw-bold text-indigo-600">{{ number_format($device->hourly_rate, 2) }} {{ __('messages.currency') }}</td>
                            <td><span class="badge badge-{{ $device->status }} fs-6">{{ $device->status_name }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('devices.edit', $device) }}" class="btn btn-outline-secondary btn-sm" title="تعديل"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('devices.destroy', $device) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="حذف"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6"><div class="empty-state py-4"><i class="bi bi-display text-slate-300"></i><p class="text-slate-500">{{ __('messages.no_results') }}</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
