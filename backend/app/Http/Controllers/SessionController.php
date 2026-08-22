<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSessionItemRequest;
use App\Http\Requests\CloseSessionRequest;
use App\Http\Requests\StartSessionRequest;
use App\Models\Category;
use App\Models\Device;
use App\Models\GameSession;
use App\Models\Product;
use App\Services\SessionService;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * التحكم في إدارة الجلسات - البدء، إضافة منتجات، الإغلاق، الإلغاء
 */
class SessionController extends Controller
{
    public function __construct(
        protected SessionService $sessionService,
    ) {}

    /**
     * عرض جميع الجلسات
     */
    public function index(Request $request)
    {
        $query = GameSession::with(['device', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->paginate(20);
        return view('sessions.index', compact('sessions'));
    }

    /**
     * عرض الجلسات النشطة فقط (الشاشة الرئيسية للكاشير)
     */
    public function active()
    {
        $activeSessions = GameSession::active()
            ->with(['device', 'user', 'items.product'])
            ->latest()
            ->get();

        $availableDevices = Device::available()->get();

        return view('sessions.active', compact('activeSessions', 'availableDevices'));
    }

    /**
     * صفحة بدء جلسة جديدة
     */
    public function create()
    {
        $devices = Device::available()->orderBy('name')->get();
        return view('sessions.create', compact('devices'));
    }

    /**
     * بدء جلسة جديدة
     */
    public function store(StartSessionRequest $request)
    {
        try {
            $session = $this->sessionService->startSession($request->validated());
            return redirect()->route('pos.index', ['tab' => 'devices', 'selected' => 'session-'.$session->id])
                ->with('success', __('messages.success.session_started'));
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['device_id' => $e->getMessage()])->withInput();
        }
    }

    /**
     * عرض تفاصيل جلسة (مع إمكانية إضافة منتجات)
     */
    public function show(GameSession $gameSession)
    {
        $gameSession->load(['device', 'user', 'items.product']);
        $categories = Category::with('products')->orderBy('name')->get();
        $session = $gameSession;

        return view('sessions.show', compact('session', 'categories'));
    }

    /**
     * إضافة منتج إلى الجلسة
     */
    public function addItem(AddSessionItemRequest $request, GameSession $gameSession)
    {
        try {
            $this->sessionService->addItemToSession(
                $gameSession,
                $request->validated()['product_id'],
                $request->validated()['quantity']
            );
            return redirect()->route('pos.index', ['tab' => 'devices', 'selected' => 'session-'.$gameSession->id])
                ->with('success', __('messages.success.item_added'));
        } catch (InvalidArgumentException $e) {
            return redirect()->route('pos.index', ['tab' => 'devices', 'selected' => 'session-'.$gameSession->id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * تحديث عدد الأذرع النشطة في جلسة قائمة
     */
    public function updateControllers(Request $request, GameSession $gameSession)
    {
        $validated = $request->validate([
            'active_controllers' => 'required|integer|min:1|max:8',
        ]);

        try {
            $this->sessionService->updateActiveControllers(
                $gameSession,
                (int) $validated['active_controllers']
            );
            return redirect()->route('pos.index', ['selected' => 'session-'.$gameSession->id])
                ->with('success', __('messages.success.controllers_updated'));
        } catch (InvalidArgumentException $e) {
            return redirect()->route('pos.index', ['selected' => 'session-'.$gameSession->id])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * إزالة عنصر من الجلسة
     */
    public function removeItem(GameSession $gameSession, int $itemId)
    {
        try {
            $this->sessionService->removeItemFromSession($gameSession, $itemId);
            return redirect()->route('pos.index', ['tab' => 'devices', 'selected' => 'session-'.$gameSession->id])
                ->with('success', __('messages.success.item_removed'));
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * صفحة الدفع والإغلاق
     */
    public function checkout(GameSession $gameSession)
    {
        $gameSession->load(['device', 'user', 'items.product']);
        $session = $gameSession;
        return view('sessions.checkout', compact('session'));
    }

    /**
     * إغلاق الجلسة ومعالجة الدفع
     */
    public function close(CloseSessionRequest $request, GameSession $gameSession)
    {
        try {
            $session = $this->sessionService->closeSession($gameSession, $request->validated());
            return redirect()->route('sessions.invoice', $session)
                ->with('success', __('messages.success.session_closed'));
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * عرض الفاتورة
     */
    public function invoice(GameSession $gameSession)
    {
        $gameSession->load(['device', 'user', 'items.product']);
        $session = $gameSession;
        return view('sessions.invoice', compact('session'));
    }

    /**
     * إلغاء الجلسة
     */
    public function cancel(GameSession $gameSession)
    {
        try {
            $this->sessionService->cancelSession($gameSession);
            return redirect()->route('pos.index', ['tab' => 'devices'])
                ->with('success', __('messages.success.session_cancelled'));
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
