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
            <span>{{ __('messages.app_name') }} — نظام نقطة البيع الموحد</span>
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
            @if(isset($quickStats['controllers']))
            <div class="pos-mini-stat">
                <span class="stat-dot" style="background:#8b5cf6;"></span>
                الأذرع المتاحة: <strong>{{ $quickStats['controllers']['available'] }} / {{ $quickStats['controllers']['total'] }}</strong>
            </div>
            @endif
        </div>

        <div class="pos-clock">
            <span class="live-time" id="liveClock">--:--:--</span>
            <div class="pos-nav">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('reports.daily') }}"><i class="bi bi-bar-chart"></i> التقارير والتحليلات</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit"><i class="bi bi-box-arrow-left"></i> خروج</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════ --}}
    {{-- الجسم الثلاثي (Three Unified Panels) --}}
    {{-- ════════════════════════════════════ --}}
    <div class="pos-body">

        {{-- ═══════════════════════════════ --}}
        {{-- لوحة 1: بيئة العمل الموحدة        --}}
        {{-- (جميع الأجهزة والطاولات والتيك أواي) --}}
        {{-- ═══════════════════════════════ --}}
        <div class="pos-panel-workspace">
            <div class="pos-panel-header">
                <h6><i class="bi bi-grid-fill me-1" style="color:var(--pos-primary);"></i> بيئة العمل (الأجهزة والطاولات)</h6>
                <div class="pos-tab-switcher">
                    <button class="pos-tab-btn active" data-workspace-filter="all">الكل</button>
                    <button class="pos-tab-btn" data-workspace-filter="ps"><i class="bi bi-controller"></i> بلايستيشن</button>
                    <button class="pos-tab-btn" data-workspace-filter="tables"><i class="bi bi-cup-hot"></i> طاولات</button>
                    <button class="pos-tab-btn" data-workspace-filter="takeaway"><i class="bi bi-bag"></i> تيك أواي</button>
                </div>
            </div>

            <div class="pos-panel-body">
                <div class="pos-device-grid">

                    {{-- ═════ أجهزة البلايستيشن ═════ --}}
                    @foreach($devices as $device)
                        @if($device->status === 'available')
                        {{-- جهاز متاح --}}
                        @php
                            $deviceIdle = $device->total_controllers;
                            $isPlayable = !in_array($device->type, ['billiard']);
                        @endphp
                        <div class="pos-device-card available workspace-item ps" onclick="openStartSessionModal({{ $device->id }}, '{{ $device->name }}', '{{ $device->type_name }}', {{ $device->hourly_rate }}, {{ $device->total_controllers }}, {{ $deviceIdle ? 'true' : 'false' }})">
                            <span class="device-status-dot"></span>
                            <div class="device-name"><i class="bi bi-display me-1"></i> {{ $device->name }}</div>
                            <div class="device-type">{{ $device->type_name }} — {{ number_format($device->hourly_rate, 2) }} ج.م/ساعة</div>
                            @if($isPlayable)
                            <div class="device-controllers">
                                <i class="bi bi-controller me-1"></i>
                                الأذرع المتاحة: <strong>{{ $device->total_controllers }}</strong>
                            </div>
                            @endif
                            <div class="device-action-btn btn-start-session">
                                <i class="bi bi-play-fill me-1"></i> بدء جلسة
                            </div>
                        </div>

                        @elseif($device->status === 'occupied' && $device->activeSession)
                        {{-- جهاز مشغول --}}
                        @php $sess = $device->activeSession; @endphp
                        <a href="{{ route('pos.index', ['selected' => 'session-'.$sess->id]) }}"
                           class="pos-device-card occupied workspace-item ps {{ ($selectedType === 'session' && $selectedEntity?->id === $sess->id) ? 'selected' : '' }}">
                            <span class="device-status-dot"></span>
                            <div class="device-name"><i class="bi bi-display me-1"></i> {{ $device->name }}</div>
                            <div class="device-type">{{ $sess->client_name ?? __('messages.guest') }}</div>
                            <div class="device-timer">
                                <i class="bi bi-stopwatch me-1"></i>
                                {{ $sess->elapsed_time_formatted }}
                            </div>
                            @if(!in_array($device->type, ['billiard']))
                            <div class="device-controllers">
                                <i class="bi bi-controller me-1"></i>
                                الأذرع النشطة: <strong>{{ $sess->active_controllers }}</strong> / المتاحة: <strong>{{ $device->idle_controllers }}</strong>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="device-cost">{{ number_format($sess->calculatePlaystationCost(), 2) }} ج.م</span>
                                <small style="font-weight:800;font-size:0.75rem;color:#0f172a;">{{ $sess->items->count() }} صنف</small>
                            </div>
                        </a>

                        @elseif($device->status === 'maintenance')
                        {{-- جهاز صيانة --}}
                        <div class="pos-device-card maintenance workspace-item ps">
                            <span class="device-status-dot"></span>
                            <div class="device-name"><i class="bi bi-tools me-1"></i> {{ $device->name }}</div>
                            <div class="device-type">صيانة</div>
                            <div class="device-controllers">
                                <i class="bi bi-controller me-1"></i>
                                الأذرع المتاحة: <strong>0</strong> / {{ $device->total_controllers }}
                            </div>
                        </div>
                        @endif
                    @endforeach

                    {{-- ═════ طلبات الكافيه النشطة (طاولات وتيك أواي) ═════ --}}
                    @foreach($openCafeOrders as $order)
                    @php $isTable = $order->order_type === 'table'; @endphp
                    <a href="{{ route('pos.index', ['selected' => 'cafe-'.$order->id]) }}"
                       class="pos-cafe-card active-order workspace-item {{ $isTable ? 'tables' : 'takeaway' }} {{ ($selectedType === 'cafe' && $selectedEntity?->id === $order->id) ? 'selected' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-weight:800;font-size:0.92rem;color:var(--pos-text);">
                                <i class="bi bi-{{ $isTable ? 'grid-1x2' : 'bag-fill' }} me-1" style="color:#f59e0b;"></i>
                                {{ $order->order_type_name }}
                            </span>
                            <span style="font-size:0.75rem;font-weight:800;color:#0f172a;">#{{ $order->id }}</span>
                        </div>
                        @if($order->client_name)
                        <div style="font-size:0.8rem;font-weight:700;color:#0f172a;">{{ $order->client_name }}</div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span style="font-weight:800;font-size:0.95rem;color:var(--pos-primary);">
                                {{ number_format($order->total_amount, 2) }} ج.م
                            </span>
                            <span style="font-size:0.75rem;font-weight:800;color:#0f172a;">{{ $order->items->count() }} صنف</span>
                        </div>
                    </a>
                    @endforeach

                    {{-- ═════ أزرار إضافة طلب جديدة مسجلة داخل نفس الشبكة ═════ --}}
                    <div class="pos-cafe-card empty-table workspace-item tables" onclick="openNewCafeOrderModal('table')">
                        <i class="bi bi-plus-circle" style="font-size:1.8rem;"></i>
                        <span style="font-weight:700;font-size:0.82rem;">طلب طاولة جديد</span>
                    </div>

                    <div class="pos-cafe-card empty-table workspace-item takeaway" onclick="openNewCafeOrderModal('takeaway')">
                        <i class="bi bi-bag-plus" style="font-size:1.8rem;"></i>
                        <span style="font-weight:700;font-size:0.82rem;">تيك أواي جديد</span>
                    </div>

                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════ --}}
        {{-- لوحة 2: قائمة المنتجات السريعة   --}}
        {{-- ═══════════════════════════════ --}}
        <div class="pos-panel-products">
            <div class="pos-panel-header">
                <h6><i class="bi bi-grid-3x3-gap-fill me-1" style="color:var(--pos-primary);"></i> قائمة المنتجات</h6>
            </div>

            {{-- شريط تصنيفات المنتجات --}}
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
                    @if($selectedEntity)
                    <form action="{{ $selectedType === 'session' ? route('sessions.addItem', $selectedEntity) : route('cafe-orders.addItem', $selectedEntity) }}"
                          method="POST"
                          class="pos-prod-item cat-{{ $cat->id }}"
                          style="display:contents;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="pos-prod-card {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}" title="{{ $product->name }}">
                            <div>
                                <div class="prod-name">{{ $product->name }}</div>
                                <div class="prod-stock">المخزون: {{ $product->stock_quantity }}</div>
                            </div>
                            <div class="prod-price">{{ number_format($product->sale_price, 2) }} ج.م</div>
                        </button>
                    </form>
                    @else
                    <div class="pos-prod-item cat-{{ $cat->id }}" style="display:contents;">
                        <button type="button" onclick="alert('يرجى اختيار جهاز أو طاولة أولاً من القائمة على اليسار لبدء إضافة المنتجات')" class="pos-prod-card {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}">
                            <div>
                                <div class="prod-name">{{ $product->name }}</div>
                                <div class="prod-stock">المخزون: {{ $product->stock_quantity }}</div>
                            </div>
                            <div class="prod-price">{{ number_format($product->sale_price, 2) }} ج.م</div>
                        </button>
                    </div>
                    @endif
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- ═══════════════════════════════ --}}
        {{-- لوحة 3: السلة والفاتورة النشطة  --}}
        {{-- ═══════════════════════════════ --}}
        <div class="pos-panel-cart">
            @if($selectedEntity)
            {{-- رأس السلة --}}
            <div class="pos-cart-header">
                <h6>
                    <i class="bi bi-receipt me-1"></i>
                    @if($selectedType === 'session')
                        جلسة — {{ $selectedEntity->device?->name ?? 'بلايستيشن' }}
                    @else
                        طلب — {{ $selectedEntity->order_type_name }}
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
                    <span style="font-size:0.75rem;">اضغط على المنتجات لإضافتها فوراً</span>
                </div>
                @endforelse
            </div>

            {{-- إجماليات وأزرار الدفع --}}
            <div class="pos-cart-footer">
                @if($selectedType === 'session' && $selectedEntity->device && !in_array($selectedEntity->device->type, ['billiard']))
                {{-- تحكم لحظي في عدد أذرع الجلسة النشطة --}}
                <form action="{{ route('sessions.updateControllers', $selectedEntity) }}" method="POST"
                      class="controllers-inline-form" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:10px 12px;margin-bottom:8px;">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <label for="inlineControllersSelect" class="fw-bold" style="color:#0f172a;font-size:0.85rem;">
                            <i class="bi bi-controller me-1" style="color:#0f172a;"></i> الأذرع النشطة: {{ $selectedEntity->active_controllers }} / المتاحة: {{ $selectedEntity->device->idle_controllers }}
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            <select name="active_controllers" id="inlineControllersSelect" class="form-select form-select-sm fw-bold" style="width:auto;color:#000;background-color:#fff;">
                                @for($c = 1; $c <= $selectedEntity->device->total_controllers; $c++)
                                    <option value="{{ $c }}" {{ $selectedEntity->active_controllers == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-sm fw-bold" style="background:#4f46e5;color:#fff;border:none;">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                    </div>
                </form>
                @endif
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
                            $grandExpected = $psCost + $cafeCost;
                        @endphp
                        {{ number_format($grandExpected, 2) }} ج.م
                    </span>
                </div>

                <div class="pos-cart-actions">
                    <button type="button" class="pos-btn-checkout primary" onclick="openQuickCheckoutModal({{ $grandExpected }})">
                        <i class="bi bi-cash-stack"></i> إتمام الدفع وتحصيل الفاتورة
                    </button>
                    <form action="{{ $selectedType === 'session'
                        ? route('sessions.cancel', $selectedEntity)
                        : route('cafe-orders.cancel', $selectedEntity) }}"
                          method="POST"
                          onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الطلب/الجلسة؟')">
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

            <div class="mb-3">
                <label class="form-label">عدد أذرع التحكم (اللاعبين)</label>
                <select name="active_controllers" id="modalControllersSelect" class="form-select fw-bold" style="color:#000;background-color:#fff;" required></select>
                <small class="form-text text-dark fw-bold" style="color:#0f172a;">لا يمكن تجاوز عدد الأذرع المتاحة على هذا الجهاز.</small>
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
                <button type="submit" class="btn-modal-confirm" style="background:var(--pos-success);color:#fff;"><i class="bi bi-play-fill me-1"></i> بدء الجلسة</button>
                <button type="button" class="btn-modal-cancel" style="background:#e2e8f0;color:#334155;" onclick="closeModal('startSessionModal')">إلغاء</button>
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
                <button type="submit" class="btn-modal-confirm" style="background:var(--pos-primary);color:#fff;"><i class="bi bi-plus-lg me-1"></i> فتح الطلب</button>
                <button type="button" class="btn-modal-cancel" style="background:#e2e8f0;color:#334155;" onclick="closeModal('newCafeOrderModal')">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════ --}}
{{-- مودال إتمام الدفع وتحصيل الفاتورة      --}}
{{-- ════════════════════════════════════ --}}
@if($selectedEntity)
<div class="pos-modal-backdrop" id="quickCheckoutModal">
    <div class="pos-modal">
        <h5><i class="bi bi-cash-stack me-2" style="color:var(--pos-success);"></i> تحصيل وإتمام الفاتورة</h5>
        <form action="{{ $selectedType === 'session' ? route('sessions.close', $selectedEntity) : route('cafe-orders.close', $selectedEntity) }}" method="POST">
            @csrf

            <div class="p-3 mb-3" style="background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;">
                <div class="d-flex justify-content-between mb-1" style="font-size:0.88rem;font-weight:700;">
                    <span>إجمالي الحساب:</span>
                    <span id="checkoutModalSubtotal">{{ number_format($grandExpected ?? 0, 2) }} ج.م</span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">الخصم (ج.م)</label>
                <input type="number" step="0.5" name="discount" id="checkoutDiscountInput" class="form-control" value="0" min="0" oninput="updateCheckoutFinalTotal({{ $grandExpected ?? 0 }})">
            </div>

            <div class="mb-3">
                <label class="form-label">المبلغ النهائي المطلوب</label>
                <div class="fw-black fs-4 text-success" id="checkoutModalFinalTotal">{{ number_format($grandExpected ?? 0, 2) }} ج.م</div>
            </div>

            <div class="mb-4">
                <label class="form-label">طريقة الدفع</label>
                <select name="payment_method" class="form-select fw-bold">
                    <option value="cash">💵 كاش (نقداً)</option>
                    <option value="vodafone_cash">📱 فودافون كاش</option>
                    <option value="card">💳 بطاقة / فيزا</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-modal-confirm" style="background:var(--pos-success);color:#fff;"><i class="bi bi-check-circle me-1"></i> تأكيد وتحصيل</button>
                <button type="button" class="btn-modal-cancel" style="background:#e2e8f0;color:#334155;" onclick="closeModal('quickCheckoutModal')">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
// ═══ ساعة حية ═══
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const clockEl = document.getElementById('liveClock');
    if (clockEl) clockEl.textContent = h + ':' + m + ':' + s;
}
setInterval(updateClock, 1000);
updateClock();

// ═══ تصفية عناصر بيئة العمل (Workspace Filters) ═══
document.querySelectorAll('[data-workspace-filter]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('[data-workspace-filter]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filter = this.getAttribute('data-workspace-filter');
        document.querySelectorAll('.workspace-item').forEach(item => {
            if (filter === 'all' || item.classList.contains(filter)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

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
function openStartSessionModal(deviceId, deviceName, typeName, rate, totalControllers, hasControllers) {
    document.getElementById('modalDeviceId').value = deviceId;
    document.getElementById('modalDeviceName').value = deviceName + ' (' + typeName + ' — ' + rate.toFixed(2) + ' ج.م/ساعة)';

    // بناء قائمة اختيار عدد الأذرع حسب المتاح فعلياً على الجهاز
    const select = document.getElementById('modalControllersSelect');
    const groupWrapper = select.closest('.mb-3');
    select.innerHTML = '';

    if (!hasControllers || totalControllers < 1) {
        // أجهزة بلا أذرع (مثل البلياردو)
        groupWrapper.style.display = 'none';
        select.removeAttribute('required');
    } else {
        groupWrapper.style.display = 'block';
        select.setAttribute('required', 'required');
        for (let c = 1; c <= totalControllers; c++) {
            const opt = document.createElement('option');
            opt.value = c;
            opt.textContent = c + (c === 1 ? ' لاعب (فردي)' : (c === 2 ? ' لاعبين (جماعي)' : ' لاعبين'));
            if (c === Math.min(2, totalControllers)) opt.selected = true;
            select.appendChild(opt);
        }
    }

    document.getElementById('startSessionModal').classList.add('show');
}

// ═══ مودال طلب كافيه ═══
function openNewCafeOrderModal(type) {
    document.getElementById('modalOrderType').value = type;
    document.getElementById('modalOrderTypeName').value = type === 'table' ? 'طاولة' : 'تيك أواي';
    document.getElementById('modalTableNumberGroup').style.display = type === 'table' ? 'block' : 'none';
    document.getElementById('newCafeOrderModal').classList.add('show');
}

// ═══ مودال الدفع السريع ═══
function openQuickCheckoutModal(subtotal) {
    const modal = document.getElementById('quickCheckoutModal');
    if (modal) {
        modal.classList.add('show');
    }
}

function updateCheckoutFinalTotal(subtotal) {
    const discount = parseFloat(document.getElementById('checkoutDiscountInput').value) || 0;
    const finalTotal = Math.max(0, subtotal - discount);
    document.getElementById('checkoutModalFinalTotal').textContent = finalTotal.toFixed(2) + ' ج.م';
}

// ═══ إغلاق المودال ═══
function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('show');
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
