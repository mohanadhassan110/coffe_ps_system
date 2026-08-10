<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

/**
 * التحكم في التقارير اليومية والرسوم البيانية
 */
class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
    ) {}

    public function daily(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $report = $this->reportService->getDailyReport($date);
        $weeklyTrend = $this->reportService->getWeeklyTrend();
        $topProducts = $this->reportService->getTopSellingProducts($date);
        $hourlyBreakdown = $this->reportService->getHourlyBreakdown($date);

        return view('reports.daily', compact('report', 'date', 'weeklyTrend', 'topProducts', 'hourlyBreakdown'));
    }
}
