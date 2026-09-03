<?php

namespace App\Console\Commands;

use App\Models\OTPMaster;
use Illuminate\Console\Command;

class UpdateExpiredOTPs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'command:name';
    protected $signature = 'otp:update-expired';
    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';
    protected $description = 'Update status of expired OTPs';

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
        $this->info('Updating status of expired OTPs...');

        // Get expired OTPs where OTP_End is less than or equal to the current time
        $expiredOTPs = OTPMaster::where('status', 'pending')
            ->where('OTP_End', '<=', now())
            ->get();

        foreach ($expiredOTPs as $otp) {
            $otp->status = 'expired';
            $otp->save();
        }

        $this->info('Expired OTPs updated successfully.');
        // return 0;
    }
}
