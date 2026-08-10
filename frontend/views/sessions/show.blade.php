@extends('layouts.app')
@section('title', __('messages.details') . ' - جلسة #' . $session->id)
@section('page-title', 'نقطة البيع - جلسة #' . $session->id)

@section('content')
<div class="animate-in">
    <div class="row g-4">
        {{-- سلة الطلبات وملخص الفاتورة --}}
        <div class="col-lg-5 col-xl-4">
            <div class="card mb-4 border-2 border-primary">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-controller text-indigo-600 me-2"></i>تفاصيل الجلسة الحالية</span>
                    <span class="badge badge-active fs-6">{{ $session->session_type_name }}</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-slate-600 fw-bold">{{ __('messages.table.device') }}:</span>
                        <strong class="text-slate-900 fs-6">{{ $session->device?->name ?? __('messages.cafe_only') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-slate-600 fw-bold">{{ __('messages.table.client') }}:</span>
                        <strong class="text-slate-900">{{ $session->client_name ?? __('messages.guest') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-slate-600 fw-bold">{{ __('messages.table.start_time') }}:</span>
                        <strong class="text-slate-900">{{ $session->start_time->format('h:i A') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-slate-600 fw-bold">{{ __('messages.table.duration') }}:</span>
                        <strong class="text-emerald-700 fs-6"><i class="bi bi-stopwatch me-1"></i>{{ $session->elapsed_time_formatted }}</strong>
                    </div>

                    @if($session->device)
                    <div class="d-flex justify-content-between mb-2 p-2 rounded bg-indigo-50 border border-indigo-100">
                        <span class="text-indigo-900 fw-bold">تكلفة الوقت الحالية:</span>
                        <strong class="text-indigo-700 fs-6">{{ number_format($session->calculatePlaystationCost(), 2) }} {{ __('messages.currency') }}</strong>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between mb-2 p-2 rounded bg-amber-50 border border-amber-100">
                        <span class="text-amber-900 fw-bold">إجمالي طلبات الكافيه:</span>
                        <strong class="text-amber-700 fs-6">{{ number_format($session->cafe_total, 2) }} {{ __('messages.currency') }}</strong>
                    </div>
                </div>
            </div>

            {{-- قائمة أصناف الكافيه المضافة --}}
            <div class="card mb-4">
                <div class="card-header bg-white"><i class="bi bi-receipt me-2 text-indigo-600"></i>الأصناف المطلوبة بالسلة</div>
                <div class="card-body p-0">
                    @if($session->items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>الصنف</th>
                                    <th class="text-center">الكمية</th>
                                    <th>الإجمالي</th>
                                    @if($session->isActive())<th></th>@endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($session->items as $item)
                                <tr>
                                    <td class="fw-bold text-slate-900">{{ $item->product->name }}</td>
                                    <td class="text-center"><span class="badge bg-slate-200 text-slate-800 fs-6">{{ $item->quantity }}</span></td>
                                    <td class="fw-bold text-indigo-600">{{ number_format($item->total_price, 2) }}</td>
                                    @if($session->isActive())
                                    <td class="text-end">
                                        <form action="{{ route('sessions.removeItem', [$session, $item->id]) }}" method="POST" onsubmit="return confirm('إزالة هذا الصنف من السلة؟')">
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
                        <i class="bi bi-cart3 text-slate-300"></i>
                        <p class="mb-0 fw-semibold text-slate-900">لم يتم إضافة أصناف كافيه للسلة بعد</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($session->isActive())
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('sessions.checkout', $session) }}" class="btn btn-success btn-lg shadow-sm w-100 fw-bold">
                    <i class="bi bi-cash-stack me-2"></i>إتمام الدفع وتحصيل الفاتورة
                </a>
                <form action="{{ route('sessions.cancel', $session) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_cancel_session') }}')">
                    @csrf
                    <button class="btn btn-outline-danger w-100 fw-semibold"><i class="bi bi-x-circle me-1"></i>{{ __('messages.cancel_session') }}</button>
                </form>
            </div>
            @endif
        </div>

        {{-- شاشة لمس نقطة البيع للمأكولات والمشروبات (POS Touch Grid) --}}
        <div class="col-lg-7 col-xl-8">
            @if($session->isActive())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-slate-900 mb-0"><i class="bi bi-grid-3x3-gap-fill text-indigo-600 me-2"></i>قائمة الكافيه والمنتجات</h5>
                        <small class="text-slate-900 fw-semibold">اضغط على أي صنف لإضافته مباشرة للسلة</small>
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
                                <form action="{{ route('sessions.addItem', $session) }}" method="POST" class="h-100">
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
