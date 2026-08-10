<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceRequest;
use App\Models\Device;

/**
 * التحكم في إدارة الأجهزة (CRUD)
 */
class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::orderBy('name')->get();
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(StoreDeviceRequest $request)
    {
        Device::create($request->validated());
        return redirect()->route('devices.index')
            ->with('success', __('messages.success.created'));
    }

    public function edit(Device $device)
    {
        return view('devices.edit', compact('device'));
    }

    public function update(StoreDeviceRequest $request, Device $device)
    {
        $device->update($request->validated());
        return redirect()->route('devices.index')
            ->with('success', __('messages.success.updated'));
    }

    public function destroy(Device $device)
    {
        // منع الحذف إذا يوجد جلسات مرتبطة
        if ($device->gameSessions()->exists()) {
            return back()->with('error', __('messages.errors.delete_has_sessions'));
        }

        $device->delete();
        return redirect()->route('devices.index')
            ->with('success', __('messages.success.deleted'));
    }
}
