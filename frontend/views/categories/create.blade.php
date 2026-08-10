@extends('layouts.app')
@section('title', __('messages.add_new') . ' - ' . __('messages.categories'))
@section('page-title', __('messages.add_new') . ' تصنيف')

@section('content')
<div class="animate-in">
    <div class="row justify-content-center"><div class="col-lg-5">
        <div class="card border-0 shadow-sm"><div class="card-header bg-white text-slate-900 fw-bold fs-5"><i class="bi bi-plus-circle me-2 text-indigo-600"></i>تصنيف جديد</div>
            <div class="card-body p-4">
                <form action="{{ route('categories.store') }}" method="POST">@csrf
                    <div class="mb-4"><label class="form-label text-slate-900 fw-bold">{{ __('messages.table.name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="مثال: مشروبات ساخنة" required></div>
                    <div class="d-flex gap-2"><button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-bold shadow-sm"><i class="bi bi-check-lg me-1"></i>{{ __('messages.save') }}</button>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-lg">{{ __('messages.back') }}</a></div>
                </form>
            </div>
        </div>
    </div></div>
</div>
@endsection
