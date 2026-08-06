<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCafeOrderItemRequest;
use App\Http\Requests\CloseCafeOrderRequest;
use App\Http\Requests\StoreCafeOrderRequest;
use App\Models\CafeOrder;
use App\Models\Category;
use App\Services\CafeOrderService;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * التحكم في طلبات الكافيه المستقلة (طاولات وتيك أواي)
 */
class CafeOrderController extends Controller
{
    public function __construct(
        protected CafeOrderService $cafeOrderService,
    ) {}

    /**
     * عرض أرشيف جميع طلبات الكافيه
     */
    public function index(Request $request)
    {
        $query = CafeOrder::with(['user', 'items.product'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        $orders = $query->paginate(20);
        return view('cafe_orders.index', compact('orders'));
    }

    /**
     * واجهة صالة الكافيه والطاولات النشطة (شاشة الكاشير)
     */
    public function active()
    {
        $openOrders = CafeOrder::open()
            ->with(['user', 'items.product'])
            ->latest()
            ->get();

        return view('cafe_orders.active', compact('openOrders'));
    }

    /**
     * صفحة فتح طلب كافيه جديد
     */
    public function create()
    {
        return view('cafe_orders.create');
    }

    /**
     * فتح طلب كافيه جديد
     */
    public function store(StoreCafeOrderRequest $request)
    {
        $order = $this->cafeOrderService->createOrder($request->validated());
        return redirect()->route('pos.index', ['tab' => 'cafe', 'selected' => 'cafe-'.$order->id])
            ->with('success', __('messages.success.created'));
    }

    /**
     * عرض تفاصيل الطلب مع إمكانية إضافة منتجات
     */
    public function show(CafeOrder $cafeOrder)
    {
        $cafeOrder->load(['user', 'items.product']);
        $categories = Category::with('products')->orderBy('name')->get();
        $order = $cafeOrder;

        return view('cafe_orders.show', compact('order', 'categories'));
    }

    /**
     * إضافة منتج للطلب مع خصم المخزون أوتوماتيكياً
     */
    public function addItem(AddCafeOrderItemRequest $request, CafeOrder $cafeOrder)
    {
        try {
            $this->cafeOrderService->addItem(
                $cafeOrder,
                $request->validated()['product_id'],
                $request->validated()['quantity']
            );
            return redirect()->route('pos.index', ['tab' => 'cafe', 'selected' => 'cafe-'.$cafeOrder->id])
                ->with('success', __('messages.success.item_added'));
        } catch (InvalidArgumentException $e) {
            return redirect()->route('pos.index', ['tab' => 'cafe', 'selected' => 'cafe-'.$cafeOrder->id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * حذف عنصر من الطلب واستعادة المخزون
     */
    public function removeItem(CafeOrder $cafeOrder, int $itemId)
    {
        try {
            $this->cafeOrderService->removeItem($cafeOrder, $itemId);
            return redirect()->route('pos.index', ['tab' => 'cafe', 'selected' => 'cafe-'.$cafeOrder->id])
                ->with('success', __('messages.success.item_removed'));
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * صفحة دفع وتحصيل طلب الكافيه
     */
    public function checkout(CafeOrder $cafeOrder)
    {
        $cafeOrder->load(['user', 'items.product']);
        $order = $cafeOrder;
        return view('cafe_orders.checkout', compact('order'));
    }

    /**
     * إغلاق الطلب وتحصيله
     */
    public function close(CloseCafeOrderRequest $request, CafeOrder $cafeOrder)
    {
        try {
            $order = $this->cafeOrderService->checkout($cafeOrder, $request->validated());
            return redirect()->route('cafe-orders.invoice', $order)
                ->with('success', __('messages.success.session_closed'));
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * الفاتورة الحرارية الخاصة بطلب الكافيه
     */
    public function invoice(CafeOrder $cafeOrder)
    {
        $cafeOrder->load(['user', 'items.product']);
        $order = $cafeOrder;
        return view('cafe_orders.invoice', compact('order'));
    }

    /**
     * إلغاء الطلب وإعادة المخزون
     */
    public function cancel(CafeOrder $cafeOrder)
    {
        try {
            $this->cafeOrderService->cancelOrder($cafeOrder);
            return redirect()->route('pos.index', ['tab' => 'cafe'])
                ->with('success', __('messages.success.session_cancelled'));
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
