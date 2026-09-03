<?php

namespace App\Mail;

use App\Models\PaymentTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PaymentReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The payment transaction instance.
     *
     * @var PaymentTransaction
     */
    public $transaction;

    /**
     * Create a new message instance.
     *
     * @param PaymentTransaction $transaction
     * @return void
     */
    public function __construct(PaymentTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fileName = 'receipts/receipt-' . $this->transaction->reference . '.pdf';
        $fullPath = public_path($fileName);
        
        $email = $this->subject('Payment Receipt: ' . $this->transaction->reference)
            ->from(config('mail.from.address', 'no-reply@fmdqgroup.com'), $this->transaction->app->appName ?? 'Central Hub')
            ->view('emails.payment_receipt');

        if (file_exists($fullPath)) {
            $email->attach($fullPath, [
                'as' => 'receipt-' . $this->transaction->reference . '.pdf',
                'mime' => 'application/pdf',
            ]);
        } else {
            // Fallback: generate it on the fly if for some reason the file doesn't exist
            $logoPath = public_path('assets/FMDQ-Logo.png');
            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $logoBase64 = base64_encode(file_get_contents($logoPath));
            }
            $amountInWords = \App\Helpers\NumberToWordsHelper::convert($this->transaction->amount, $this->transaction->currency);
            $subtotal = $this->transaction->amount / 1.075;
            $vat = $this->transaction->amount - $subtotal;

            $pdf = Pdf::loadView('pdf.receipt', [
                'transaction' => $this->transaction,
                'logoBase64' => $logoBase64,
                'amountInWords' => $amountInWords,
                'subtotal' => $subtotal,
                'vat' => $vat
            ]);
            $email->attachData($pdf->output(), 'receipt-' . $this->transaction->reference . '.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
