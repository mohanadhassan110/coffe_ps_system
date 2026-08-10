@extends('layouts.app')
@section('title', 'تفاصيل الطلب #' . $order->id)
@section('page-title', 'نقطة البيع - ' . $order->order_type_name)

@section('content')
<div class="animate-in">
    <div class="row g-4">
        {{-- سلة طلب الكافيه وملخص الفاتورة --}}
        <div class="col-lg-5 col-xl-4">
            <div class="card mb-4 border-2 {{ $order->order_type === 'table' ? 'border-amber-400' : 'border-sky-400' }}">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span>
                        @if($order->order_type === 'table')
                            <i class="bi bi-table me-2 text-amber-600"></i>
                        @else
                            <i class="bi bi-bag-check me-2 text-sky-600"></i>
                        @endif
                        {{ $order->order_type_name }}
                    </span>
                    <span class="badge {{ $order->order_type === 'table' ? 'badge-maintenance' : 'badge-active' }}">
                        {{ $order->order_type === 'table' ? 'طاولة' : 'تيك أواي' }}
                    </span>
                </div>
                <div class="card-body">
                    @if($order->table_number)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-slate-600 fw-bold">رقم الطاولة:</span>
                        <strong class="text-slate-900 fs-6">{{ $order->table_number }}</strong>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-slate-600 fw-bold">اسم العميل:</span>
                        <strong class="text-slate-900">{{ $order->client_name ?? 'زائر' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-slate-600 fw-bold">الكاشير المسؤول:</span>
                        <strong class="text-slate-900">{{ $order->user->name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-slate-600 fw-bold">وقت الطلب:</span>
                        <strong class="text-slate-900">{{ $order->created_at->format('h:i A') }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2 p-2 rounded bg-indigo-50 border border-indigo-100">
                        <span class="text-indigo-900 fw-bold">الإجمالي الحالي:</span>
                        <strong class="text-indigo-700 fs-5">{{ number_format($order->items->sum('total_price'), 2) }} {{ __('messages.currency') }}</strong>
                    </div>
                </div>
            </div>

            {{-- الأصناف المطلوبة في السلة --}}
            <div class="card mb-4">
                <div class="card-header bg-white"><i class="bi bi-receipt me-2 text-indigo-600"></i>أصناف الطلب للسفرة / التيك أواي</div>
                <div class="card-body p-0">
                    @if($order->items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>الصنف</th>
                                    <th class="text-center">الكمية</th>
                                    <th>الإجمالي</th>
                                    @if($order->isOpen())<th></th>@endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="fw-bold text-slate-900">{{ $item->product->name }}</td>
                                    <td class="text-center"><span class="badge bg-slate-200 text-slate-800 fs-6">{{ $item->quantity }}</span></td>
                                    <td class="fw-bold text-indigo-600">{{ number_format($item->total_price, 2) }}</td>
                                    @if($order->isOpen())
                                    <td class="text-end">
                                        <form action="{{ route('cafe-orders.removeItem', [$order, $item->id]) }}" method="POST" onsubmit="return confirm('إزالة هذا الصنف من الطلب؟')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm rounded-circle p-1" style="width:30px;height:30px;"><i class="bi bi-x"></i></button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state py-4">
                        <i class="bi bi-cup text-slate-300"></i>
                        <p class="mb-0 fw-semibold text-slate-900">لم يتم إضافة أصناف لهذا الطلب بعد</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($order->isOpen())
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('cafe-orders.checkout', $order) }}" class="btn btn-success btn-lg shadow-sm w-100 fw-bold">
                    <i class="bi bi-cash-stack me-2"></i>تحصيل وإغلاق الطلب
                </a>
                <form action="{{ route('cafe-orders.cancel', $order) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الطلب وإعادة العناصر للمخزون؟')">
                    @csrf
                    <button class="btn btn-outline-danger w-100 fw-semibold"><i class="bi bi-x-circle me-1"></i>إلغاء الطلب</button>
                </form>
            </div>
            @endif
        </div>

        {{-- شاشة لمس نقطة البيع للمأكولات والمشروبات (POS Touch Grid) --}}
        <div class="col-lg-7 col-xl-8">
            @if($order->isOpen())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-slate-900 mb-0"><i class="bi bi-grid-3x3-gap-fill text-indigo-600 me-2"></i>قائمة المشروبات والمأكولات</h5>
                        <small class="text-slate-900 fw-semibold">اضغط على أي صنف لإضافته مباشرة للطلب</small>
                    </div>

                    {{-- شريط التصفية السريعة للتصنيفات --}}
                    <div class="d-flex gap-2 overflow-x-auto pb-2 scrollbar-none">
                        <button class="pos-category-btn active" data-category="all">
                            <i class="bi bi-grid-fill me-1"></i>كل التصنيفات
                        </button>
                        @foreach($categories as $cat)
                        <button class="pos-category-btn" data-category="cat-{{ $cat->id }}">
                            <i class="bi bi-tag-fill me-1"></i>{{ $cat->name }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="card-body bg-slate-50 p-4">
                    {{-- شبكة بطاقات المنتجات --}}
                    <div class="row g-3" id="posProductGrid">
                        @foreach($categories as $cat)
                            @foreach($cat->products as $product)
                            <div class="col-6 col-md-4 col-xl-3 pos-product-item cat-{{ $cat->id }}">
                                <form action="{{ route('cafe-orders.addItem', $order) }}" method="POST" class="h-100">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    
                                    <button type="submit" class="pos-product-card w-100 border-0 text-start" title="إضافة للطلب">
                                        <div>
                                            <div class="pos-product-name text-slate-900">{{ $product->name }}</div>
                                            <span class="badge bg-slate-100 text-slate-600 border mb-2">
                                                المخزون: {{ $product->stock_quantity }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="pos-product-price">{{ number_format($product->sale_price, 2) }} ج.م</span>
                                            <span class="btn btn-sm btn-primary rounded-circle p-1" style="width:32px;height:32px;">
                                                <i class="bi bi-plus-lg"></i>
                                            </span>
                                        </div>
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.pos-category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.pos-category-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const cat = this.getAttribute('data-category');
        document.querySelectorAll('.pos-product-item').forEach(item => {
            if (cat === 'all' || item.classList.contains(cat)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
@endsection
