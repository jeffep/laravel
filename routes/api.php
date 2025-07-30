// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WattageController;

Route::post('/log_wattage', [WattageChartController::class, 'logWattage'])->name('logWattage');

