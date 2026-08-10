@extends('layouts.app')
@section('title', __('messages.add_new') . ' - ' . __('messages.products'))
@section('page-title', __('messages.add_new') . ' منتج')

@section('content')
<div class="animate-in">
    <div class="row justify-content-center"><div class="col-lg-7">
        <div class="card border-0 shadow-sm"><div class="card-header bg-white text-slate-900 fw-bold fs-5"><i class="bi bi-plus-circle me-2 text-indigo-600"></i>منتج جديد</div>
            <div class="card-body p-4">
                <form action="{{ route('products.store') }}" method="POST">@csrf
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label text-slate-900 fw-bold">{{ __('messages.table.name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="مثال: نسكافيه" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label text-slate-900 fw-bold">{{ __('messages.table.category') }}</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">{{ __('messages.select_category') }}</option>
                                @foreach($categories as $cat)<option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>@endforeach
                            </select></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label text-slate-900 fw-bold">{{ __('messages.table.purchase_price') }} (ج.م)</label>
                            <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price') }}" step="0.01" min="0" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label text-slate-900 fw-bold">{{ __('messages.table.sale_price') }} (ج.م)</label>
                            <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price') }}" step="0.01" min="0" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label text-slate-900 fw-bold">{{ __('messages.table.stock') }}</label>
                            <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 0) }}" min="0" required></div>
                        <div class="col-md-6 mb-4"><label class="form-label text-slate-900 fw-bold">{{ __('messages.table.min_alert') }}</label>
                            <input type="number" name="min_stock_alert" class="form-control" value="{{ old('min_stock_alert', 5) }}" min="0" required></div>
                    </div>
                    <div class="d-flex gap-2"><button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-bold shadow-sm"><i class="bi bi-check-lg me-1"></i>{{ __('messages.save') }}</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-lg">{{ __('messages.back') }}</a></div>
                </form>
            </div>
        </div>
    </div></div>
</div>
@endsection
