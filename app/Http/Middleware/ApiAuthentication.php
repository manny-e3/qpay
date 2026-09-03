<?php

namespace App\Http\Middleware;

use App\Models\AppConfig;
use Closure;
use Illuminate\Http\Request;

class ApiAuthentication
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
        // Get the provided username and password from the request headers
        $appID = $request->header('ID');
        $username = $request->header('Username');
        $password = $request->header('Password');

        // Check if the username and password match the records in the app_config table
        $appConfig = AppConfig::where('appID', $appID)
            ->where('username', $username)
            ->where('password', $password)
            ->where('status', 'Active')
            ->first();

        if (!$appConfig) {
            // Lazily persist the application from the external auth service
            $appConfig = \App\Services\AuthService::getAndPersistApp($appID);

            // Re-validate credentials against the dynamically persisted app
            if (!$appConfig || $appConfig->username !== $username || $appConfig->password !== $password || $appConfig->status !== 'Active') {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Invalid credentials. Please provide valid ID, Username, and Password.'
                ], 401);
            }
        }

        // Pass the request to the next middleware if authentication is successful
        return $next($request);
    }
}