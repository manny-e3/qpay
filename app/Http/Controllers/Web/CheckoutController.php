<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AppPaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class CheckoutController extends Controller
{
    public function index($reference)
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        return redirect("{$frontendUrl}/checkout/{$reference}");
    }

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

            return redirect($initResponse['checkout_url']);

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Could not initialize payment: ' . $e->getMessage());
        }
    }
}
