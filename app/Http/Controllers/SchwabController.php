<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CornPrice;
use Illuminate\Support\Facades\Artisan;

class SchwabController extends Controller
{
    protected $callbackUrl;

    public function __construct()
    {
        $this->callbackUrl = config('schwab.callback_url', 'https://192.168.87.99/schwab-callback');
    }

private function refreshToken()
{
    $refreshToken = config('schwab.refresh_token');
    Log::info("Refreshing Token - Old Refresh Token: " . substr($refreshToken, 0, 10) . '...', [
        'full_length' => strlen($refreshToken),
        'first_10' => substr($refreshToken, 0, 10),
        'last_10' => substr($refreshToken, -10),
    ]);

    $response = Http::withBasicAuth(config('schwab.app_key'), config('schwab.app_secret'))
        ->asForm()
        ->post('https://api.schwabapi.com/v1/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'redirect_uri' => config('schwab.callback_url'),
        ]);

    Log::info("Refresh Token Response", [
        'status' => $response->status(),
        'body' => $response->body(),
    ]);

    if ($response->successful()) {
        $data = $response->json();
        $newAccessToken = $data['access_token'];
        $newRefreshToken = $data['refresh_token'];
        Log::info("New Access Token: " . substr($newAccessToken, 0, 10) . '...', [
            'access_token_length' => strlen($newAccessToken),
            'refresh_token_length' => strlen($newRefreshToken),
        ]);

        // Update config
        config(['schwab.token' => $newAccessToken]);
        config(['schwab.refresh_token' => $newRefreshToken]);

        // Persist to .env file
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        $envContent = preg_replace(
            '/^SCHWAB_TOKEN=.*/m',
            'SCHWAB_TOKEN=' . $newAccessToken,
            $envContent
        );
        $envContent = preg_replace(
            '/^SCHWAB_REFRESH_TOKEN=.*/m',
            'SCHWAB_REFRESH_TOKEN=' . $newRefreshToken,
            $envContent
        );
        file_put_contents($envPath, $envContent);

        // Clear and cache config
        //Artisan::call('config:cache');
        Log::info("Config cache cleared successfully");

        // Sync to Pi
        $this->updatePiEnv($newAccessToken, $newRefreshToken);
        return true;
    } else {
        Log::error("Refresh Token Failed", [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
        return false;
    }
}



    private function updatePiEnv($accessToken, $refreshToken)
  {
    \Log::info('Updating Pi .env');
    $env_content = "APP_KEY=" . config('schwab.app_key') . "\n" .
                   "APP_SECRET=" . config('schwab.app_secret') . "\n" .
                   "ACCESS_TOKEN=$accessToken\n" .
                   "REFRESH_TOKEN=$refreshToken\n";
    $temp_file = '/tmp/schwab.env';
    file_put_contents($temp_file, $env_content);
    $pi_user = 'jeffrey';
    $pi_host = '192.168.87.130';
    $pi_env_path = '/home/jeffrey/corn-screensaver/.env';
    $scp_command = "scp $temp_file $pi_user@$pi_host:$pi_env_path";
    exec($scp_command . ' 2>&1', $output, $return_code);
    unlink($temp_file);

    if ($return_code !== 0) {
        \Log::error('SCP to Pi failed: ' . implode(', ', $output));
    } else {
        \Log::info('Pi .env updated', [
            'access_token' => substr($accessToken, 0, 10) . '...',
            'refresh_token' => substr($refreshToken, 0, 10) . '...'
        ]);
    }
  }
 
    public function testApi()
    {
        $token = config('schwab.token');
        Log::info("Test API Start - Initial Token: " . ($token ? substr($token, 0, 10) . '...' : 'null'));

        if (!$token || !$this->refreshToken()) {
            Log::warning("Token missing or refresh failed");
            return "Please authenticate with Schwab.";
        }

        try {
            $response = Http::withHeaders(['Authorization' => "Bearer {$token}"])
                ->get('https://api.schwabapi.com/marketdata/v1/quotes?symbols=/ZCZ25');

            Log::info("Corn Quote Response", [
                'status' => $response->status(),
                'body' => $response->body(),
                'token_used' => substr($token, 0, 10) . '...'
            ]);

            if ($response->status() === 401) {
                Log::warning("401 Detected - Forcing refresh");
                if ($this->refreshToken()) {
                    $token = config('schwab.token');
                    $response = Http::withHeaders(['Authorization' => "Bearer {$token}"])
                        ->get('https://api.schwabapi.com/marketdata/v1/quotes?symbols=/ZCZ25');
                    Log::info("Retry Corn Quote Response", [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'token_used' => substr($token, 0, 10) . '...'
                    ]);
                }
            }

            if ($response->status() === 401) {
                Log::error("401 Unauthorized - Token invalid");
                return "Authentication failed.";
            } elseif ($response->failed()) {
                Log::error("Request Failed - Status: " . $response->status());
                return "Request failed: " . $response->status();
            }

            return $response->body();
        } catch (\Exception $e) {
            Log::error("Test API Exception", [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return "Error: " . $e->getMessage();
        }
    }

    public function redirectToSchwab()
    {
        $authUrl = "https://api.schwabapi.com/v1/oauth/authorize?client_id=" . config('schwab.app_key') . "&redirect_uri=" . $this->callbackUrl;
        return view('schwab-auth', ['authUrl' => $authUrl]);
    }

    public function handleCallback(Request $request)
{
    \Log::info('Schwab callback: Started', ['query' => $request->query()]);
    $code = $request->query('code');
    if (!$code) {
        \Log::error('Schwab callback: No code received');
        return response("Error: No code received.", 400);
    }

    \Log::info('Schwab callback: Sending token request', ['code' => substr($code, 0, 10) . '...']);
    $response = Http::withBasicAuth(config('schwab.app_key'), config('schwab.app_secret'))
        ->asForm()
        ->post('https://api.schwabapi.com/v1/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->callbackUrl,
        ]);

    if ($response->failed()) {
        \Log::error('Schwab callback: Token request failed', ['body' => $response->body()]);
        return response("Token request failed: " . $response->body(), 500);
    }

    $data = $response->json();
    \Log::info('Schwab callback: Tokens received', [
        'access_token' => substr($data['access_token'], 0, 10) . '...',
        'refresh_token' => substr($data['refresh_token'], 0, 10) . '...'
    ]);

    // Update config
    $config = config('schwab');
    $config['token'] = $data['access_token'];
    $config['refresh_token'] = $data['refresh_token'];
    file_put_contents(config_path('schwab.php'), '<?php return ' . var_export($config, true) . ';');
    \Log::info('Schwab callback: Config updated');

    // Clear config cache
    \Artisan::call('config:clear');
    \Log::info('Schwab callback: Config cache cleared');

    return redirect()->route('corn.prices')->with('success', 'Token saved!');
  }
    public function showCodeForm()
    {
        return view('schwab-code');
    }

    public function handleCode(Request $request)
    {
        $url = $request->input('code');
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $code = $params['code'] ?? null;

        if (!$code) {
            Log::error('Schwab code form: No code found');
            return redirect()->route('schwab.code.form')->with('error', 'No code found in URL.');
        }

        $response = Http::withBasicAuth(config('schwab.app_key'), config('schwab.app_secret'))
            ->asForm()
            ->post('https://api.schwabapi.com/v1/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->callbackUrl,
            ]);

        if ($response->failed()) {
            Log::error('Token request failed: ' . $response->body());
            return redirect()->route('schwab.code.form')->with('error', 'Token request failed: ' . $response->body());
        }

        $data = $response->json();
        config(['schwab.token' => $data['access_token']]);
        config(['schwab.refresh_token' => $data['refresh_token']]);
        $this->updatePiEnv($data['access_token'], $data['refresh_token']);
        Log::info('Schwab code form: Tokens updated');

        return redirect()->route('corn.prices')->with('success', 'Token saved!');
    }

  public function showCornPrices()
{
    $token = config('schwab.token');
    \Log::info('Show Corn Prices - Start', [
        'token' => $token ? substr($token, 0, 10) . '...' : 'null',
        'schwab_config' => config('schwab')
    ]);

    if (!$token || !$this->refreshToken()) {
        \Log::warning('Token missing or refresh failed');
        return redirect()->route('schwab.auth')->with('error', 'Please authenticate with Schwab first.');
    }

    \Log::info('Show Corn Prices - Post-refresh token', [
        'token' => $token ? substr($token, 0, 10) . '...' : 'null'
    ]);

    \Log::info('Show Corn Prices - Fetching futures');
    $futuresResponse = Http::withHeaders(['Authorization' => "Bearer {$token}"])
        ->timeout(10) // Add timeout to prevent hanging
        ->get('https://api.schwabapi.com/marketdata/v1/quotes?symbols=/ZCZ25');

    \Log::info('Show Corn Prices - Futures Response', [
        'status' => $futuresResponse->status(),
        'body' => $futuresResponse->body()
    ]);

    if ($futuresResponse->status() === 401) {
        \Log::warning('Futures 401 - Forcing refresh');
        if ($this->refreshToken()) {
            $token = config('schwab.token');
            \Log::info('Show Corn Prices - Retry fetch after 401');
            $futuresResponse = Http::withHeaders(['Authorization' => "Bearer {$token}"])
                ->timeout(10)
                ->get('https://api.schwabapi.com/marketdata/v1/quotes?symbols=/ZCZ25');
            \Log::info('Show Corn Prices - Retry Response', [
                'status' => $futuresResponse->status(),
                'body' => $futuresResponse->body()
            ]);
        }
    }

    $futuresPrice = $futuresResponse->failed() ? 'N/A' : ($futuresResponse->json()['/ZCZ25']['quote']['lastPrice'] ?? 'N/A');
    \Log::info('Show Corn Prices - Parsed Price', ['futuresPrice' => $futuresPrice]);

    \Log::info('Show Corn Prices - Updating DB');
    CornPrice::updateOrCreate(
        ['symbol' => '/ZCZ25'],
        ['price' => $futuresPrice, 'updated_at' => now()]
    );
    CornPrice::updateOrCreate(
        ['symbol' => '/ZCZ25_500_PUT'],
        ['price' => null, 'bid' => null, 'ask' => null, 'updated_at' => now()]
    );

    $lastUpdated = CornPrice::where('symbol', '/ZCZ25')->first()->updated_at ?? 'N/A';
    \Log::info('Show Corn Prices - DB Updated', ['lastUpdated' => $lastUpdated]);

    \Log::info('Show Corn Prices - Rendering view');
    return view('corn-prices', [
        'futuresPrice' => $futuresPrice,
        'bid' => 'N/A',
        'ask' => 'N/A',
        'lastUpdated' => $lastUpdated
    ]);
}  

   public function updateCornPrice()
    {
        $token = config('schwab.token');
        if (!$token || !$this->refreshToken()) {
            Log::warning('No valid Schwab token');
            return;
        }

        $futuresResponse = Http::withHeaders(['Authorization' => "Bearer {$token}"])
            ->get('https://api.schwabapi.com/marketdata/v1/quotes?symbols=/ZCZ25');

        if (!$futuresResponse->failed()) {
            $futuresPrice = $futuresResponse->json()['/ZCZ25']['quote']['lastPrice'] ?? null;
            if ($futuresPrice) {
                CornPrice::updateOrCreate(
                    ['symbol' => '/ZCZ25'],
                    ['price' => $futuresPrice, 'updated_at' => now()]
                );
            }
        }

        $optionsResponse = Http::withHeaders(['Authorization' => "Bearer {$token}"])
            ->get('https://api.schwabapi.com/marketdata/v1/chains?symbol=/ZCZ25&contractType=CALL&strike=500');

        if (!$optionsResponse->failed()) {
            $optionsData = $optionsResponse->json()['callExpDateMap'] ?? [];
            $dec2025Key = array_key_first($optionsData);
            $option = $optionsData[$dec2025Key]['500.0'][0] ?? null;

            if ($option) {
                CornPrice::updateOrCreate(
                    ['symbol' => '/ZCZ25_500_CALL'],
                    ['bid' => $option['bidPrice'], 'ask' => $option['askPrice'], 'updated_at' => now()]
                );
            }
        }
    }
}
