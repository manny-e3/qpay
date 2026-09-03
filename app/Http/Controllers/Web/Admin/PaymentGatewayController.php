<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\AppPaymentGateway;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::all();
        $transactions = PaymentTransaction::with('app', 'gateway')->latest()->paginate(20);
        return view('admin.payment.index', compact('gateways', 'transactions'));
    }

    public function edit(PaymentGateway $gateway)
    {
        return view('admin.payment.edit_gateway', compact('gateway'));
    }

    public function update(Request $request, PaymentGateway $gateway)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'config' => 'nullable|array',
        ]);

        $gateway->update($request->all());

        return redirect()->route('admin.payment.index')->with('success', 'Gateway updated successfully.');
    }

    public function configureApp(Request $request, AppConfig $app)
    {
        $gateways = PaymentGateway::where('is_active', true)->get();
        $appGateways = AppPaymentGateway::where('app_config_id', $app->id)->get()->keyBy('payment_gateway_id');

        return view('admin.payment.configure_app', compact('app', 'gateways', 'appGateways'));
    }

    public function saveAppConfig(Request $request, AppConfig $app)
    {
        $request->validate([
            'gateways' => 'required|array',
            'payment_callback_url' => 'nullable|url',
        ]);

        $availableGateways = PaymentGateway::where('is_active', true)->get();

        foreach ($availableGateways as $gateway) {
            $gatewayId = $gateway->id;
            $gatewayInput = $request->gateways[$gatewayId] ?? [];
            
            $isActive = isset($gatewayInput['is_active']) && $gatewayInput['is_active'] == '1';

            AppPaymentGateway::updateOrCreate(
                ['app_config_id' => $app->id, 'payment_gateway_id' => $gatewayId],
                [
                    'config' => [],
                    'is_active' => $isActive,
                    'callback_url' => $request->payment_callback_url
                ]
            );
        }

        return redirect()->back()->with('success', 'Payment configuration saved.');
    }
}
