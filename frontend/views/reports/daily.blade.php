@extends('layouts.app')
@section('title', __('messages.report.daily_report'))
@section('page-title', __('messages.report.daily_report'))

@section('content')
<div class="animate-in">
    {{-- اختيار التاريخ --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body py-3">
            <form class="d-flex gap-2 align-items-center flex-wrap">
                <label class="form-label mb-0 ms-2 fw-bold" style="white-space:nowrap;color:#1e293b;">
                    <i class="bi bi-calendar3 me-1 text-indigo-600"></i>{{ __('messages.report.select_date') }}:
                </label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" style="max-width:200px;">
                <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>{{ __('messages.search') }}</button>
                <span class="fw-bold text-slate-600 me-auto" style="font-size:0.85rem;">
                    <i class="bi bi-info-circle me-1"></i>{{ $report['date_formatted'] }}
                </span>
            </form>
        </div>
    </div>

    @if(($report['sessions_count'] ?? 0) > 0 || ($report['cafe_orders_count'] ?? 0) > 0 || ($report['total_revenue'] ?? 0) > 0)

    {{-- ════════════════════════════════════ --}}
    {{-- بطاقات KPI الرئيسية                  --}}
    {{-- ════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        {{-- إجمالي الإيرادات --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="overflow:hidden;">
                <div class="card-body p-0">
                    <div style="background:linear-gradient(135deg,#4f46e5,#6366f1);padding:20px;color:#fff;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:0.78rem;font-weight:600;opacity:0.85;">{{ __('messages.report.total_revenue') }}</div>
                                <div style="font-size:1.75rem;font-weight:900;margin-top:4px;">{{ number_format($report['total_revenue'], 2) }}</div>
                                <small class="fw-bold">{{ __('messages.currency') }}</small>
                            </div>
                            <i class="bi bi-cash-stack" style="font-size:2.2rem;opacity:0.3;"></i>
                        </div>
                    </div>
                    <div class="px-3 py-2 d-flex align-items-center gap-2" style="background:#f8fafc;font-size:0.78rem;">
                        <span class="badge" style="background:#dcfce7;color:#15803d;">{{ $report['sessions_count'] + ($report['cafe_orders_count'] ?? 0) }} عملية</span>
                        <span class="text-slate-900 fw-semibold">إجمالي اليوم</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- إيرادات البلايستيشن --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="overflow:hidden;">
                <div class="card-body p-0">
                    <div style="background:linear-gradient(135deg,#0284c7,#0369a1);padding:20px;color:#fff;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:0.78rem;font-weight:600;opacity:0.85;">{{ __('messages.report.ps_revenue') }}</div>
                                <div style="font-size:1.75rem;font-weight:900;margin-top:4px;">{{ number_format($report['ps_revenue'], 2) }}</div>
                                <small class="fw-bold">{{ __('messages.currency') }}</small>
                            </div>
                            <i class="bi bi-controller" style="font-size:2.2rem;opacity:0.3;"></i>
                        </div>
                    </div>
                    <div class="px-3 py-2" style="background:#f8fafc;font-size:0.78rem;">
                        <span class="text-slate-600 fw-semibold">{{ $report['sessions_count'] }} جلسة</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- إيرادات الكافيه --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="overflow:hidden;">
                <div class="card-body p-0">
                    <div style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:20px;color:#fff;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:0.78rem;font-weight:600;opacity:0.85;">{{ __('messages.report.cafe_revenue') }}</div>
                                <div style="font-size:1.75rem;font-weight:900;margin-top:4px;">{{ number_format($report['cafe_revenue'], 2) }}</div>
                                <small class="fw-bold">{{ __('messages.currency') }}</small>
                            </div>
                            <i class="bi bi-cup-hot" style="font-size:2.2rem;opacity:0.3;"></i>
                        </div>
                    </div>
                    <div class="px-3 py-2" style="background:#f8fafc;font-size:0.78rem;">
                        <span class="text-slate-600 fw-semibold">{{ $report['cafe_orders_count'] ?? 0 }} طلب كافيه</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- صافي الربح --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="overflow:hidden;">
                <div class="card-body p-0">
                    <div style="background:linear-gradient(135deg,{{ $report['net_profit'] >= 0 ? '#10b981,#059669' : '#ef4444,#dc2626' }});padding:20px;color:#fff;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:0.78rem;font-weight:600;opacity:0.85;">{{ __('messages.report.net_profit') }}</div>
                                <div style="font-size:1.75rem;font-weight:900;margin-top:4px;">{{ number_format($report['net_profit'], 2) }}</div>
                                <small class="fw-bold">{{ __('messages.currency') }}</small>
                            </div>
                            <i class="bi bi-graph-up-arrow" style="font-size:2.2rem;opacity:0.3;"></i>
                        </div>
                    </div>
                    <div class="px-3 py-2 d-flex align-items-center gap-2" style="background:#f8fafc;font-size:0.78rem;">
                        <span class="badge" style="background:#fee2e2;color:#991b1b;">خصومات: {{ number_format($report['total_discount'], 2) }}</span>
                        <span class="badge" style="background:#fef3c7;color:#92400e;">مصروفات: {{ number_format($report['total_expenses'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════ --}}
    {{-- صف الرسوم البيانية                    --}}
    {{-- ════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        {{-- رسم بياني: اتجاه الأسبوع --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <i class="bi bi-graph-up me-2 text-indigo-600"></i>
                    <span class="fw-bold text-slate-900">اتجاه الإيرادات — آخر 7 أيام</span>
                </div>
                <div class="card-body p-3">
                    <canvas id="weeklyTrendChart" height="250"></canvas>
                </div>
            </div>
        </div>

        {{-- رسم بياني: توزيع الإيرادات (دائري) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <i class="bi bi-pie-chart me-2 text-indigo-600"></i>
                    <span class="fw-bold text-slate-900">توزيع الإيرادات</span>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-3">
                    <div style="width:100%;max-width:280px;">
                        <canvas id="revenueSplitChart" height="280"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════ --}}
    {{-- صف ثاني من الرسوم + البيانات          --}}
    {{-- ════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        {{-- ساعات الذروة --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <i class="bi bi-clock me-2 text-amber-600"></i>
                    <span class="fw-bold text-slate-900">ساعات الذروة</span>
                </div>
                <div class="card-body p-3">
                    @if(count($hourlyBreakdown['labels']) > 0)
                    <canvas id="hourlyChart" height="220"></canvas>
                    @else
                    <div class="text-center py-4 text-slate-400">
                        <i class="bi bi-clock" style="font-size:2rem;"></i>
                        <p class="fw-bold mt-2">لا توجد بيانات لساعات اليوم</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- الأكثر مبيعاً --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <i class="bi bi-star me-2 text-amber-600"></i>
                    <span class="fw-bold text-slate-900">المنتجات الأكثر مبيعاً</span>
                </div>
                <div class="card-body p-3">
                    @if(count($topProducts) > 0)
                    <canvas id="topProductsChart" height="220"></canvas>
                    @else
                    <div class="text-center py-4 text-slate-400">
                        <i class="bi bi-box-seam" style="font-size:2rem;"></i>
                        <p class="fw-bold mt-2">لا توجد مبيعات منتجات لهذا اليوم</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════ --}}
    {{-- التفاصيل المالية وطرق الدفع          --}}
    {{-- ════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        {{-- توزيع طرق الدفع --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <i class="bi bi-credit-card me-2 text-indigo-600"></i>
                    <span class="fw-bold text-slate-900">{{ __('messages.report.payment_breakdown') }}</span>
                </div>
                <div class="card-body">
                    @if($report['payment_breakdown']->count() > 0)
                    <div style="max-width:240px;margin:0 auto 16px;">
                        <canvas id="paymentChart" height="200"></canvas>
                    </div>
                    @foreach($report['payment_breakdown'] as $method => $data)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge" style="background:#e0e7ff;color:#4338ca;font-size:0.8rem;">{{ __('messages.payment_methods.' . $method) }}</span>
                            <small class="text-slate-900 fw-semibold">({{ $data['count'] }} عملية)</small>
                        </div>
                        <strong style="color:#1e293b;font-size:0.92rem;">{{ number_format($data['total'], 2) }} {{ __('messages.currency') }}</strong>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-3 text-slate-400">
                        <i class="bi bi-credit-card" style="font-size:2rem;"></i>
                        <p class="fw-bold mt-2">لا توجد مدفوعات</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ملخص مالي --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <i class="bi bi-calculator me-2 text-indigo-600"></i>
                    <span class="fw-bold text-slate-900">الملخص المالي التفصيلي</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                                <div class="fw-bold text-slate-600 mb-1" style="font-size:0.8rem;">الإيرادات</div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold text-slate-700" style="font-size:0.85rem;">البلايستيشن:</span>
                                    <strong style="color:#15803d;">{{ number_format($report['ps_revenue'], 2) }} ج.م</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold text-slate-700" style="font-size:0.85rem;">الكافيه:</span>
                                    <strong style="color:#15803d;">{{ number_format($report['cafe_revenue'], 2) }} ج.م</strong>
                                </div>
                                <hr style="border-color:#bbf7d0;margin:8px 0;">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-slate-900">الإجمالي:</span>
                                    <strong style="color:#15803d;font-size:1.05rem;">{{ number_format($report['total_revenue'], 2) }} ج.م</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:#fef2f2;border:1px solid #fecaca;">
                                <div class="fw-bold text-slate-600 mb-1" style="font-size:0.8rem;">المصروفات والخصومات</div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold text-slate-700" style="font-size:0.85rem;">المصروفات:</span>
                                    <strong style="color:#dc2626;">{{ number_format($report['total_expenses'], 2) }} ج.م</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold text-slate-700" style="font-size:0.85rem;">الخصومات:</span>
                                    <strong style="color:#dc2626;">{{ number_format($report['total_discount'], 2) }} ج.م</strong>
                                </div>
                                <hr style="border-color:#fecaca;margin:8px 0;">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-slate-900">صافي الربح:</span>
                                    <strong style="color:{{ $report['net_profit'] >= 0 ? '#15803d' : '#dc2626' }};font-size:1.05rem;">
                                        {{ number_format($report['net_profit'], 2) }} ج.م
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- بيانات إضافية --}}
                    <div class="mt-3 p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                        <div class="row text-center">
                            <div class="col-4">
                                <div style="font-size:1.4rem;font-weight:900;color:#4f46e5;">{{ $report['sessions_count'] }}</div>
                                <div style="font-size:0.8rem;font-weight:800;color:#0f172a;">جلسة PS</div>
                            </div>
                            <div class="col-4">
                                <div style="font-size:1.4rem;font-weight:900;color:#d97706;">{{ $report['cafe_orders_count'] ?? 0 }}</div>
                                <div style="font-size:0.8rem;font-weight:800;color:#0f172a;">طلب كافيه</div>
                            </div>
                            <div class="col-4">
                                <div style="font-size:1.4rem;font-weight:900;color:#ef4444;">{{ $report['expenses']->count() }}</div>
                                <div style="font-size:0.8rem;font-weight:800;color:#0f172a;">مصروف</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════ --}}
    {{-- جدول المصروفات                        --}}
    {{-- ════════════════════════════════════ --}}
    @if($report['expenses']->count() > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <i class="bi bi-wallet2 me-2" style="color:#ef4444;"></i>
            <span class="fw-bold text-slate-900">تفاصيل المصروفات</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="background:#f8fafc;">{{ __('messages.table.reason') }}</th>
                            <th style="background:#f8fafc;">{{ __('messages.table.amount') }}</th>
                            <th style="background:#f8fafc;">{{ __('messages.table.cashier') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report['expenses'] as $expense)
                        <tr>
                            <td class="fw-bold" style="color:#1e293b;">{{ $expense->reason }}</td>
                            <td><strong style="color:#ef4444;">{{ number_format($expense->amount, 2) }} {{ __('messages.currency') }}</strong></td>
                            <td class="fw-semibold" style="color:#475569;">{{ $expense->user->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @else
    {{-- لا توجد بيانات --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body py-5 text-center">
            <i class="bi bi-bar-chart" style="font-size:4rem;color:#cbd5e1;"></i>
            <h4 class="fw-bold mt-3" style="color:#1e293b;">{{ __('messages.report.no_data') }}</h4>
            <p style="color:#64748b;">{{ $report['date_formatted'] }}</p>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════ --}}
    {{-- رسم بياني: اتجاه الأسبوع (دائماً)    --}}
    {{-- ════════════════════════════════════ --}}
    @if(($report['sessions_count'] ?? 0) == 0 && ($report['cafe_orders_count'] ?? 0) == 0 && ($report['total_revenue'] ?? 0) == 0)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom">
            <i class="bi bi-graph-up me-2 text-indigo-600"></i>
            <span class="fw-bold text-slate-900">اتجاه الإيرادات — آخر 7 أيام</span>
        </div>
        <div class="card-body p-3">
            <canvas id="weeklyTrendChartEmpty" height="200"></canvas>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = 'Cairo';
    Chart.defaults.font.weight = '700';
    Chart.defaults.color = '#0f172a';

    const weeklyData = @json($weeklyTrend);
    const topProductsData = @json($topProducts);
    const hourlyData = @json($hourlyBreakdown);

    // ═══ اتجاه الأسبوع (Line Chart) ═══
    const weeklyEl = document.getElementById('weeklyTrendChart') || document.getElementById('weeklyTrendChartEmpty');
    if (weeklyEl) {
        new Chart(weeklyEl, {
            type: 'line',
            data: {
                labels: weeklyData.labels,
                datasets: [
                    {
                        label: 'البلايستيشن',
                        data: weeklyData.ps,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79,70,229,0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#4f46e5',
                    },
                    {
                        label: 'الكافيه',
                        data: weeklyData.cafe,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#f59e0b',
                    },
                    {
                        label: 'المصروفات',
                        data: weeklyData.expenses,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,0.05)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        borderDash: [5, 5],
                        pointRadius: 3,
                        pointBackgroundColor: '#ef4444',
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, padding: 15, font: { weight: '700' } } },
                    tooltip: { rtl: true, textDirection: 'rtl' }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { callback: v => v + ' ج.م' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // ═══ توزيع الإيرادات (Doughnut) ═══
    @if(($report['total_revenue'] ?? 0) > 0)
    const splitEl = document.getElementById('revenueSplitChart');
    if (splitEl) {
        new Chart(splitEl, {
            type: 'doughnut',
            data: {
                labels: ['بلايستيشن', 'كافيه'],
                datasets: [{
                    data: [{{ $report['ps_revenue'] }}, {{ $report['cafe_revenue'] }}],
                    backgroundColor: ['#4f46e5', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12, font: { weight: '700' } } },
                    tooltip: { rtl: true, textDirection: 'rtl', callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed.toFixed(2) + ' ج.م' } }
                }
            }
        });
    }
    @endif

    // ═══ ساعات الذروة (Bar Chart) ═══
    @if(count($hourlyBreakdown['labels']) > 0)
    const hourlyEl = document.getElementById('hourlyChart');
    if (hourlyEl) {
        new Chart(hourlyEl, {
            type: 'bar',
            data: {
                labels: hourlyData.labels,
                datasets: [{
                    label: 'الإيرادات',
                    data: hourlyData.data,
                    backgroundColor: hourlyData.data.map(v => {
                        const max = Math.max(...hourlyData.data);
                        return v === max ? '#4f46e5' : '#a5b4fc';
                    }),
                    borderRadius: 6,
                    barThickness: 28,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { rtl: true, textDirection: 'rtl', callbacks: { label: ctx => ctx.parsed.y.toFixed(2) + ' ج.م' } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { callback: v => v + ' ج.م' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
    @endif

    // ═══ الأكثر مبيعاً (Horizontal Bar) ═══
    @if(count($topProducts) > 0)
    const topEl = document.getElementById('topProductsChart');
    if (topEl) {
        new Chart(topEl, {
            type: 'bar',
            data: {
                labels: topProductsData.map(p => p.name),
                datasets: [{
                    label: 'الكمية المباعة',
                    data: topProductsData.map(p => p.total_qty),
                    backgroundColor: ['#4f46e5','#6366f1','#818cf8','#a5b4fc','#c7d2fe','#0284c7','#38bdf8','#f59e0b','#fbbf24','#fde68a'],
                    borderRadius: 6,
                    barThickness: 20,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { rtl: true, textDirection: 'rtl' }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    y: { grid: { display: false } }
                }
            }
        });
    }
    @endif

    // ═══ طرق الدفع (Pie Chart) ═══
    @if(($report['payment_breakdown'] ?? collect())->count() > 0)
    const payEl = document.getElementById('paymentChart');
    if (payEl) {
        const paymentData = @json($report['payment_breakdown']);
        const payLabels = [];
        const payValues = [];
        const payColors = { 'cash': '#10b981', 'vodafone_cash': '#ef4444', 'card': '#3b82f6' };
        const payColorArr = [];
        const methodNames = { 'cash': 'كاش', 'vodafone_cash': 'فودافون كاش', 'card': 'بطاقة' };

        for (const [method, data] of Object.entries(paymentData)) {
            payLabels.push(methodNames[method] || method);
            payValues.push(data.total);
            payColorArr.push(payColors[method] || '#94a3b8');
        }

        new Chart(payEl, {
            type: 'doughnut',
            data: {
                labels: payLabels,
                datasets: [{ data: payValues, backgroundColor: payColorArr, borderWidth: 0, hoverOffset: 6 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 10, font: { weight: '700', size: 11 } } },
                    tooltip: { rtl: true, textDirection: 'rtl', callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed.toFixed(2) + ' ج.م' } }
                }
            }
        });
    }
    @endif
</script>
@endpush
@endsection
