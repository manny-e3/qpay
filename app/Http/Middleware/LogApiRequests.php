<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiRequestLog;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function terminate(Request $request, $response)
    {
        try {
            $duration = defined('LARAVEL_START')
                ? (microtime(true) - LARAVEL_START) * 1000
                : 0;

            // Resolve authenticated user email safely
            $userEmail = null;
            try {
                if ($request->user()) {
                    $userEmail = $request->user()->email;
                }
            } catch (\Exception $e) {
                // Auth resolver might throw exception if context not fully booted
            }

            // Capture source_app (using the ID header if it exists from api.auth or request headers/body/params)
            $appID = $request->header('ID') 
                  ?? $request->header('appID') 
                  ?? $request->input('appID')
                  ?? $request->query('appID');

            if (!$appID) {
                // Check if route has a reference (e.g. checkout pages) to trace back the app config
                $reference = $request->route('reference') 
                          ?? $request->input('reference') 
                          ?? $request->query('reference');
                if ($reference) {
                    try {
                        $transaction = \App\Models\PaymentTransaction::where('reference', $reference)->first();
                        if ($transaction && $transaction->app) {
                            $appID = $transaction->app->appID;
                        }
                    } catch (\Exception $txEx) {
                        // ignore if payment relation not resolved
                    }
                }
            }

            $sourceApp = $appID;
            if ($appID) {
                try {
                    $appConfig = \App\Models\AppConfig::where('appID', $appID)->first();
                    if ($appConfig) {
                        $sourceApp = $appConfig->appName . ' (' . $appID . ')';
                    }
                } catch (\Exception $appEx) {
                    // ignore if app config query fails
                }
            }

            ApiRequestLog::create([
                'method' => $request->method(),
                'path' => $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'source_app' => $sourceApp,
                'user_email' => $userEmail,
                'status_code' => $response ? $response->getStatusCode() : 500,
                'duration' => round($duration, 2),
            ]);
        } catch (\Exception $e) {
            // Safe-guard to ensure logging failure never crashes request termination
            Log::error("Failed to log API request: " . $e->getMessage());
        }
    }
}
