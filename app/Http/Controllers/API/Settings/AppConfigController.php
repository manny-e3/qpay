<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\PaymentGateway;
use App\Mail\AppConnectionDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AppConfigController extends Controller
{
    /**
     * Display a listing of the app configurations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $appConfig = \App\Services\AuthService::getSyncedApplications();
        if (method_exists($appConfig, 'load')) {
            $appConfig->load('gateways');
        }
        return response()->json($appConfig);
    }

    /**
     * Display a listing of the active app configurations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewActive()
    {
        $appConfig = \App\Services\AuthService::getSyncedApplications()->where('status', 'Active');
        if (method_exists($appConfig, 'load')) {
            $appConfig->load('gateways');
        }
        return response()->json($appConfig->values());
    }

    /**
     * Display a listing of the inactive app configurations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewNotActive()
    {
        $appConfig = \App\Services\AuthService::getSyncedApplications()->whereIn('status', ['Inactive', 'Not active']);
        if (method_exists($appConfig, 'load')) {
            $appConfig->load('gateways');
        }
        return response()->json($appConfig->values());
    }

    /**
     * Display a single app configuration.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $appConfig = \App\Services\AuthService::getAndPersistApp($id);
        if ($appConfig) {
            $appConfig->load('gateways');
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'App configuration not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $appConfig
        ]);
    }

    /**
     * Retrieve all active payment gateways.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function gateways()
    {
        $gateways = PaymentGateway::where('is_active', true)->get();
        return response()->json([
            'status' => 'success',
            'data' => $gateways
        ]);
    }

    /**
     * Store a newly created app configuration in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
       
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'appName' => 'required|string|max:255|unique:app_config,appName',
            'appID' => 'required|string|unique:app_config,appID',
            'username' => 'required|string|unique:app_config,username',
            'password' => 'required|string',
            'status' => 'required|in:Active,Inactive,Not active',
            'type' => 'required|in:numeric,alphabetic,alphanumeric',
            'otp_length' => 'required|integer|min:4|max:10',
            'email_subject' => 'nullable|string',
            'email_body' => 'nullable|string',
            'link' => 'nullable|url',
            'admin_email' => 'nullable|email',
            'gateways' => 'nullable|array',
            'gateways.*' => 'exists:payment_gateways,id',
            'payment_callback_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Please validate your form.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Instantiate in-memory AppConfig model
            $appConfig = new AppConfig();
            $appConfig->forceFill(array_merge([
                'id' => $request->appID,
            ], $request->only([
                'appName', 'appID', 'username', 'password', 'status', 'type', 'otp_length', 'email_subject', 'email_body', 'link', 'admin_email'
            ])));

            if ($request->has('gateways')) {
                $gatewayIds = $request->input('gateways', []);
                foreach ($gatewayIds as $gatewayId) {
                    AppPaymentGateway::updateOrCreate(
                        ['app_config_id' => $appConfig->id, 'payment_gateway_id' => $gatewayId],
                        [
                            'config' => [],
                            'is_active' => true,
                            'callback_url' => $request->payment_callback_url,
                        ]
                    );
                }
            }

            DB::commit();

            if ($request->admin_email) {
                try {
                    Mail::to($request->admin_email)->send(new AppConnectionDetails($appConfig));
                } catch (\Exception $mailEx) {
                    logger('Failed to send connection details email: ' . $mailEx->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "App configuration for {$appConfig->appName} created successfully.",
                'data' => $appConfig
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the app configuration.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified app configuration in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $appConfig = \App\Services\AuthService::getAndPersistApp($id);

        if (!$appConfig) {
            return response()->json([
                'status' => 'error',
                'message' => 'App configuration not found'
            ], 404);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'appName' => 'required|string|max:255|unique:app_config,appName,' . $id,
            'username' => 'required|string|unique:app_config,username,' . $id,
            'password' => 'required|string',
            'status' => 'required|in:Active,Inactive,Not active',
            'type' => 'required|in:numeric,alphabetic,alphanumeric',
            'otp_length' => 'required|integer|min:4|max:10',
            'email_subject' => 'nullable|string',
            'email_body' => 'nullable|string',
            'link' => 'nullable|url',
            'admin_email' => 'nullable|email',
            'gateways' => 'nullable|array',
            'gateways.*' => 'exists:payment_gateways,id',
            'payment_callback_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Please validate your form.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Save/persist custom application settings locally
            $dbRecord = AppConfig::firstOrNew(['appID' => $appConfig->appID]);
            $dbRecord->id = $appConfig->id;
            $dbRecord->fill($request->only([
                'appName', 'username', 'password', 'status', 'type', 'otp_length', 'email_subject', 'email_body', 'link', 'admin_email'
            ]));
            $dbRecord->save();

            $gatewayIds = $request->input('gateways', []);

            // Save configurations directly to app_payment_gateways using the app ID
            foreach ($gatewayIds as $gatewayId) {
                AppPaymentGateway::updateOrCreate(
                    ['app_config_id' => $appConfig->id, 'payment_gateway_id' => $gatewayId],
                    [
                        'config' => [],
                        'is_active' => true,
                        'callback_url' => $request->payment_callback_url,
                    ]
                );
            }

            // Remove gateways not in the request
            AppPaymentGateway::where('app_config_id', $appConfig->id)
                ->whereNotIn('payment_gateway_id', $gatewayIds)
                ->delete();

            DB::commit();

            $appConfig->fill($request->only([
                'appName', 'username', 'password', 'status', 'type', 'otp_length', 'email_subject', 'email_body', 'link', 'admin_email'
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'App configuration updated successfully',
                'data' => $appConfig
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the app configuration.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified app configuration from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $appConfig = \App\Services\AuthService::getAndPersistApp($id);

        if (!$appConfig) {
            return response()->json([
                'status' => 'error',
                'message' => 'App configuration not found'
            ], 404);
        }

        try {
            DB::beginTransaction();
            
            // Delete gateways configuration associated with this app ID
            AppPaymentGateway::where('app_config_id', $appConfig->id)->delete();
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'App configuration deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the app configuration.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run simulated test OTP generation for the specified app.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function runTest(Request $request, $id)
    {
        $appConfig = \App\Services\AuthService::getAndPersistApp($id);
        if (!$appConfig) {
            return response()->json([
                'status' => 'error',
                'message' => 'App configuration not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|email',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation failed for test payload.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $url = url('/api/master/generator');
        $headers = [
            'ID' => $appConfig->appID,
            'Username' => $appConfig->username,
            'Password' => $appConfig->password,
            'Accept' => 'application/json',
        ];
        $payload = [
            'appID' => $appConfig->appID,
            'username' => $request->username,
            'name' => $request->name,
        ];

        try {
            $startTime = microtime(true);
            $response = Http::withHeaders($headers)->post($url, $payload);
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'status' => 'success',
                'message' => 'Test connection completed.',
                'test_result' => [
                    'url' => $url,
                    'headers' => $headers,
                    'payload' => $payload,
                    'response_status' => $response->status(),
                    'response_body' => $response->json() ?? $response->body(),
                    'duration' => $duration . 'ms',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Execute clear and cache Artisan commands.
     *
     * @return void
     */
    public function executeCommands()
    {
        Artisan::call('optimize:clear');
        Artisan::call('view:clear');
        Artisan::call('config:cache');
        dd("Command Executed Successfully");
    }
}