<?php

namespace App\Console\Commands;

use App\Models\AutomationRule;
use App\Models\Sensor;
use App\Models\SensorData;
use App\Models\Setting;
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

        $verbose = Setting::where('key', 'temperature_monitor_verbose')->first()->value ?? '0';

        $rules = AutomationRule::where('condition_type', 'temperature')
            ->where('active', true)
            ->with('actionDevice')
            ->get();

        if ($rules->isEmpty()) {
            Log::channel('temperature_monitor')->warning('No active temperature rules found');
            return;
        }

        $summary = ['processed' => 0, 'actions' => [], 'errors' => []];

        foreach ($rules as $rule) {
            $summary['processed']++;

            $sensor = Sensor::where('location', $rule->location)->first();
            if (!$sensor) {
                $summary['errors'][] = "No sensor found for location {$rule->location}";
                continue;
            }

            $latest = SensorData::where('sensor_id', $sensor->id)
                ->where('title', 'temperature')
                ->orderBy('time', 'desc')
                ->first();

            if (!$latest) {
                $summary['errors'][] = "No temperature data for location {$rule->location} (sensor_id={$sensor->id})";
                continue;
            }

            // Parse condition (e.g., ">84")
            preg_match('/([><=]+)(\d+\.?\d*)/', $rule->condition_compare, $matches);
            if (count($matches) < 3) {
                $summary['errors'][] = "Invalid condition format for rule ID {$rule->id}: {$rule->condition_compare}";
                continue;
            }

            $operator = $matches[1];
            $threshold = (float) $matches[2];
            $temperature = $latest->value;

            // Log detailed condition evaluation only in verbose mode
            if ($verbose) {
                Log::channel('temperature_monitor')->debug("Evaluating rule ID {$rule->id}: {$temperature} {$operator} {$threshold} at {$rule->location}");
            }

            $shouldAct = false;
            if ($operator == '>' && $temperature > $threshold) {
                $shouldAct = true;
            } elseif ($operator == '<' && $temperature < $threshold) {
                $shouldAct = true;
            } elseif ($operator == '=' && $temperature == $threshold) {
                $shouldAct = true;
            }

            if ($shouldAct) {
                $url = $rule->action == 'turn_on' ? $rule->actionDevice->action_on : $rule->actionDevice->action_off;
                try {
                    Http::get($url);
                    $summary['actions'][] = "Executed {$rule->action} on {$rule->actionDevice->name} (Temp: {$temperature} at {$rule->location})";
                } catch (\Exception $e) {
                    $summary['errors'][] = "Failed to execute {$rule->action} on {$rule->actionDevice->name}: {$e->getMessage()}";
                }
            }
        }

        // Log summary
        Log::channel('temperature_monitor')->info("Processed {$summary['processed']} rules. Actions taken: " . count($summary['actions']));
        if (!empty($summary['actions']) && $verbose) {
            Log::channel('temperature_monitor')->info('Actions: ' . implode('; ', $summary['actions']));
        }
        if (!empty($summary['errors'])) {
            Log::channel('temperature_monitor')->warning('Errors: ' . implode('; ', $summary['errors']));
        }

        Log::channel('temperature_monitor')->info('MonitorTemperature command completed at ' . now());
    }
}
