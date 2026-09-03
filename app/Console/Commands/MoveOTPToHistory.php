<?php

namespace App\Console\Commands;

use App\Models\OTPHistory;
use App\Models\OTPMaster;
use Illuminate\Console\Command;

class MoveOTPToHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:move-to-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move OTPs to OTP_history after 24 hours';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Moving OTPs to OTP_history...');

        // Get expired OTPs where OTP_End is 24 hours old or more
        $allOTPs = OTPMaster::get();

        foreach ($allOTPs as $otp) {
            OTPHistory::create([
                'appID' => $otp->appID,
                'username' => $otp->username,
                'OTP' => $otp->OTP,
                'OTP_Start' => $otp->OTP_Start,
                'OTP_End' => $otp->OTP_End,
                'IP' => $otp->IP,
                'status' => $otp->status,
            ]);

            $otp->delete();
        }

        $this->info('OTP records moved to OTP_history successfully.');
    }
}
