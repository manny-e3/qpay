<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\PaymentGateway;
use App\Models\AppPaymentGateway;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentGatewayController extends Controller
{
    /**
     * Fetch all payment gateways and a paginated list of transaction logs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $gateways = PaymentGateway::all();
        $transactions = PaymentTransaction::with('app', 'gateway')->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => [
                'gateways' => $gateways,
                'transactions' => $transactions
            ]
        ]);
    }

    /**
     * Retrieve a paginated history of transaction logs only.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function transactionsOnly(Request $request)
    {
        $query = PaymentTransaction::with('app', 'gateway')->latest();

        if ($request->has('app_id') && !empty($request->app_id)) {
            $query->where('app_config_id', $request->app_id);
        }

        $transactions = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    /**
     * Retrieve settings for a single payment gateway.
     *
     * @param  int  $gatewayId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($gatewayId)
    {
        $gateway = PaymentGateway::find($gatewayId);

        if (!$gateway) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment gateway not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $gateway
        ]);
    }

    /**
     * Modify settings for a payment gateway.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gatewayId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $gatewayId)
    {
        $gateway = PaymentGateway::find($gatewayId);

        if (!$gateway) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment gateway not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'config' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $gateway->update($request->only(['name', 'description', 'is_active', 'config']));

        return response()->json([
            'status' => 'success',
            'message' => 'Payment gateway settings updated successfully.',
            'data' => $gateway
        ]);
    }

    /**
     * Retrieves the payment gateway options for a specific application along with credentials.
     *
     * @param  int  $appId
     * @return \Illuminate\Http\JsonResponse
     */
    public function appConfig($appId)
    {
        $app = \App\Services\AuthService::getAndPersistApp($appId);

        if (!$app) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
            ], 404);
        }

        $gateways = PaymentGateway::where('is_active', true)->get();
        $appConfigs = AppPaymentGateway::where('app_config_id', $app->id)->get()->keyBy('payment_gateway_id');

        return response()->json([
            'status' => 'success',
            'data' => [
                'app' => $app,
                'gateways' => $gateways,
                'app_configs' => $appConfigs
            ]
        ]);
    }

    /**
     * Save gateway configuration settings for a specific app and specify which gateway is active.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $appId
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveAppConfig(Request $request, $appId = null)
    {
        $appId = $request->input('app_id') 
            ?? $request->input('app_config_id') 
            ?? $request->input('appId') 
            ?? $request->input('id') 
            ?? $appId;

        $validator = Validator::make($request->all(), [
            'gateways' => 'required|array',
            'active_gateway' => 'nullable|exists:payment_gateways,id',
            'payment_callback_url' => 'nullable|url',
            'callback_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $availableGateways = PaymentGateway::where('is_active', true)->get();
            $globalCallbackUrl = $request->payment_callback_url ?? $request->callback_url;

            foreach ($availableGateways as $gateway) {
                $gatewayId = $gateway->id;
                $gatewayInput = $request->gateways[$gatewayId] ?? [];
                
                $isActive = isset($gatewayInput['is_active']) && ($gatewayInput['is_active'] === true || $gatewayInput['is_active'] === 1 || $gatewayInput['is_active'] === '1' || $gatewayInput['is_active'] === 'true');
                
                if ($request->has('active_gateway') && $request->active_gateway == $gatewayId) {
                    $isActive = true;
                }

                $callbackUrl = $globalCallbackUrl ?? $gatewayInput['callback_url'] ?? null;

                AppPaymentGateway::updateOrCreate(
                    ['app_config_id' => $appId, 'payment_gateway_id' => $gatewayId],
                    [
                        'config' => [],
                        'is_active' => $isActive,
                        'callback_url' => $callbackUrl
                    ]
                );
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment credentials for application saved successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving configuration: ' . $e->getMessage()
            ], 500);
        }
    }
}
