<?php

namespace App\Http\Controllers;

use App\Models\AutomationRule;
use App\Models\Device;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutomationRuleController extends Controller
{
    public function index()
    {
        $rules = AutomationRule::with('actionDevice')->get();
        $verboseMode = Setting::where('key', 'temperature_monitor_verbose')->first()->value ?? '0';
        return view('automation_rules.index', compact('rules', 'verboseMode'));
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
            'active' => 'required|boolean',
        ]);

        AutomationRule::create($validated);

        return redirect()->route('automation_rules.index')->with('success', 'Rule added successfully.');
    }

    public function update(Request $request, AutomationRule $automationRule)
    {
        $validated = $request->validate([
            'location' => 'required_if:condition_type,temperature|string|max:255',
            'condition_type' => 'required|in:temperature,device_status',
            'condition_compare' => 'required|string|max:255',
            'action_device_id' => 'required|exists:devices,id',
            'action' => 'required|in:turn_on,turn_off',
            'active' => 'required|boolean',
        ]);

        $automationRule->update($validated);

        return redirect()->route('automation_rules.index')->with('success', 'Rule updated successfully.');
    }

    public function edit(AutomationRule $automationRule)
    {
        $devices = Device::all();
        $locations = DB::table('sensors')->select('location')->distinct()->pluck('location');
        return view('automation_rules.edit', compact('automationRule', 'devices', 'locations'));
    }

    public function destroy(AutomationRule $automationRule)
    {
        $automationRule->delete();
        return redirect()->route('automation_rules.index')->with('success', 'Rule deleted successfully.');
    }

    public function toggleVerbose(Request $request)
    {
        $verboseMode = $request->has('verbose_mode') ? 1 : 0; // Default to 0 if unchecked
        Setting::updateOrCreate(
            ['key' => 'temperature_monitor_verbose'],
            ['value' => $verboseMode]
        );
        return redirect()->route('automation_rules.index')->with('success', 'Verbose mode updated successfully.');
    }

}
