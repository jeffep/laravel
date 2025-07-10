<?php

namespace App\Console\Commands;

use App\Models\AutomationRule;
use App\Models\SensorData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonitorTemperature extends Command
{
    protected $signature = 'monitor:temperature';
    protected $description = 'Monitor temperature readings and apply automation rules';

    public function handle()
    {
        Log::channel('temperature_monitor')->info('MonitorTemperature command started at ' . now());

        $rules = AutomationRule::where('condition_type', 'temperature')
            ->with('actionDevice')
            ->get();

        Log::channel('temperature_monitor')->info("Found {$rules->count()} temperature rules");

        if ($rules->isEmpty()) {
            Log::channel('temperature_monitor')->warning('No temperature rules found');
        }

        foreach ($rules as $rule) {
            Log::channel('temperature_monitor')->info("Processing rule ID {$rule->id} for location {$rule->location}");

            // Get the latest temperature for the location
            $latest = SensorData::where('location', $rule->location)
                ->orderBy('time', 'desc')
                ->first();

            if (!$latest) {
                Log::channel('temperature_monitor')->warning("No temperature data for location {$rule->location}");
                continue;
            }

            Log::channel('temperature_monitor')->info("Latest temperature for {$rule->location}: {$latest->temperature} at {$latest->time}");

            // Parse condition (e.g., ">84")
            preg_match('/([><=]+)(\d+\.?\d*)/', $rule->condition_compare, $matches);
            if (count($matches) < 3) {
                Log::channel('temperature_monitor')->error("Invalid condition format for rule ID {$rule->id}: {$rule->condition_compare}");
                continue;
            }

            $operator = $matches[1];
            $threshold = (float) $matches[2];
            $temperature = $latest->temperature;

            Log::channel('temperature_monitor')->info("Evaluating condition: {$temperature} {$operator} {$threshold}");

            $shouldAct = false;
            if ($operator == '>' && $temperature > $threshold) {
                $shouldAct = true;
            } elseif ($operator == '<' && $temperature < $threshold) {
                $shouldAct = true;
            } elseif ($operator == '=' && $temperature == $threshold) {
                $shouldAct = true;
            }

            Log::channel('temperature_monitor')->info("Rule ID {$rule->id} shouldAct: " . ($shouldAct ? 'true' : 'false'));

            if ($shouldAct) {
                Log::channel('temperature_monitor')->info("Attempting {$rule->action} on {$rule->actionDevice->name} (Temp: {$temperature} at {$rule->location})");

                // Execute the action
                $url = $rule->action == 'turn_on' ? $rule->actionDevice->action_on : $rule->actionDevice->action_off;
                try {
                    Log::channel('temperature_monitor')->info("Sending HTTP request to URL: {$url}");
                    Http::get($url);
                    Log::channel('temperature_monitor')->info("Successfully executed {$rule->action} on {$rule->actionDevice->name} (Temp: {$temperature} at {$rule->location})");
                } catch (\Exception $e) {
                    Log::channel('temperature_monitor')->error("Failed to execute {$rule->action} on {$rule->actionDevice->name}: {$e->getMessage()}");
                }
            }
        }

        Log::channel('temperature_monitor')->info('MonitorTemperature command completed at ' . now());
    }
}
