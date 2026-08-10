@extends('layouts.app')

@section('title', 'فتح طلب كافيه جديد')
@section('page-title', 'فتح طلب كافيه جديد')

@section('content')
<div class="animate-in">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white text-slate-900 fw-bold fs-5">
                    <i class="bi bi-plus-circle me-2 text-indigo-600"></i>فتح طلب كافيه جديد (طاولة / تيك أواي)
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('cafe-orders.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-slate-900 fw-bold">نوع الطلب</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="order_type" value="table" id="typeTable" {{ old('order_type', 'table') == 'table' ? 'checked' : '' }}>
                                    <label class="form-check-label text-slate-900 fw-bold" for="typeTable">
                                        <i class="bi bi-table me-1 text-amber-600"></i>طاولة في الصالة
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="order_type" value="takeaway" id="typeTakeaway" {{ old('order_type') == 'takeaway' ? 'checked' : '' }}>
                                    <label class="form-check-label text-slate-900 fw-bold" for="typeTakeaway">
                                        <i class="bi bi-bag-check me-1 text-sky-600"></i>سفري / تيك أواي
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="tableNumberGroup">
                            <label class="form-label text-slate-900 fw-bold">رقم / اسم الطاولة</label>
                            <input type="text" name="table_number" class="form-control form-control-lg" value="{{ old('table_number') }}" placeholder="مثال: طاولة 1، طاولة VIP 2">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-slate-900 fw-bold">اسم العميل (اختياري)</label>
                            <input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" placeholder="اسم العميل أو صاحب الطلب">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-bold shadow-sm">
                                <i class="bi bi-plus-lg me-1"></i>فتح الطلب وبدء إضافة المنتجات
                            </button>
                            <a href="{{ route('cafe-orders.active') }}" class="btn btn-outline-secondary btn-lg">رجوع</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('input[name="order_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('tableNumberGroup').style.display =
            this.value === 'table' ? 'block' : 'none';
    });
});
if(document.querySelector('input[name="order_type"]:checked')?.value === 'takeaway') {
    document.getElementById('tableNumberGroup').style.display = 'none';
}
</script>
@endpush
@endsection
