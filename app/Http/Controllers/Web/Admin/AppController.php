<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index()
    {
        $apps = \App\Services\AuthService::getSyncedApplications();
        return view('admin.apps.index', compact('apps'));
    }

    public function create()
    {
        $gateways = \App\Models\PaymentGateway::where('is_active', true)->get();
        return view('admin.apps.create', compact('gateways'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'appName' => 'required|string|max:255',
            'appID' => 'required|string|unique:app_config,appID',
            'username' => 'required|string',
            'password' => 'required|string',
            'status' => 'required|in:Active,Inactive',
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

        $app = AppConfig::create($request->only([
            'appName', 'appID', 'username', 'password', 'status', 'type', 'otp_length', 'email_subject', 'email_body', 'link', 'admin_email'
        ]));

        if ($request->has('gateways')) {
            $gatewayIds = $request->input('gateways', []);
            $syncData = [];
            foreach ($gatewayIds as $id) {
                $gateway = PaymentGateway::find($id);
                $syncData[$id] = [
                    'config' => $gateway->config ?? [],
                    'is_active' => true,
                    'callback_url' => $request->payment_callback_url,
                ];
            }
            $app->gateways()->sync($syncData);
        }

        if ($request->admin_email) {
            \Illuminate\Support\Facades\Mail::to($request->admin_email)
                ->send(new \App\Mail\AppConnectionDetails($app));
        }

        return redirect()->route('admin.apps.index')->with('success', 'Application created successfully.');
    }

    public function edit(AppConfig $app)
    {
        $gateways = PaymentGateway::where('is_active', true)->get();
        return view('admin.apps.edit', compact('app', 'gateways'));
    }

    public function update(Request $request, AppConfig $app)
    {
        $validated = $request->validate([
            'appName' => 'required|string|max:255',
            'username' => 'required|string',
            'password' => 'required|string',
            'status' => 'required|in:Active,Inactive',
            'type' => 'required|in:numeric,alphabetic,alphanumeric',
            'email_subject' => 'nullable|string',
            'email_body' => 'nullable|string',
            'link' => 'nullable|url',
            
        ]);

        // Save/persist custom application settings locally
        $dbRecord = AppConfig::firstOrNew(['appID' => $app->appID]);
        $dbRecord->id = $app->id;
        $dbRecord->fill($request->only(['appName', 'username', 'password', 'status', 'type', 'email_subject', 'email_body', 'link']));
        $dbRecord->save();

        // Sync gateways directly in the app_payment_gateways table
        $gatewayIds = $request->input('gateways', []);
        
        foreach ($gatewayIds as $gatewayId) {
            \App\Models\AppPaymentGateway::updateOrCreate(
                ['app_config_id' => $app->id, 'payment_gateway_id' => $gatewayId],
                [
                    'config' => [],
                    'is_active' => true,
                ]
            );
        }

        \App\Models\AppPaymentGateway::where('app_config_id', $app->id)
            ->whereNotIn('payment_gateway_id', $gatewayIds)
            ->delete();

        return redirect()->route('admin.apps.index')->with('success', 'Application updated successfully.');
    }

    public function destroy(AppConfig $app)
    {
        $app->delete();
        return redirect()->route('admin.apps.index')->with('success', 'Application deleted successfully.');
    }

    public function test(AppConfig $app)
    {
        return view('admin.apps.test', compact('app'));
    }

    public function runTest(Request $request, AppConfig $app)
    {
        $request->validate([
            'username' => 'required|email',
            'name' => 'required|string',
        ]);

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
            $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
                ->post($url, $payload);
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            return back()->with([
                'test_result' => [
                    'url' => $url,
                    'headers' => $headers,
                    'payload' => $payload,
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                    'duration' => $duration . 'ms',
                ]
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Test failed: ' . $e->getMessage());
        }
    }
}
