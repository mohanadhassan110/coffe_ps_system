<?php

namespace App\Http\Controllers;

use App\Models\CafeOrder;
use App\Models\Category;
use App\Models\Device;
use App\Models\GameSession;
use App\Services\CafeOrderService;
use App\Services\InventoryService;
use App\Services\ReportService;
use App\Services\SessionService;
use Illuminate\Http\Request;

/**
 * نقطة البيع الموحدة - شاشة واحدة لإدارة الأجهزة والكافيه والطلبات
 * Unified POS Controller — Single screen for devices, café, and orders
 */
class PosController extends Controller
{
    public function __construct(
        protected SessionService $sessionService,
        protected CafeOrderService $cafeOrderService,
        protected InventoryService $inventoryService,
        protected ReportService $reportService,
    ) {}

    /**
     * الشاشة الرئيسية الموحدة لنقطة البيع (Single-Screen Unified POS)
     */
    public function index(Request $request)
    {
        // تصفية العرض (الكل / أجهزة / طاولات / تيك أواي)
        $filter = $request->get('filter', 'all');

        // جميع الأجهزة مع الجلسة النشطة إن وجدت
        $devices = Device::with(['activeSession.items.product', 'activeSession.user'])
            ->orderBy('name')
            ->get();

        // طلبات الكافيه المفتوحة (طاولات وتيك أواي)
        $openCafeOrders = CafeOrder::open()
            ->with(['user', 'items.product'])
            ->latest()
            ->get();

        // التصنيفات والمنتجات (قائمة السريعة)
        $categories = Category::with(['products' => function ($q) {
            $q->orderBy('name');
        }])->orderBy('name')->get();

        // تحديد العنصر المختار (جلسة PS أو طلب كافيه)
        $selectedType = null; // 'session' | 'cafe'
        $selectedEntity = null;
        $selectedItems = collect();

        $selected = $request->get('selected');
        if ($selected) {
            if (str_starts_with($selected, 'session-')) {
                $sessionId = (int) str_replace('session-', '', $selected);
                $selectedEntity = GameSession::with(['device', 'user', 'items.product'])
                    ->find($sessionId);
                if ($selectedEntity && $selectedEntity->isActive()) {
                    $selectedType = 'session';
                    $selectedItems = $selectedEntity->items;
                } else {
                    $selectedEntity = null;
                }
            } elseif (str_starts_with($selected, 'cafe-')) {
                $orderId = (int) str_replace('cafe-', '', $selected);
                $selectedEntity = CafeOrder::with(['user', 'items.product'])
                    ->find($orderId);
                if ($selectedEntity && $selectedEntity->status === 'open') {
                    $selectedType = 'cafe';
                    $selectedItems = $selectedEntity->items;
                } else {
                    $selectedEntity = null;
                }
            }
        }

        // إذا لم يتم تحديد عنصر يدويًا، نحاول اختيار أول عنصر نشط افتراضيًا لتسهيل العمل على الكاشير
        if (!$selectedEntity) {
            $firstActiveSession = GameSession::active()->first();
            if ($firstActiveSession) {
                $selectedEntity = $firstActiveSession->load(['device', 'user', 'items.product']);
                $selectedType = 'session';
                $selectedItems = $selectedEntity->items;
            } else {
                $firstOpenCafe = CafeOrder::open()->first();
                if ($firstOpenCafe) {
                    $selectedEntity = $firstOpenCafe->load(['user', 'items.product']);
                    $selectedType = 'cafe';
                    $selectedItems = $selectedEntity->items;
                }
            }
        }

        // إحصائيات سريعة للشريط العلوي
        $quickStats = [
            'today_revenue' => $this->reportService->getDashboardStats()['today_revenue'] ?? 0,
            'active_sessions' => GameSession::active()->count(),
            'open_cafe_orders' => CafeOrder::open()->count(),
        ];

        return view('pos.index', compact(
            'filter',
            'devices',
            'openCafeOrders',
            'categories',
            'selectedType',
            'selectedEntity',
            'selectedItems',
            'quickStats',
        ));
    }
}
