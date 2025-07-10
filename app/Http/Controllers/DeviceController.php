<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the devices.
     */
    public function index()
    {
        $devices = Device::all();
        return view('devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new device.
     */
    public function create()
    {
        return view('devices.create');
    }

    /**
     * Store a newly created device in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|ip',
            'status_endpoint' => 'required|string|max:255',
            'report_url' => 'required|url',
            'action_on' => 'required|url',
            'action_off' => 'required|url',
        ]);

        Device::create($validated);

        return redirect()->route('devices.index')->with('success', 'Device added successfully.');
    }

    /**
     * Show the form for editing the specified device.
     */
    public function edit(Device $device)
    {
        return view('devices.edit', compact('device'));
    }

    /**
     * Update the specified device in storage.
     */
    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|ip',
            'status_endpoint' => 'required|string|max:255',
            'report_url' => 'required|url',
            'action_on' => 'required|url',
            'action_off' => 'required|url',
        ]);

        $device->update($validated);

        return redirect()->route('devices.index')->with('success', 'Device updated successfully.');
    }

    /**
     * Remove the specified device from storage.
     */
    public function destroy(Device $device)
    {
        $device->delete();
        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }
}
