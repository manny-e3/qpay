<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\PaymentGateway;
use App\Models\AppPaymentGateway;
use App\Mail\AppConnectionDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AppController extends Controller
{
    /**
     * Display a listing of client applications.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $apps = \App\Services\AuthService::getSyncedApplications();
        if (method_exists($apps, 'load')) {
            $apps->load('gateways');
        } else {
            $apps->each(function ($app) {
                if (method_exists($app, 'load')) {
                    $app->load('gateways');
                }
            });
        }
        return response()->json([
            'status' => 'success',
            'data' => $apps
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
     * Store a newly created application configuration in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appID'         => 'required|string',
            'username'      => 'required|string',
            'password'      => 'required|string',
            'status'        => 'required|in:Active,Inactive',
            'type'          => 'required|in:numeric,alphabetic,alphanumeric',
            'otp_length'    => 'required|integer|min:4|max:10',
            'email_subject' => 'nullable|string',
            'email_body'    => 'nullable|string',
            // 'link'          => 'nullable|url',
            // 'admin_email'   => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Please validate your form.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Retrieve app details automatically from AuthService using provided appID
        $authApp = \App\Services\AuthService::getAndPersistApp($request->appID);

        $appName = $authApp->appName ?? $authApp->app_name ?? $request->appName;
        $appID   = $authApp->appID ?? $authApp->app_id ?? $request->appID;

        if (!$appName || !$appID) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unable to resolve application details for the provided appID.'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $app = AppConfig::firstOrNew(['appID' => $appID]);
            if ($authApp && isset($authApp->id)) {
                $app->id = $authApp->id;
            }
            $app->fill([
                'appName'       => $appName,
                'appID'         => $appID,
                'username'      => $request->username,
                'password'      => $request->password,
                'status'        => $request->status,
                'type'          => $request->type,
                'otp_length'    => $request->otp_length,
                'email_subject' => $request->email_subject,
                'email_body'    => $request->email_body,
                // 'link'          => $request->link,
                // 'admin_email'   => $request->admin_email,
            ]);
            $app->save();

            // if ($request->admin_email) {
            //     try {
            //         Mail::to($request->admin_email)
            //             ->send(new AppConnectionDetails($app));
            //     } catch (\Exception $mailEx) {
            //         \Illuminate\Support\Facades\Log::warning('Failed sending application connection details email: ' . $mailEx->getMessage());
            //     }
            // }

            DB::commit();

            $app->load('gateways');

            return response()->json([
                'status'  => 'success',
                'message' => 'Application created successfully.',
                'data'    => $app
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while creating the application.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        $app = \App\Services\AuthService::getAndPersistApp($id);
        
        if ($app) {
            $app->load('gateways');
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $app
        ]);
    }

    /**
     * Update the specified application configuration in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $app = AppConfig::find($id);

        if (!$app) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'appName' => 'nullable|string|max:255',
            'username' => 'required|string',
            'password' => 'required|string',
            'status' => 'required|in:Active,Inactive',
            'type' => 'required|in:numeric,alphabetic,alphanumeric',
            'otp_length' => 'nullable|integer|min:4|max:10',
            'email_subject' => 'nullable|string',
            'email_body' => 'nullable|string',
            'link' => 'nullable|url',
            'admin_email' => 'nullable|email',
           
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
            $dbRecord = AppConfig::firstOrNew(['appID' => $app->appID]);
            $dbRecord->id = $app->id;
            
            $resolvedAppName = $request->input('appName') ?: $app->appName ?: $app->app_name ?: 'App ' . $app->appID;
            
            $dbRecord->fill(array_merge($request->only([
                'username', 'password', 'status', 'type', 'otp_length', 'email_subject', 'email_body', 'link', 'admin_email'
            ]), ['appName' => $resolvedAppName]));
            $dbRecord->save();

            // Sync gateways directly in the app_payment_gateways table
            $gatewayIds = $request->input('gateways', []);
            foreach ($gatewayIds as $gatewayId) {
                AppPaymentGateway::updateOrCreate(
                    ['app_config_id' => $app->id, 'payment_gateway_id' => $gatewayId],
                    [
                        'config' => [],
                        'is_active' => true,
                        'callback_url' => $request->payment_callback_url,
                    ]
                );
            }

            AppPaymentGateway::where('app_config_id', $app->id)
                ->whereNotIn('payment_gateway_id', $gatewayIds)
                ->delete();

            DB::commit();

            $app->fill(array_merge($request->only([
                'username', 'password', 'status', 'type', 'otp_length', 'email_subject', 'email_body', 'link', 'admin_email'
            ]), ['appName' => $resolvedAppName]));

            return response()->json([
                'status' => 'success',
                'message' => 'Application updated successfully.',
                'data' => $app
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified application configuration from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $app = AppConfig::find($id);

        if (!$app) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
            ], 404);
        }

        try {
            DB::beginTransaction();

            AppPaymentGateway::where('app_config_id', $app->id)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Application deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run simulated test OTP generation for the specified application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function runTest(Request $request, $id)
    {
        $app = AppConfig::find($id);
        
        if (!$app) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
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
            'ID' => $app->appID,
            'Username' => $app->username,
            'Password' => $app->password,
            'Accept' => 'application/json',
        ];
        $payload = [
            'appID' => $app->appID,
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
}
