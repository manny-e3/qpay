<?php

namespace App\Services;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthService
{
    /**
     * Fetch apps from the external API, sync them to local DB, and return the synced collection.
     * Fall back to all local DB records if API call is unsuccessful.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getSyncedApplications()
    {
        $url = config('services.auth_service.apps_url');
        $token = config('services.auth_service.bearer_token');
        
        $syncedApps = collect();

        if (!$url || !$token) {
            Log::warning('AuthService: apps_url or bearer_token not configured in services.php config.');
            return $syncedApps;
        }

        try {
            Log::info("AuthService: Fetching apps from external API: {$url}");
            
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $apiData = $response->json();
                
                // Determine the correct apps list array
                $apps = [];
                if (is_array($apiData)) {
                    if (isset($apiData['data']) && is_array($apiData['data'])) {
                        $apps = $apiData['data'];
                    } else {
                        $apps = $apiData;
                    }
                }

                $localConfigs = AppConfig::all()->keyBy('appID');

                foreach ($apps as $app) {
                    if (!is_array($app)) {
                        continue;
                    }

                    // Extract appID and appName, mapping common variations
                    $appID = $app['appID'] ?? $app['app_id'] ?? $app['code'] ?? $app['id'] ?? null;
                    $appName = $app['app_name'] ?? $app['appName'] ?? $app['name'] ?? null;
                    $status = $app['status'] ?? 'Active';

                    if (!$appID) {
                        Log::warning('AuthService: Application record missing ID key: ' . json_encode($app));
                        continue;
                    }

                    // Normalize status to 'Active' or 'Inactive'
                    if (is_string($status)) {
                        $status = in_array(strtolower($status), ['active', '1', 'true', 'enabled']) ? 'Active' : 'Inactive';
                    } else {
                        $status = $status ? 'Active' : 'Inactive';
                    }

                    // Look for existing local custom config in database
                    $local = $localConfigs->get($appID);

                    // Create a dynamic AppConfig model instance
                    $model = new AppConfig();
                    $model->forceFill([
                        'id' => $local->id ?? $app['id'] ?? $appID,
                        'appID' => $appID,
                        'appName' => $local->appName ?? $appName ?? 'App ' . $appID,
                        'status' => $local->status ?? $status,
                        'otp_configured' => $local !== null,
                        'link' => $local->link ?? $app['app_url'] ?? $app['link'] ?? $app['url'] ?? null,
                        'admin_email' => $local->admin_email ?? $app['admin_email'] ?? $app['email'] ?? null,
                        'username' => $local->username ?? $app['username'] ?? 'admin',
                        'password' => $local->password ?? $app['password'] ?? 'password',
                        'type' => $local->type ?? $app['type'] ?? 'numeric',
                        'otp_length' => $local->otp_length ?? $app['otp_length'] ?? 6,
                        'from_address' => $local->from_address ?? $app['from_address'] ?? 'no-reply@fmdqgroup.com',
                        'email_subject' => $local->email_subject ?? $app['email_subject'] ?? $appName ?? 'One-Time Password',
                        'email_body' => $local->email_body ?? $app['email_body'] ?? 'Dear User, your OTP is {otp}.',
                    ]);

                    if ($local) {
                        $model->exists = true;
                    }

                    $syncedApps->push($model);
                }

                Log::info('AuthService: Applications fetched from external API successfully (in-memory).');
                return $syncedApps;
            } else {
                Log::warning("AuthService: External API returned status {$response->status()}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("AuthService: Exception occurred during fetching: " . $e->getMessage());
        }

        // Return empty collection to ensure we do not use the local database records on failure
        return collect();
    }

    /**
     * Retrieve a specific application by ID or appID.
     * If not found locally in database, attempts to fetch from external API and persist it.
     *
     * @param string|int $appId
     * @return \App\Models\AppConfig|null
     */
    public static function getAndPersistApp($appId)
    {
        if (empty($appId)) {
            return null;
        }

        // Fetch the apps from the external API (in-memory)
        $apps = self::getSyncedApplications();

        // Find the app matching $appId in the returned collection (comparing either id or appID)
        return $apps->first(function ($app) use ($appId) {
            return $app->id == $appId || $app->appID == $appId;
        });
    }
}

