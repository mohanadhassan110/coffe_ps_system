<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;
use App\Services\ReportService;

/**
 * لوحة التحكم الرئيسية - عرض الإحصائيات وتنبيهات المخزون
 */
class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected InventoryService $inventoryService,
    ) {}

    public function index()
    {
        $stats = $this->reportService->getDashboardStats();
        $lowStockProducts = $this->inventoryService->getLowStockProducts();

        return view('dashboard.index', compact('stats', 'lowStockProducts'));
    }
}
