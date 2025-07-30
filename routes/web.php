<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AutomationRuleController;
use App\Http\Controllers\ControlPageController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FertilizerController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\LimitedController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchwabController;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\ShellyController;
use App\Http\Controllers\SprinklerController;
use App\Http\Controllers\TemperaturesSetupController;
use App\Http\Controllers\TouchController;
use App\Http\Controllers\TrugreenController;
use App\Http\Controllers\WattageChartController;
use App\Http\Controllers\WebcamController;
use App\Http\Controllers\AutomationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| Routes accessible without authentication.
|
*/

Route::get('/', fn () => view('landing'))->name('welcome');

Route::get('/schwab-callback', [SchwabController::class, 'handleCallback'])->name('schwab.callback');
Route::get('/test-schwab', [SchwabController::class, 'testApi'])->name('test.schwab');
Route::get('/test3', fn () => view('test3'))->name('test3');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|
| Routes requiring authentication, grouped by user type or feature.
|
*/

Route::middleware('auth')->group(function () {
    // General Dashboards
    Route::get('/dashboard', [ControlPageController::class, 'home'])->name('dashboard');
    Route::get('/admin-dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/limited-dashboard', [LimitedController::class, 'index'])->name('limited.dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Control Page Features
    Route::get('/home', [ControlPageController::class, 'home'])->name('home');
    Route::post('/generateGoAccessReport', [ControlPageController::class, 'generateGoAccessReport'])->name('generateGoAccessReport');
    Route::get('/temperatures', [ControlPageController::class, 'temperatures'])->name('temperatures');
    Route::get('/sounds', [ControlPageController::class, 'sounds'])->name('sounds');
    Route::get('/sprinkler2', [ControlPageController::class, 'sprinkler2'])->name('sprinkler2');

    // Shelly Devices
    Route::get('/shelly_status', [ShellyController::class, 'shelly_status'])->name('shelly_status');
    Route::post('/shelly/toggle', [ShellyController::class, 'toggle'])->name('shelly.toggle');
    Route::get('/shellyDevice/{id}', [ShellyController::class, 'showDevice'])->name('shellyDevice')->where('id', '[1-4]');
    Route::get('/shellyLight/{id}', [ShellyController::class, 'shellyLight'])->name('shellyLight')->where('id', '[1-6]');

    // Sprinkler and Trugreen
    Route::get('/control', [ControlPageController::class, 'showControlPage'])->name('sprinkler');
    Route::post('/sprinkler/control', [SprinklerController::class, 'controlZone'])->name('sprinkler.controlZone');
    Route::get('/trugreen', [TrugreenController::class, 'trugreenFiles'])->name('trugreen');
    Route::get('/trugreen/{directory}/{file}', [TrugreenController::class, 'showPDF'])->name('trugreen.showPDF');
    Route::get('/fertilizer', [FertilizerController::class, 'index'])->name('fertilizer');

    // Sensors and House
    Route::get('/sensor-data', [SensorDataController::class, 'showSensorData'])->name('sensor-data');
    Route::get('/api/sensor-data', [SensorDataController::class, 'getSensorData'])->name('api.sensor-data');
    Route::get('/house', [HouseController::class, 'index'])->name('house');

    // Webcams
    Route::get('/webcams', [WebcamController::class, 'index'])->name('webcams');
    Route::get('/webcam/{name}', [WebcamController::class, 'show'])->name('webcam.show');

    // Events and Calendar
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::get('/calendar', fn () => view('calendar'))->name('calendar');

    // Schwab Integration
    Route::get('/schwab-auth', [SchwabController::class, 'redirectToSchwab'])->name('schwab.auth');
    Route::get('/schwab-code', [SchwabController::class, 'showCodeForm'])->name('schwab.code.form');
    Route::post('/schwab-code', [SchwabController::class, 'handleCode'])->name('schwab.code.handle');
    Route::get('/corn-prices', [SchwabController::class, 'showCornPrices'])->name('corn.prices');

    // Devices and Automations
    Route::resource('devices', DeviceController::class);

    // Resource routes for automation_rules (handled by AutomationRuleController)
    Route::resource('automation_rules', AutomationRuleController::class);

    // Custom route for toggling active status (handled by AutomationRuleController for consistency)
    Route::post('/automation_rules/toggle-active', [AutomationRuleController::class, 'toggleActive'])->name('automation.toggle-active');

    // Custom route for toggling verbose mode (handled by AutomationRuleController)
    Route::post('/automation_rules/toggle-verbose', [AutomationRuleController::class, 'toggleVerbose'])->name('automation_rules.toggleVerbose');

    // Custom route for Shelly-specific automation actions (handled by AutomationController)
    Route::post('/automation/store', [AutomationController::class, 'store'])->name('automation.store');

    // Temperatures Setup
    Route::get('/temperatures_setup', [TemperaturesSetupController::class, 'temperatures_setup'])->name('temperatures_setup');
    Route::post('/temperatures_save', [TemperaturesSetupController::class, 'temperatures_save'])->name('temperatures_save');

    // Wattage Chart
    Route::get('/wattage_chart', [WattageChartController::class, 'index'])->name('wattage_chart');
});

/*
|--------------------------------------------------------------------------
| Touch Panel Routes (GarageTablet and FrontTablet)
|--------------------------------------------------------------------------
|
| Routes shared by garagetablet and fronttablet logins, accessible only to authenticated users.
|
*/

Route::middleware(['auth'])->prefix('touch')->name('touch.')->group(function () {
    Route::get('/dashboard', [TouchController::class, 'dashboard'])->name('dashboard');
    Route::get('/switches', [TouchController::class, 'switches'])->name('switches');
    Route::get('/cameras', [TouchController::class, 'cameras'])->name('cameras');
    Route::get('/clock', [TouchController::class, 'clock'])->name('clock');
    Route::get('/corn-futures', [TouchController::class, 'cornFutures'])->name('corn-futures');
    Route::get('/temperatures', [TouchController::class, 'temperatures'])->name('temperatures');
    Route::get('/slideshow', [TouchController::class, 'slideshow'])->name('slideshow');
    Route::get('/calendar', [TouchController::class, 'calendar'])->name('calendar');
});

require __DIR__ . '/auth.php';
