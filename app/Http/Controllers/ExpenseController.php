<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * التحكم في إدارة المصروفات اليومية
 */
class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $expenses = Expense::with('user')
            ->forDate($date)
            ->latest()
            ->get();

        $totalExpenses = $expenses->sum('amount');

        return view('expenses.index', compact('expenses', 'date', 'totalExpenses'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(StoreExpenseRequest $request)
    {
        Expense::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('expenses.index')
            ->with('success', __('messages.success.created'));
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')
            ->with('success', __('messages.success.deleted'));
    }
}
