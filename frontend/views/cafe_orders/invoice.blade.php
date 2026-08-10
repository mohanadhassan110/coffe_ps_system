@extends('layouts.app')

@section('title', 'فاتورة كافيه #' . $order->id)
@section('page-title', 'فاتورة كافيه #' . $order->id)

@section('content')
<div class="animate-in">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex justify-content-end mb-3">
                <button onclick="printInvoice()" class="btn btn-primary btn-lg shadow-sm"><i class="bi bi-printer me-1"></i>طباعة الفاتورة (80mm)</button>
            </div>

            {{-- الفاتورة الحرارية --}}
            <div class="card border-0 shadow-sm" id="invoiceCard">
                <div class="card-body p-4 text-slate-900" id="invoicePrint" style="font-size:0.95rem;">
                    {{-- رأس الفاتورة --}}
                    <div class="text-center mb-3 pb-3" style="border-bottom:2px dashed #cbd5e1;">
                        <h3 style="font-weight:900;margin-bottom:2px;" class="text-slate-900">{{ __('messages.app_name') }}</h3>
                        <span class="text-slate-600 fw-bold">كافيه وخدمة طاولات وتيك أواي</span>
                        <br>
                        <small class="text-slate-400">━━━━━━━━━━━━━━━━━━━━━━━</small>
                        <br>
                        <strong class="fs-5 text-slate-900">فاتورة طلب كافيه #{{ $order->id }}</strong>
                        <br>
                        <small class="text-slate-600 fw-semibold">{{ $order->updated_at->format('Y/m/d - h:i A') }}</small>
                    </div>

                    {{-- تفاصيل الطلب --}}
                    <div class="mb-3 fw-semibold" style="font-size:0.9rem;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-slate-600">الكاشير:</span>
                            <span class="text-slate-900 fw-bold">{{ $order->user->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-slate-600">نوع الطلب:</span>
                            <span class="text-slate-900 fw-bold">{{ $order->order_type_name }}</span>
                        </div>
                        @if($order->client_name)
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-slate-600">العميل:</span>
                            <span class="text-slate-900 fw-bold">{{ $order->client_name }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- الأصناف --}}
                    <div style="border-top:1px dashed #cbd5e1;padding-top:10px;" class="mb-2">
                        <strong class="text-slate-900">الأصناف المطلوبة</strong>
                        <table class="table table-sm mt-1 mb-0" style="font-size:0.9rem;">
                            <thead>
                                <tr class="text-slate-700">
                                    <th style="border:none;padding:4px 0;">الصنف</th>
                                    <th style="border:none;padding:4px 0;">الكمية</th>
                                    <th style="border:none;padding:4px 0;">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr class="text-slate-900 fw-semibold">
                                    <td style="border:none;padding:3px 0;">{{ $item->product->name }}</td>
                                    <td style="border:none;padding:3px 0;">{{ $item->quantity }}</td>
                                    <td style="border:none;padding:3px 0;">{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- الإجماليات --}}
                    <div style="border-top:2px dashed #cbd5e1;padding-top:10px;margin-top:10px;">
                        <div class="d-flex justify-content-between mb-1 text-slate-700 fw-semibold">
                            <span>المجموع الفرعي:</span>
                            <span>{{ number_format($order->total_amount, 2) }} {{ __('messages.currency') }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="d-flex justify-content-between mb-1 text-emerald-700 fw-bold">
                            <span>مبلغ الخصم:</span>
                            <span>- {{ number_format($order->discount, 2) }} {{ __('messages.currency') }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between text-indigo-700" style="font-size:1.25rem;font-weight:900;">
                            <span>المبلغ النهائي:</span>
                            <span>{{ number_format($order->final_amount, 2) }} {{ __('messages.currency') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mt-2 text-slate-700 fw-bold" style="font-size:0.88rem;">
                            <span>طريقة الدفع:</span>
                            <span class="badge badge-active fs-6">{{ $order->payment_method_name }}</span>
                        </div>
                    </div>

                    {{-- التذييل --}}
                    <div class="text-center mt-3 pt-3 text-slate-600" style="border-top:2px dashed #cbd5e1;">
                        <p class="mb-0 fw-bold">{{ __('messages.invoice.thank_you') }}</p>
                        <small>نتمنى لكم وقتاً ممتعاً ومشروباً هنيئاً</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printInvoice() {
    const content = document.getElementById('invoicePrint').innerHTML;
    const win = window.open('', '_blank');
    win.document.write(`
        <html dir="rtl" lang="ar">
        <head>
            <style>
                * { font-family: 'Cairo', 'Tahoma', sans-serif; }
                body { width: 80mm; margin: 0 auto; padding: 10px; font-size: 12px; color: #000; }
                table { width: 100%; border-collapse: collapse; }
                th, td { text-align: right; padding: 2px 0; }
                .d-flex { display: flex; justify-content: space-between; }
                h3, h4 { margin: 5px 0; }
                .badge { padding: 2px 8px; border-radius: 10px; font-size: 11px; }
                .text-center { text-align: center; }
                .table-sm th, .table-sm td { font-size: 11px; }
                hr { border: 1px dashed #ccc; }
            </style>
        </head>
        <body>${content}</body>
        </html>
    `);
    win.document.close();
    win.print();
}
</script>
@endpush
@endsection
