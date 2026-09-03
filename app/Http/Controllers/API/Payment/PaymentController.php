<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\AppPaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class PaymentController extends Controller
{
    /**
     * Initiate a new payment transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'appID'         => 'required',
            'amount'        => 'required|numeric|min:1',
            'currency'      => 'string|max:3',
            'email'         => 'required|email',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'phone'         => 'nullable|string|max:20',
            'company'       => 'nullable|string|max:255',
            'callback_url'  => 'nullable|url',
            'metadata'      => 'nullable|array',
        ]);

        $app = \App\Services\AuthService::getAndPersistApp($request->appID);

        if (!$app) {
            return response()->json([
                'status'  => 'error',
                'message' => 'The selected appID is invalid.'
            ], 422);
        }

        try {
            $reference = 'PAY-' . Str::upper(Str::random(12));

            // Resolve default callback_url from app_payment_gateways if not provided in payload
            $callbackUrl = $request->callback_url;
            if (empty($callbackUrl)) {
                $appGw = AppPaymentGateway::where('app_config_id', $app->id)
                    ->whereNotNull('callback_url')
                    ->where('callback_url', '!=', '')
                    ->first();
                if ($appGw && !empty($appGw->callback_url)) {
                    $callbackUrl = $appGw->callback_url;
                }
            }

            DB::beginTransaction();

            $transaction = PaymentTransaction::create([
                'app_config_id'       => $app->id,
                'payment_gateway_id'  => null, // Will be selected on the preview page
                'reference'           => $reference,
                'amount'              => $request->amount,
                'currency'            => $request->currency ?? 'NGN',
                'status'              => 'pending',
                'customer_email'      => $request->email,
                'customer_first_name' => $request->first_name,
                'customer_last_name'  => $request->last_name,
                'customer_phone'      => $request->phone,
                'customer_company'    => $request->company,
                'callback_url'        => $callbackUrl,
                'metadata'            => $request->metadata,
            ]);

            DB::commit();

            try {
                $logoPath = public_path('assets/FMDQ-Logo.png');
                $logoBase64 = '';
                if (file_exists($logoPath)) {
                    $logoBase64 = base64_encode(file_get_contents($logoPath));
                }
                $amountInWords = \App\Helpers\NumberToWordsHelper::convert($transaction->amount, $transaction->currency);
                $subtotal = $transaction->amount / 1.075;
                $vat = $transaction->amount - $subtotal;

                $pdf = Pdf::loadView('pdf.invoice', [
                    'transaction' => $transaction,
                    'logoBase64' => $logoBase64,
                    'amountInWords' => $amountInWords,
                    'subtotal' => $subtotal,
                    'vat' => $vat
                ]);

                $dirPath = public_path('invoices');
                if (!file_exists($dirPath)) {
                    mkdir($dirPath, 0755, true);
                }
                $fileName = 'invoices/invoice-' . $transaction->reference . '.pdf';
                file_put_contents(public_path($fileName), $pdf->output());
                $invoiceUrl = asset($fileName);

                $transaction->update([
                    'invoice_url' => $invoiceUrl
                ]);

                \Illuminate\Support\Facades\Mail::to($transaction->customer_email)
                    ->send(new \App\Mail\PaymentInvoiceMail($transaction));
            } catch (\Exception $mailEx) {
                \Illuminate\Support\Facades\Log::error("Failed to generate invoice/send email for {$reference}: " . $mailEx->getMessage());
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Transaction initialized. Redirect user to checkout_url.',
                'data'    => [
                    'checkout_url' => route('checkout.index', $reference),
                    'reference'    => $reference,
                    'invoice_url'  => $transaction->invoice_url,
                ]
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function verify($reference)
    {
        try {
            $transaction = PaymentTransaction::where('reference', $reference)->with('app', 'gateway')->firstOrFail();
            
            if ($transaction->status !== 'pending') {
                return response()->json(['status' => 'success', 'data' => $transaction]);
            }

            $appGateway = AppPaymentGateway::where('app_config_id', $transaction->app_config_id)
                ->where('payment_gateway_id', $transaction->payment_gateway_id)
                ->with('gateway')
                ->firstOrFail();

            $gatewayConfig = is_array($transaction->gateway->config) ? $transaction->gateway->config : [];
            $appConfig = is_array($appGateway->config) ? $appGateway->config : [];
            $mergedConfig = array_merge($gatewayConfig, $appConfig);

            $service = PaymentServiceFactory::make($transaction->gateway->slug);
            $verifyResponse = $service->verifyTransaction($reference, $mergedConfig);

            $transaction->update([
                'status' => $verifyResponse['status'],
                'gateway_response' => $verifyResponse['gateway_response'],
            ]);

            if ($verifyResponse['status'] === 'successful') {
                try {
                    $logoPath = public_path('assets/FMDQ-Logo.png');
                    $logoBase64 = '';
                    if (file_exists($logoPath)) {
                        $logoBase64 = base64_encode(file_get_contents($logoPath));
                    }
                    $amountInWords = \App\Helpers\NumberToWordsHelper::convert($transaction->amount, $transaction->currency);
                    $subtotal = $transaction->amount / 1.075;
                    $vat = $transaction->amount - $subtotal;

                    $pdf = Pdf::loadView('pdf.receipt', [
                        'transaction' => $transaction,
                        'logoBase64' => $logoBase64,
                        'amountInWords' => $amountInWords,
                        'subtotal' => $subtotal,
                        'vat' => $vat
                    ]);

                    $dirPath = public_path('receipts');
                    if (!file_exists($dirPath)) {
                        mkdir($dirPath, 0755, true);
                    }
                    $fileName = 'receipts/receipt-' . $transaction->reference . '.pdf';
                    file_put_contents(public_path($fileName), $pdf->output());
                    $receiptUrl = asset($fileName);

                    $transaction->update([
                        'receipt_url' => $receiptUrl
                    ]);

                    \Illuminate\Support\Facades\Mail::to($transaction->customer_email)
                        ->send(new \App\Mail\PaymentReceiptMail($transaction));
                } catch (\Exception $mailEx) {
                    \Illuminate\Support\Facades\Log::error("Failed to generate receipt/send email for {$reference}: " . $mailEx->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction verified.',
                'data' => $transaction
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function callback(Request $request, $gateway)
    {
        // This is where the user is redirected after payment
        $reference = $request->query('reference') ?? $request->query('trxref') ?? $request->query('tx_ref');
        
        if (!$reference) {
            return response()->json(['message' => 'Reference not found.'], 400);
        }

        // Logic to verify and then redirect to app's callback_url
        $transaction = PaymentTransaction::where('reference', $reference)->first();
        
        if ($transaction && $transaction->status === 'pending') {
            $this->verify($reference);
            $transaction->refresh();
        }

        $callbackUrl = $this->resolveCallbackUrl($transaction);

        if ($callbackUrl) {
            $separator = parse_url($callbackUrl, PHP_URL_QUERY) ? '&' : '?';
            return redirect($callbackUrl . $separator . "status={$transaction->status}&reference={$reference}");
        }

        return redirect()->route('checkout.index', $reference);
    }

    /**
     * Resolve the callback URL for a transaction from transaction or app_payment_gateways table.
     *
     * @param  \App\Models\PaymentTransaction|null  $transaction
     * @return string|null
     */
    protected function resolveCallbackUrl($transaction)
    {
        if (!$transaction) {
            return null;
        }

        if (!empty($transaction->callback_url)) {
            return $transaction->callback_url;
        }

        if (!empty($transaction->payment_gateway_id)) {
            $appGateway = AppPaymentGateway::where('app_config_id', $transaction->app_config_id)
                ->where('payment_gateway_id', $transaction->payment_gateway_id)
                ->whereNotNull('callback_url')
                ->where('callback_url', '!=', '')
                ->first();

            if ($appGateway && !empty($appGateway->callback_url)) {
                return $appGateway->callback_url;
            }
        }

        $fallbackGateway = AppPaymentGateway::where('app_config_id', $transaction->app_config_id)
            ->whereNotNull('callback_url')
            ->where('callback_url', '!=', '')
            ->first();

        if ($fallbackGateway && !empty($fallbackGateway->callback_url)) {
            return $fallbackGateway->callback_url;
        }

        return null;
    }

    public function webhook(Request $request, $gateway)
    {
        // Handle background notifications from gateways
        // Implementation varies per gateway (signature verification, etc.)
        return response()->json(['message' => 'Webhook received.']);
    }
}


