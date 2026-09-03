<?php

namespace App\Http\Controllers\API\Master;

use App\Helpers\EncryptHelper;
use App\Http\Controllers\Controller;
use App\Mail\OTPMail;
use App\Models\AppConfig;
use App\Models\OTPConfig;
use App\Models\OTPLength;
use App\Models\OTPMaster;
use App\Models\OTPType;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OTPGeneratorController extends Controller
{
    //
    public function index()
    {
        if(auth()->user()) {

            $generatedOTPs = OTPMaster::all();
            return response()->json($generatedOTPs);
        } else {
            return response()->json(['message' => 'not authorised']);
        }
    }
    //
    public function generateOTP(Request $request)
    {
        // Validate the request data
        $validatedData = Validator::make($request->all(), [
            'appID' => 'required',
            'name' => 'required',
            'username' => ['required', 'email'],
        ]);

        if ($validatedData->fails()) {
            return [
                'status' => 'failed',
                'message' => 'Please validate your form.',
                'errors' => $validatedData->errors(),
            ];
        }
        // Encrypt the request data

        $encryptedAppID = EncryptHelper::encrypt($request->appID);
        $encryptedUsername = EncryptHelper::encrypt($request->username);
        $encryptedName = EncryptHelper::encrypt($request->name);

        // Decrypt the appID and username using the EncryptHelper
        $decryptedAppID = EncryptHelper::decrypt($encryptedAppID);
        $decryptedUsername = EncryptHelper::decrypt($encryptedUsername);
        $decryptedName = EncryptHelper::decrypt($encryptedName);

        // Responses
        $error = Response::whereMessage('not_generated')->first();
        $success = Response::whereMessage('generated')->first();

        // Check if the appID is active from the OTPConfig table
        $appConfig = AppConfig::where('appID', $decryptedAppID)->where('status', 'Active')->first();
        if (!$appConfig) {
            return [
                // 'status' => $error->code,
                'status' => $error->message,
                'message' => 'This app does not require OTP.',
            ];
        }

        // Fetch the OTP length from the OTPLength table
        // $type = $request->type;
        $app = AppConfig::where('appID', $decryptedAppID)->first();
        $type = $app->type;
        $otpLength = OTPLength::first();
        // $otpType = OTPType::where('type', $type)->first();
        // Map the type to the corresponding OTP type in the database
        if ($type === 'alphabetic') {
            $otpType = OTPType::where('type', 'alphabetic')->first();
            // Generate the OTP based on the selected type
            $otp = $this->generateRandomOTP($otpLength->length, $otpType->type);
        } elseif ($type === 'alphanumeric') {
            $otpType = OTPType::where('type', 'alphanumeric')->first();
            // Generate the OTP based on the selected type
            $otp = $this->generateRandomOTP($otpLength->length, $otpType->type);
        } else {
            $otpType = OTPType::where('type', 'numeric')->first();
            // Generate the OTP based on the selected type
            $otp = $this->generateRandomOTP($otpLength->length, $otpType->type);
        }

        // Calculate OTP_Start and OTP_End timestamps
        $otpConfig = OTPConfig::first();
        $currentTime = now();
        $otpStart = $currentTime->toDateTimeString();
        $otpEnd = $currentTime->addMinutes($otpConfig->duration)->toDateTimeString();
        // Fetch the IP address
        $ip = request()->ip();

        // Save OTP details in the otp_master table
        $otpMaster = OTPMaster::create([
            'appID' => $decryptedAppID,
            'username' => $decryptedUsername,
            'name' => $decryptedName,
            // 'OTP_Type' => $otpType->type,
            'OTP' => $otp,
            'OTP_Start' => $otpStart,
            'OTP_End' => $otpEnd,
            'IP' => $ip
        ]);
        // Send the OTP to the user's email (you can customize this as per your application)
        if ($otpMaster) {
            $info = ([
                'name' => $decryptedName,
                'app' => $appConfig->appName,
                'from' => 'no-reply@fmdqgroup.com',
                'subject' => 'OTP for ' . $appConfig->email_subject . ' Portal',
                'body' => $appConfig->email_body,
                'link' => $appConfig->link,
                'otp' => $otp,
                'duration' => $otpConfig->duration,
                'end' => $otpEnd,
            ]);
            logger($decryptedName.' : '.$otp);
            Mail::to($decryptedUsername)->send(new OTPMail($info));
            return [
                'success' => true,
                'status' => $success->message,
                'message' => $success->description,
                // 'data' => $otpMaster,
            ];
        } else {
            return [
                'success' => false,
                'status' => $error->code,
                'message' => $error->description,
                // 'data' => $otpMaster,
            ];
        }
    }


    private function generateRandomOTP($length, $otpType)
    {
        // Define the characters allowed in the OTP based on the selected type
        $numeric = '0123456789';
        $alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // $alphanumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $alphanumeric = '9AB0CD1EF2GH3IJ4KL5MN6OP7QR8STUVWXYZ';

        // Choose the appropriate character set based on the selected OTP type
        if ($otpType === 'alphabetic') {
            $characters = $alpha;
        } elseif ($otpType === 'numeric') {
            $characters = $numeric;
        } elseif ($otpType === 'alphanumeric') {
            $characters = $alphanumeric;
        } else {
            throw new \InvalidArgumentException('Invalid OTP type.');
        }

        // Calculate the number of characters in the allowed character set
        $characterSetLength = strlen($characters);

        // Initialize an empty string to store the OTP
        $otp = '';

        // Generate random characters from the allowed character set until the desired length is reached
        for ($i = 0; $i < $length; $i++) {
            // Get a random index to select a character from the character set
            $randomIndex = random_int(0, $characterSetLength - 1);

            // Append the selected character to the OTP
            $otp .= $characters[$randomIndex];
        }

        return $otp;
    }
}