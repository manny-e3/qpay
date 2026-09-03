<?php

namespace App\Helpers;

use App\Models\OTPMaster;
use App\Models\Response;
use Illuminate\Support\Facades\DB;

class OTPValidationService
{
    public function validateOTP($appID, $username, $otp)
    {
        // Responses
        $error = Response::whereMessage('not_validated')->first();
        $success = Response::whereMessage('validated')->first();

        // Check if the provided appID, username, and OTP match the record in OTPMaster
        $otpRecord = OTPMaster::where('appID', $appID)
            ->where('username', $username)
            ->where('status', 'pending')
            // ->where('OTP', $otp)
            // ->latest()
            ->orderBy('created_at', 'DESC')
            ->first();
        //
        if (!$otpRecord) {
            return [
                'success' => false,
                'response_code' => 'Invalid OTP',
                'response_message' => 'OTP does not exist.',
            ];
        }
        //
        if ($otpRecord->OTP_End < now()) {
            return [
                'success' => false,
                'response_code' => $error->message,
                'response_message' => 'Expired OTP. Please try again.',
            ];
        }
        //
        if ($otpRecord->OTP != $otp) {
            return [
                'success' => false,
                'response_code' => $error->message,
                'response_message' => 'Invalid OTP. Please try again.',
            ];
        }

        if ($otpRecord) {
            // Update the status of the OTP records
            OTPMaster::where('username', $username)
                ->where('appID', $appID)
                ->where('status', 'pending')
                ->where('id', '<>', $otpRecord->id)
                ->update(['status' => 'expired']);

            $otpRecord->status = 'validated';
            $otpRecord->save();
            // DB::table('otp_master')
            //     ->where('username', $username)
            //     ->update(['status' => DB::raw("CASE WHEN id = $otpRecord->id THEN 'validated' ELSE 'expired' END")]);

            return [
                'success' => true,
                // 'otp' => $otpRecord,
                'response_code' => $success->message,
                'response_message' => $success->description,
            ];
        }
    }
}
