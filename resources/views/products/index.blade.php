@extends('layouts.app')
@section('title', __('messages.products'))
@section('page-title', __('messages.products'))

@section('content')
<div class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-slate-900 mb-0"><i class="bi bi-box-seam-fill me-2 text-indigo-600"></i>قائمة منتجات الكافيه والمخزون</h4>
        <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>{{ __('messages.add_new') }} منتج</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('messages.table.id') }}</th>
                            <th>{{ __('messages.table.name') }}</th>
                            <th>{{ __('messages.table.category') }}</th>
                            <th>{{ __('messages.table.purchase_price') }}</th>
                            <th>{{ __('messages.table.sale_price') }}</th>
                            <th>{{ __('messages.table.stock') }}</th>
                            <th>{{ __('messages.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="fw-bold text-slate-500">#{{ $product->id }}</td>
                            <td><strong class="text-slate-900 fs-6">{{ $product->name }}</strong></td>
                            <td><span class="badge" style="background:#312e81;color:#ffffff;font-size:0.85rem;font-weight:800;padding:6px 12px;border-radius:12px;">{{ $product->category->name }}</span></td>
                            <td class="text-slate-600 fw-semibold">{{ number_format($product->purchase_price, 2) }} ج.م</td>
                            <td class="fw-bold text-indigo-600 fs-6">{{ number_format($product->sale_price, 2) }} ج.م</td>
                            <td>
                                <span class="badge {{ $product->isLowStock() ? 'badge-cancelled' : 'badge-active' }} fs-6">
                                    {{ $product->stock_quantity }} قطعة
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-secondary btn-sm" title="تعديل"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" title="حذف"><i class="bi bi-trash"></i></button></form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7"><div class="empty-state py-4"><i class="bi bi-box-seam text-slate-300"></i><p class="text-slate-500">{{ __('messages.no_results') }}</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
