@extends('layouts.app')
@section('title', __('messages.add_new') . ' - ' . __('messages.devices'))
@section('page-title', __('messages.add_new') . ' ' . __('messages.devices'))

@section('content')
<div class="animate-in">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white text-slate-900 fw-bold fs-5">
                    <i class="bi bi-plus-circle me-2 text-indigo-600"></i>{{ __('messages.add_new') }} جهاز
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('devices.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-slate-900 fw-bold">{{ __('messages.table.name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="مثال: بلايستيشن 5 - 01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-slate-900 fw-bold">{{ __('messages.table.type') }}</label>
                            <select name="type" class="form-select" required>
                                @foreach(__('messages.device_types') as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-slate-900 fw-bold">{{ __('messages.table.hourly_rate') }} ({{ __('messages.currency') }})</label>
                            <input type="number" name="hourly_rate" class="form-control" value="{{ old('hourly_rate') }}" step="0.50" min="0" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-bold shadow-sm"><i class="bi bi-check-lg me-1"></i>{{ __('messages.save') }}</button>
                            <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary btn-lg">{{ __('messages.back') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
