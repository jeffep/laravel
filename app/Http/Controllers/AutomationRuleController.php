<?php

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutomationRuleController extends Controller
{
    public function index()
    {
        $rules = AutomationRule::with('actionDevice')->get();
        return view('automation_rules.index', compact('rules'));
    }

    public function create()
    {
        $devices = Device::all();
        $locations = DB::table('sensors')->select('location')->distinct()->pluck('location');
        return view('automation_rules.create', compact('devices', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required_if:condition_type,temperature|string|max:255',
            'condition_type' => 'required|in:temperature,device_status',
            'condition_compare' => 'required|string|max:255',
            'action_device_id' => 'required|exists:devices,id',
            'action' => 'required|in:turn_on,turn_off',
        ]);

        AutomationRule::create($validated);

        return redirect()->route('automation_rules.index')->with('success', 'Rule added successfully.');
    }

    public function edit(AutomationRule $automationRule)
    {
        $devices = Device::all();
        $locations = DB::table('sensors')->select('location')->distinct()->pluck('location');
        return view('automation_rules.edit', compact('automationRule', 'devices', 'locations'));
    }

    public function update(Request $request, AutomationRule $automationRule)
    {
        $validated = $request->validate([
            'location' => 'required_if:condition_type,temperature|string|max:255',
            'condition_type' => 'required|in:temperature,device_status',
            'condition_compare' => 'required|string|max:255',
            'action_device_id' => 'required|exists:devices,id',
            'action' => 'required|in:turn_on,turn_off',
        ]);

        $automationRule->update($validated);

        return redirect()->route('automation_rules.index')->with('success', 'Rule updated successfully.');
    }

    public function destroy(AutomationRule $automationRule)
    {
        $automationRule->delete();
        return redirect()->route('automation_rules.index')->with('success', 'Rule deleted successfully.');
    }
}
