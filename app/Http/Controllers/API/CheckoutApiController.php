<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppPaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class CheckoutApiController extends Controller
{
    /**
     * Return transaction details and available gateways for a reference.
     * GET /api/checkout/{reference}
     */
    public function show($reference)
    {
        try {
            $transaction = PaymentTransaction::where('reference', $reference)
                ->with(['app', 'gateway'])
                ->firstOrFail();

            // If already processed, just return the transaction
            if ($transaction->status !== 'pending') {
                return response()->json([
                    'status'  => 'success',
                    'data'    => [
                        'transaction' => $transaction,
                        'gateways'    => [],
                    ],
                ]);
            }

            // Load active payment gateways configured for this app
            $gateways = AppPaymentGateway::where('app_config_id', $transaction->app_config_id)
                ->where('is_active', true)
                ->with('gateway')
                ->get()
                ->map(function ($ag) {
                    return [
                        'id'      => $ag->id,
                        'gateway' => [
                            'id'          => $ag->gateway->id,
                            'name'        => $ag->gateway->name,
                            'slug'        => $ag->gateway->slug,
                            'description' => $ag->gateway->description ?? null,
                            'is_active'   => $ag->gateway->is_active,
                        ],
                    ];
                });

            if ($gateways->isEmpty()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No payment gateways are configured for this application.',
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'transaction' => $transaction,
                    'gateways'    => $gateways,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Transaction not found.',
            ], 404);
        }
    }

    /**
     * Customer selects a gateway; initializes the real provider session and
     * returns the provider checkout URL for frontend redirect.
     * POST /api/checkout/{reference}/select
     */
    public function select(Request $request, $reference)
    {
        $request->validate([
            'gateway_id' => 'required|exists:payment_gateways,id',
        ]);

        try {
            $transaction = PaymentTransaction::where('reference', $reference)
                ->where('status', 'pending')
                ->firstOrFail();

            $appGateway = AppPaymentGateway::where('app_config_id', $transaction->app_config_id)
                ->where('payment_gateway_id', $request->gateway_id)
                ->where('is_active', true)
                ->with('gateway')
                ->firstOrFail();

            $gatewayConfig = is_array($appGateway->gateway->config) ? $appGateway->gateway->config : [];
            $appConfig = is_array($appGateway->config) ? $appGateway->config : [];
            $mergedConfig = array_merge($gatewayConfig, $appConfig);

            DB::beginTransaction();

            $updateData = [
                'payment_gateway_id' => $appGateway->payment_gateway_id,
            ];
            if (empty($transaction->callback_url) && !empty($appGateway->callback_url)) {
                $updateData['callback_url'] = $appGateway->callback_url;
            }

            $transaction->update($updateData);

            $service = PaymentServiceFactory::make($appGateway->gateway->slug);
            $initResponse = $service->initializeTransaction($transaction, $mergedConfig);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Gateway selected. Redirect customer to checkout_url.',
                'data'    => [
                    'checkout_url' => $initResponse['checkout_url'],
                    'reference'    => $reference,
                ],
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Could not initialize payment: ' . $e->getMessage(),
            ], 500);
        }
    }
}
