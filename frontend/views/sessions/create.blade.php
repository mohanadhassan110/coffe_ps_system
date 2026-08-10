@extends('layouts.app')
@section('title', __('messages.start_session'))
@section('page-title', __('messages.start_session'))

@section('content')
<div class="animate-in">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white text-slate-900 fw-bold fs-5"><i class="bi bi-play-circle me-2 text-emerald-600"></i>{{ __('messages.start_session') }}</div>
                <div class="card-body p-4">
                    <form action="{{ route('sessions.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-slate-900 fw-bold">{{ __('messages.select_device') }}</label>
                            <select name="device_id" class="form-select form-select-lg" id="deviceSelect">
                                <option value="">{{ __('messages.cafe_only') }}</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}" data-rate="{{ $device->hourly_rate }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                        {{ $device->name }} ({{ $device->type_name }} - {{ number_format($device->hourly_rate, 2) }} {{ __('messages.currency') }}/{{ __('messages.hour') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-slate-900 fw-bold">{{ __('messages.table.client') }} (اختياري)</label>
                            <input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" placeholder="اسم العميل">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-slate-900 fw-bold">نوع الجلسة</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="session_type" value="open" id="typeOpen" {{ old('session_type', 'open') == 'open' ? 'checked' : '' }}>
                                    <label class="form-check-label text-slate-800 fw-bold" for="typeOpen">{{ __('messages.session_types.open') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="session_type" value="pre_paid" id="typePrepaid" {{ old('session_type') == 'pre_paid' ? 'checked' : '' }}>
                                    <label class="form-check-label text-slate-800 fw-bold" for="typePrepaid">{{ __('messages.session_types.pre_paid') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4" id="prePaidMinutesGroup" style="display:none;">
                            <label class="form-label text-slate-900 fw-bold">عدد الدقائق</label>
                            <select name="pre_paid_minutes" class="form-select">
                                <option value="30">30 {{ __('messages.minute') }}</option>
                                <option value="60">60 {{ __('messages.minute') }} ({{ __('messages.hour') }})</option>
                                <option value="90">90 {{ __('messages.minute') }}</option>
                                <option value="120">120 {{ __('messages.minute') }} (ساعتين)</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-lg flex-grow-1 fw-bold shadow-sm">
                                <i class="bi bi-play-fill me-1"></i>{{ __('messages.start_session') }}
                            </button>
                            <a href="{{ route('sessions.active') }}" class="btn btn-outline-secondary btn-lg">رجوع</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('input[name="session_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('prePaidMinutesGroup').style.display =
            this.value === 'pre_paid' ? 'block' : 'none';
    });
});
if(document.querySelector('input[name="session_type"]:checked')?.value === 'pre_paid') {
    document.getElementById('prePaidMinutesGroup').style.display = 'block';
}
</script>
@endpush
@endsection
