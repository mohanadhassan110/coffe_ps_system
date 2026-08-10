<?php

namespace App\Services;

use App\Models\CafeOrder;
use App\Models\Expense;
use App\Models\GameSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * خدمة التقارير والإحصائيات
 * تجميع البيانات للتقارير اليومية ولوحة التحكم
 */
class ReportService
{
    /**
     * الحصول على التقرير اليومي
     *
     * @param string|null $date التاريخ (Y-m-d), null = اليوم
     * @return array
     */
    public function getDailyReport(?string $date = null): array
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        // الجلسات المغلقة لهذا اليوم
        $closedSessions = GameSession::closed()
            ->whereDate('end_time', $date)
            ->get();

        // طلبات الكافيه المستقلة المكتملة لهذا اليوم
        $completedCafeOrders = CafeOrder::completed()
            ->whereDate('updated_at', $date)
            ->get();

        // إجماليات الإيرادات
        $psRevenue = $closedSessions->sum('playstation_total');
        $sessionCafeRevenue = $closedSessions->sum('cafe_total');
        $independentCafeRevenue = $completedCafeOrders->sum('final_amount');
        $cafeRevenue = $sessionCafeRevenue + $independentCafeRevenue;

        $totalRevenue = $psRevenue + $cafeRevenue;
        $totalDiscount = $closedSessions->sum('discount') + $completedCafeOrders->sum('discount');

        // توزيع طرق الدفع
        $allPayments = collect();
        foreach ($closedSessions as $s) {
            if ($s->payment_method) {
                $allPayments->push(['method' => $s->payment_method, 'amount' => $s->final_amount]);
            }
        }
        foreach ($completedCafeOrders as $o) {
            if ($o->payment_method) {
                $allPayments->push(['method' => $o->payment_method, 'amount' => $o->final_amount]);
            }
        }

        $paymentBreakdown = $allPayments->groupBy('method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        });

        // المصروفات
        $totalExpenses = Expense::forDate($date)->sum('amount');
        $expenses = Expense::forDate($date)->with('user')->get();

        // صافي الربح
        $netProfit = $totalRevenue - $totalExpenses;

        return [
            'date'              => $date->format('Y-m-d'),
            'date_formatted'    => $date->translatedFormat('l j F Y'),
            'sessions_count'    => $closedSessions->count(),
            'cafe_orders_count' => $completedCafeOrders->count(),
            'ps_revenue'        => round($psRevenue, 2),
            'cafe_revenue'      => round($cafeRevenue, 2),
            'total_revenue'     => round($totalRevenue, 2),
            'total_discount'    => round($totalDiscount, 2),
            'total_expenses'    => round($totalExpenses, 2),
            'net_profit'        => round($netProfit, 2),
            'payment_breakdown' => $paymentBreakdown,
            'expenses'          => $expenses,
        ];
    }

    /**
     * الحصول على إحصائيات لوحة التحكم
     */
    public function getDashboardStats(): array
    {
        $today = Carbon::today();

        // جلسات اليوم
        $todaySessions = GameSession::whereDate('created_at', $today)->get();
        $activeSessions = GameSession::active()->with('device', 'user')->get();

        // طلبات الكافيه المفتوحة والمكتملة اليوم
        $openCafeOrders = CafeOrder::open()->with('user', 'items.product')->get();
        $completedCafeOrdersToday = CafeOrder::completed()->whereDate('updated_at', $today)->get();

        // إيرادات اليوم
        $todayPsRevenue = GameSession::closed()
            ->whereDate('end_time', $today)
            ->sum('playstation_total');

        $sessionCafeRevenue = GameSession::closed()
            ->whereDate('end_time', $today)
            ->sum('cafe_total');

        $independentCafeRevenue = $completedCafeOrdersToday->sum('final_amount');
        $todayCafeRevenue = $sessionCafeRevenue + $independentCafeRevenue;

        $todayRevenue = $todayPsRevenue + $todayCafeRevenue;

        // مصروفات اليوم
        $todayExpenses = Expense::forDate($today)->sum('amount');

        // الأجهزة
        $availableDevices = \App\Models\Device::available()->count();
        $occupiedDevices = \App\Models\Device::occupied()->count();

        return [
            'today_revenue'     => round($todayRevenue, 2),
            'today_ps_revenue'  => round($todayPsRevenue, 2),
            'today_cafe_revenue'=> round($todayCafeRevenue, 2),
            'today_expenses'    => round($todayExpenses, 2),
            'net_revenue'       => round($todayRevenue - $todayExpenses, 2),
            'today_sessions'    => $todaySessions->count(),
            'active_sessions'   => $activeSessions,
            'open_cafe_orders'  => $openCafeOrders,
            'available_devices' => $availableDevices,
            'occupied_devices'  => $occupiedDevices,
        ];
    }

    /**
     * اتجاه الإيرادات خلال آخر 7 أيام (للرسم البياني)
     *
     * @return array ['labels' => [...], 'ps' => [...], 'cafe' => [...], 'expenses' => [...]]
     */
    public function getWeeklyTrend(): array
    {
        $labels = [];
        $psData = [];
        $cafeData = [];
        $expensesData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->translatedFormat('D j/n');

            $psRev = (float) GameSession::closed()
                ->whereDate('end_time', $date)
                ->sum('playstation_total');

            $sessionCafe = (float) GameSession::closed()
                ->whereDate('end_time', $date)
                ->sum('cafe_total');

            $independentCafe = (float) CafeOrder::completed()
                ->whereDate('updated_at', $date)
                ->sum('final_amount');

            $psData[] = round($psRev, 2);
            $cafeData[] = round($sessionCafe + $independentCafe, 2);
            $expensesData[] = round((float) Expense::forDate($date)->sum('amount'), 2);
        }

        return [
            'labels' => $labels,
            'ps' => $psData,
            'cafe' => $cafeData,
            'expenses' => $expensesData,
        ];
    }

    /**
     * المنتجات الأكثر مبيعاً لتاريخ معين (أعلى 10)
     */
    public function getTopSellingProducts(?string $date = null): array
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        // منتجات من جلسات PS
        $sessionProducts = DB::table('session_items')
            ->join('game_sessions', 'session_items.game_session_id', '=', 'game_sessions.id')
            ->join('products', 'session_items.product_id', '=', 'products.id')
            ->where('game_sessions.status', 'closed')
            ->whereDate('game_sessions.end_time', $date)
            ->select('products.name', DB::raw('SUM(session_items.quantity) as total_qty'), DB::raw('SUM(session_items.total_price) as total_revenue'))
            ->groupBy('products.name');

        // منتجات من طلبات الكافيه المستقلة
        $cafeProducts = DB::table('cafe_order_items')
            ->join('cafe_orders', 'cafe_order_items.cafe_order_id', '=', 'cafe_orders.id')
            ->join('products', 'cafe_order_items.product_id', '=', 'products.id')
            ->where('cafe_orders.status', 'completed')
            ->whereDate('cafe_orders.updated_at', $date)
            ->select('products.name', DB::raw('SUM(cafe_order_items.quantity) as total_qty'), DB::raw('SUM(cafe_order_items.total_price) as total_revenue'))
            ->groupBy('products.name');

        // دمج النتائج
        $combined = $sessionProducts->unionAll($cafeProducts)->get();

        $merged = $combined->groupBy('name')->map(function ($group) {
            return [
                'name' => $group->first()->name,
                'total_qty' => $group->sum('total_qty'),
                'total_revenue' => $group->sum('total_revenue'),
            ];
        })->sortByDesc('total_qty')->take(10)->values()->toArray();

        return $merged;
    }

    /**
     * توزيع الإيرادات حسب ساعات اليوم (للرسم البياني)
     */
    public function getHourlyBreakdown(?string $date = null): array
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $hours = [];
        for ($h = 0; $h < 24; $h++) {
            $hours[$h] = 0;
        }

        // جلسات PS المغلقة
        $sessions = GameSession::closed()
            ->whereDate('end_time', $date)
            ->get();

        foreach ($sessions as $s) {
            $hour = (int) $s->end_time->format('G');
            $hours[$hour] += (float) $s->final_amount;
        }

        // طلبات كافيه مكتملة
        $orders = CafeOrder::completed()
            ->whereDate('updated_at', $date)
            ->get();

        foreach ($orders as $o) {
            $hour = (int) $o->updated_at->format('G');
            $hours[$hour] += (float) $o->final_amount;
        }

        // تنسيق ساعات العرض
        $labels = [];
        $data = [];
        foreach ($hours as $h => $amount) {
            if ($amount > 0) {
                $labels[] = sprintf('%02d:00', $h);
                $data[] = round($amount, 2);
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
