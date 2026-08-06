@extends('layouts.pos')

@section('title', 'نقطة البيع الموحدة')

@section('content')
<div class="pos-wrapper">
    {{-- ════════════════════════════════════ --}}
    {{-- الشريط العلوي (Header)              --}}
    {{-- ════════════════════════════════════ --}}
    <div class="pos-header">
        <div class="pos-brand">
            <i class="bi bi-controller"></i>
            <span>{{ __('messages.app_name') }}</span>
        </div>

        <div class="pos-stats-bar">
            <div class="pos-mini-stat">
                <span class="stat-dot" style="background:#10b981;"></span>
                إيرادات اليوم: <strong>{{ number_format($quickStats['today_revenue'], 2) }} ج.م</strong>
            </div>
            <div class="pos-mini-stat">
                <span class="stat-dot" style="background:#3b82f6;"></span>
                أجهزة نشطة: <strong>{{ $quickStats['active_sessions'] }}</strong>
            </div>
            <div class="pos-mini-stat">
                <span class="stat-dot" style="background:#f59e0b;"></span>
                طلبات كافيه: <strong>{{ $quickStats['open_cafe_orders'] }}</strong>
            </div>
        </div>

        <div class="pos-clock">
            <span class="live-time" id="liveClock">--:--:--</span>
            <div class="pos-nav">
                <a href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2"></i> لوحة التحكم</a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('reports.daily') }}"><i class="bi bi-bar-chart"></i> التقارير</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit"><i class="bi bi-box-arrow-left"></i> خروج</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════ --}}
    {{-- الجسم الثلاثي (Three Panels)        --}}
    {{-- ════════════════════════════════════ --}}
    <div class="pos-body">

        {{-- ═══════════════════════════════ --}}
        {{-- لوحة 1: شبكة الأجهزة / الطاولات --}}
        {{-- ═══════════════════════════════ --}}
        <div class="pos-panel-workspace">
            <div class="pos-panel-header">
                <div class="pos-tab-switcher">
                    <a href="{{ route('pos.index', ['tab' => 'devices', 'selected' => request('selected')]) }}"
                       class="pos-tab-btn {{ $activeTab === 'devices' ? 'active' : '' }}">
                        <i class="bi bi-controller"></i> أجهزة البلايستيشن
                    </a>
                    <a href="{{ route('pos.index', ['tab' => 'cafe', 'selected' => request('selected')]) }}"
                       class="pos-tab-btn {{ $activeTab === 'cafe' ? 'active' : '' }}">
                        <i class="bi bi-cup-hot-fill"></i> صالة الكافيه
                    </a>
                </div>
            </div>

            <div class="pos-panel-body">
                @if($activeTab === 'devices')
                {{-- ═════ شبكة أجهزة البلايستيشن ═════ --}}
                <div class="pos-device-grid">
                    @foreach($devices as $device)
                        @if($device->status === 'available')
                        {{-- جهاز متاح - يفتح مودال بدء الجلسة --}}
                        <div class="pos-device-card available" onclick="openStartSessionModal({{ $device->id }}, '{{ $device->name }}', '{{ $device->type_name }}', {{ $device->hourly_rate }})">
                            <span class="device-status-dot"></span>
                            <div class="device-name"><i class="bi bi-display me-1"></i> {{ $device->name }}</div>
                            <div class="device-type">{{ $device->type_name }} — {{ number_format($device->hourly_rate, 2) }} ج.م/ساعة</div>
                            <div class="device-action-btn btn-start-session">
                                <i class="bi bi-play-fill me-1"></i> بدء جلسة
                            </div>
                        </div>

                        @elseif($device->status === 'occupied' && $device->activeSession)
                        {{-- جهاز مشغول - اضغط لتحديده --}}
                        @php $sess = $device->activeSession; @endphp
                        <a href="{{ route('pos.index', ['tab' => 'devices', 'selected' => 'session-'.$sess->id]) }}"
                           class="pos-device-card occupied {{ ($selectedType === 'session' && $selectedEntity?->id === $sess->id) ? 'selected' : '' }}">
                            <span class="device-status-dot"></span>
                            <div class="device-name"><i class="bi bi-display me-1"></i> {{ $device->name }}</div>
                            <div class="device-type">{{ $sess->client_name ?? __('messages.guest') }}</div>
                            <div class="device-timer">
                                <i class="bi bi-stopwatch me-1"></i>
                                {{ $sess->elapsed_time_formatted }}
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="device-cost">{{ number_format($sess->calculatePlaystationCost(), 2) }} ج.م</span>
                                <small style="font-weight:700;font-size:0.72rem;color:#94a3b8;">{{ $sess->items->count() }} صنف</small>
                            </div>
                        </a>

                        @elseif($device->status === 'maintenance')
                        {{-- جهاز صيانة --}}
                        <div class="pos-device-card maintenance">
                            <span class="device-status-dot"></span>
                            <div class="device-name"><i class="bi bi-tools me-1"></i> {{ $device->name }}</div>
                            <div class="device-type">صيانة</div>
                        </div>
                        @endif
                    @endforeach
                </div>

                @else
                {{-- ═════ شبكة صالة الكافيه والطاولات ═════ --}}
                <div class="pos-device-grid">
                    {{-- الطلبات المفتوحة --}}
                    @foreach($openCafeOrders as $order)
                    <a href="{{ route('pos.index', ['tab' => 'cafe', 'selected' => 'cafe-'.$order->id]) }}"
                       class="pos-cafe-card active-order {{ ($selectedType === 'cafe' && $selectedEntity?->id === $order->id) ? 'selected' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-weight:800;font-size:0.92rem;color:var(--pos-text);">
                                <i class="bi bi-{{ $order->order_type === 'table' ? 'grid-1x2' : 'bag' }} me-1" style="color:#f59e0b;"></i>
                                {{ $order->order_type_name }}
                                @if($order->table_number)
                                    — {{ $order->table_number }}
                                @endif
                            </span>
                            <span style="font-size:0.72rem;font-weight:700;color:#94a3b8;">
                                #{{ $order->id }}
                            </span>
                        </div>
                        @if($order->client_name)
                        <div style="font-size:0.78rem;font-weight:600;color:var(--pos-text-secondary);">{{ $order->client_name }}</div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span style="font-weight:800;font-size:0.95rem;color:var(--pos-primary);">
                                {{ number_format($order->total_amount, 2) }} ج.م
                            </span>
                            <span style="font-size:0.72rem;font-weight:700;color:#94a3b8;">{{ $order->items->count() }} صنف</span>
                        </div>
                    </a>
                    @endforeach

                    {{-- زر فتح طلب جديد - طاولة --}}
                    <div class="pos-cafe-card empty-table" onclick="openNewCafeOrderModal('table')">
                        <i class="bi bi-plus-circle" style="font-size:1.8rem;"></i>
                        <span style="font-weight:700;font-size:0.82rem;">طلب طاولة جديد</span>
                    </div>

                    {{-- زر فتح طلب جديد - تيك أواي --}}
                    <div class="pos-cafe-card empty-table" onclick="openNewCafeOrderModal('takeaway')">
                        <i class="bi bi-bag-plus" style="font-size:1.8rem;"></i>
                        <span style="font-weight:700;font-size:0.82rem;">تيك أواي جديد</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ═══════════════════════════════ --}}
        {{-- لوحة 2: قائمة المنتجات          --}}
        {{-- ═══════════════════════════════ --}}
        <div class="pos-panel-products">
            <div class="pos-panel-header">
                <h6><i class="bi bi-grid-3x3-gap-fill me-1" style="color:var(--pos-primary);"></i> قائمة المنتجات</h6>
            </div>

            @if($selectedEntity)
            {{-- شريط التصنيفات --}}
            <div class="pos-category-tabs">
                <button class="pos-cat-pill active" data-category="all">الكل</button>
                @foreach($categories as $cat)
                <button class="pos-cat-pill" data-category="cat-{{ $cat->id }}">{{ $cat->name }}</button>
                @endforeach
            </div>

            {{-- شبكة المنتجات --}}
            <div class="pos-products-grid" id="productsGrid">
                @foreach($categories as $cat)
                    @foreach($cat->products as $product)
                    <form action="{{ $selectedType === 'session' ? route('sessions.addItem', $selectedEntity) : route('cafe-orders.addItem', $selectedEntity) }}"
                          method="POST"
                          class="pos-prod-item cat-{{ $cat->id }}"
                          style="display:contents;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="pos-prod-card {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}">
                            <div>
                                <div class="prod-name">{{ $product->name }}</div>
                                <div class="prod-stock">المخزون: {{ $product->stock_quantity }}</div>
                            </div>
                            <div class="prod-price">{{ number_format($product->sale_price, 2) }} ج.م</div>
                        </button>
                    </form>
                    @endforeach
                @endforeach
            </div>

            @else
            {{-- لا يوجد عنصر مختار --}}
            <div class="pos-no-selection-overlay">
                <i class="bi bi-hand-index-thumb"></i>
                <p>اختر جهاز أو طاولة من القائمة اليمنى<br>لبدء إضافة المنتجات</p>
            </div>
            @endif
        </div>

        {{-- ═══════════════════════════════ --}}
        {{-- لوحة 3: سلة الطلب / الفاتورة   --}}
        {{-- ═══════════════════════════════ --}}
        <div class="pos-panel-cart">
            @if($selectedEntity)
            {{-- رأس السلة --}}
            <div class="pos-cart-header">
                <h6>
                    <i class="bi bi-receipt me-1"></i>
                    @if($selectedType === 'session')
                        فاتورة — {{ $selectedEntity->device?->name ?? 'كافيه فقط' }}
                    @else
                        فاتورة — {{ $selectedEntity->order_type_name }}
                        @if($selectedEntity->table_number) ({{ $selectedEntity->table_number }}) @endif
                    @endif
                </h6>
                <div class="cart-entity-info">
                    @if($selectedType === 'session')
                        {{ $selectedEntity->client_name ?? __('messages.guest') }} —
                        <i class="bi bi-stopwatch"></i> {{ $selectedEntity->elapsed_time_formatted }}
                    @else
                        {{ $selectedEntity->client_name ?? __('messages.guest') }} — طلب #{{ $selectedEntity->id }}
                    @endif
                </div>
            </div>

            {{-- قائمة الأصناف --}}
            <div class="pos-cart-items">
                @forelse($selectedItems as $item)
                <div class="pos-cart-item">
                    <form action="{{ $selectedType === 'session'
                            ? route('sessions.removeItem', [$selectedEntity, $item->id])
                            : route('cafe-orders.removeItem', [$selectedEntity, $item->id]) }}"
                          method="POST" style="display:contents;">
                        @csrf @method('DELETE')
                        <button type="submit" class="item-remove" title="إزالة"><i class="bi bi-x"></i></button>
                    </form>
                    <div class="item-info">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-qty">{{ $item->quantity }} × {{ number_format($item->product->sale_price, 2) }}</div>
                    </div>
                    <div class="item-total">{{ number_format($item->total_price, 2) }}</div>
                </div>
                @empty
                <div class="pos-cart-empty">
                    <i class="bi bi-cart3"></i>
                    <p style="font-weight:700;font-size:0.82rem;">السلة فارغة</p>
                    <span style="font-size:0.75rem;">اضغط على المنتجات لإضافتها</span>
                </div>
                @endforelse
            </div>

            {{-- إجماليات وأزرار --}}
            <div class="pos-cart-footer">
                @if($selectedType === 'session' && $selectedEntity->device)
                <div class="pos-cart-total-row">
                    <span><i class="bi bi-controller me-1"></i> وقت اللعب</span>
                    <span>{{ number_format($selectedEntity->calculatePlaystationCost(), 2) }} ج.م</span>
                </div>
                @endif
                <div class="pos-cart-total-row">
                    <span><i class="bi bi-cup-hot me-1"></i> طلبات الكافيه</span>
                    <span>{{ number_format($selectedItems->sum('total_price'), 2) }} ج.م</span>
                </div>
                <div class="pos-cart-total-row grand-total">
                    <span>الإجمالي المتوقع</span>
                    <span class="total-value">
                        @php
                            $psCost = ($selectedType === 'session' && $selectedEntity->device)
                                ? $selectedEntity->calculatePlaystationCost() : 0;
                            $cafeCost = $selectedItems->sum('total_price');
                        @endphp
                        {{ number_format($psCost + $cafeCost, 2) }} ج.م
                    </span>
                </div>

                <div class="pos-cart-actions">
                    <a href="{{ $selectedType === 'session'
                        ? route('sessions.checkout', $selectedEntity)
                        : route('cafe-orders.checkout', $selectedEntity) }}"
                       class="pos-btn-checkout primary">
                        <i class="bi bi-cash-stack"></i> إتمام الدفع وتحصيل الفاتورة
                    </a>
                    <form action="{{ $selectedType === 'session'
                        ? route('sessions.cancel', $selectedEntity)
                        : route('cafe-orders.cancel', $selectedEntity) }}"
                          method="POST"
                          onsubmit="return confirm('هل أنت متأكد من الإلغاء؟')">
                        @csrf
                        <button type="submit" class="pos-btn-checkout danger w-100">
                            <i class="bi bi-x-circle"></i> إلغاء
                        </button>
                    </form>
                </div>
            </div>

            @else
            {{-- لا يوجد عنصر مختار --}}
            <div class="pos-cart-header" style="background:linear-gradient(135deg,#475569,#64748b);">
                <h6><i class="bi bi-receipt me-1"></i> الفاتورة الحالية</h6>
                <div class="cart-entity-info">لم يتم اختيار جهاز أو طاولة</div>
            </div>
            <div class="pos-cart-empty" style="flex:1;">
                <i class="bi bi-receipt-cutoff"></i>
                <p style="font-weight:700;font-size:0.82rem;">اختر جهاز أو طاولة</p>
                <span style="font-size:0.75rem;">لعرض الفاتورة وإضافة المنتجات</span>
            </div>
            @endif
        </div>

    </div>{{-- end .pos-body --}}
</div>{{-- end .pos-wrapper --}}

{{-- ════════════════════════════════════ --}}
{{-- مودال بدء جلسة بلايستيشن             --}}
{{-- ════════════════════════════════════ --}}
<div class="pos-modal-backdrop" id="startSessionModal">
    <div class="pos-modal">
        <h5><i class="bi bi-play-circle me-2" style="color:var(--pos-success);"></i> بدء جلسة جديدة</h5>
        <form action="{{ route('sessions.store') }}" method="POST">
            @csrf
            <input type="hidden" name="device_id" id="modalDeviceId">

            <div class="mb-3">
                <label class="form-label">الجهاز</label>
                <input type="text" class="form-control" id="modalDeviceName" readonly style="background:#f8fafc;">
            </div>

            <div class="mb-3">
                <label class="form-label">اسم العميل (اختياري)</label>
                <input type="text" name="client_name" class="form-control" placeholder="اسم العميل">
            </div>

            <div class="mb-3">
                <label class="form-label">نوع الجلسة</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="session_type" value="open" id="modalTypeOpen" checked>
                        <label class="form-check-label fw-bold" for="modalTypeOpen">مفتوح</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="session_type" value="pre_paid" id="modalTypePrepaid">
                        <label class="form-check-label fw-bold" for="modalTypePrepaid">مدفوع مسبقاً</label>
                    </div>
                </div>
            </div>

            <div class="mb-3" id="modalPrepaidGroup" style="display:none;">
                <label class="form-label">عدد الدقائق</label>
                <select name="pre_paid_minutes" class="form-select">
                    <option value="30">30 دقيقة</option>
                    <option value="60">60 دقيقة (ساعة)</option>
                    <option value="90">90 دقيقة</option>
                    <option value="120">120 دقيقة (ساعتين)</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-modal-confirm"><i class="bi bi-play-fill me-1"></i> بدء الجلسة</button>
                <button type="button" class="btn-modal-cancel" onclick="closeModal('startSessionModal')">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════ --}}
{{-- مودال فتح طلب كافيه جديد             --}}
{{-- ════════════════════════════════════ --}}
<div class="pos-modal-backdrop" id="newCafeOrderModal">
    <div class="pos-modal">
        <h5><i class="bi bi-cup-hot me-2" style="color:#f59e0b;"></i> فتح طلب كافيه جديد</h5>
        <form action="{{ route('cafe-orders.store') }}" method="POST">
            @csrf
            <input type="hidden" name="order_type" id="modalOrderType" value="table">

            <div class="mb-3">
                <label class="form-label">نوع الطلب</label>
                <input type="text" class="form-control" id="modalOrderTypeName" readonly style="background:#f8fafc;">
            </div>

            <div class="mb-3" id="modalTableNumberGroup">
                <label class="form-label">رقم الطاولة</label>
                <input type="text" name="table_number" class="form-control" placeholder="مثال: طاولة 5">
            </div>

            <div class="mb-3">
                <label class="form-label">اسم العميل (اختياري)</label>
                <input type="text" name="client_name" class="form-control" placeholder="اسم العميل">
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-modal-confirm"><i class="bi bi-plus-lg me-1"></i> فتح الطلب</button>
                <button type="button" class="btn-modal-cancel" onclick="closeModal('newCafeOrderModal')">إلغاء</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// ═══ ساعة حية ═══
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('liveClock').textContent = h + ':' + m + ':' + s;
}
setInterval(updateClock, 1000);
updateClock();

// ═══ تحديث تلقائي كل 60 ثانية ═══
setInterval(() => location.reload(), 60000);

// ═══ تصفية المنتجات حسب التصنيف ═══
document.querySelectorAll('.pos-cat-pill').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.pos-cat-pill').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const cat = this.getAttribute('data-category');
        document.querySelectorAll('.pos-prod-item').forEach(item => {
            if (cat === 'all' || item.classList.contains(cat)) {
                item.style.display = 'contents';
                item.querySelector('.pos-prod-card').style.display = '';
            } else {
                item.style.display = 'none';
                item.querySelector('.pos-prod-card').style.display = 'none';
            }
        });
    });
});

// ═══ مودال بدء جلسة PS ═══
function openStartSessionModal(deviceId, deviceName, typeName, rate) {
    document.getElementById('modalDeviceId').value = deviceId;
    document.getElementById('modalDeviceName').value = deviceName + ' (' + typeName + ' — ' + rate.toFixed(2) + ' ج.م/ساعة)';
    document.getElementById('startSessionModal').classList.add('show');
}

// ═══ مودال طلب كافيه ═══
function openNewCafeOrderModal(type) {
    document.getElementById('modalOrderType').value = type;
    document.getElementById('modalOrderTypeName').value = type === 'table' ? 'طاولة' : 'تيك أواي';
    document.getElementById('modalTableNumberGroup').style.display = type === 'table' ? 'block' : 'none';
    document.getElementById('newCafeOrderModal').classList.add('show');
}

// ═══ إغلاق المودال ═══
function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}

// إغلاق عند الضغط على الخلفية
document.querySelectorAll('.pos-modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});

// نوع الجلسة (مفتوح / مدفوع مسبقاً)
document.querySelectorAll('#startSessionModal input[name="session_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('modalPrepaidGroup').style.display =
            this.value === 'pre_paid' ? 'block' : 'none';
    });
});
</script>
@endpush
@endsection
