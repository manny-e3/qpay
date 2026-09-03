<?php

namespace App\Http\Controllers\API\Master;

use App\Helpers\EncryptHelper;
use App\Helpers\OTPValidationService;
use App\Http\Controllers\Controller;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OTPValidatorController extends Controller
{
    protected $otpValidationService;

    public function __construct(OTPValidationService $otpValidationService)
    {
        $this->otpValidationService = $otpValidationService;
    }
    //
    public function validateOTP(Request $request)
    {

        // $validatedData = Validator::make($request->all(), [
        //     'appID' => 'required',
        //     'username' => ['required', 'email'],
        //     'otp' => 'required'
        // ]);

        // if ($validatedData->fails()) {
        //     return [
        //         'status' => 'failed',
        //         'message' => 'Please validate your form.',
        //     ];
        // }
        // Validate the incoming request data
        $validatedData = $request->validate([
            'appID' => 'required|string',
            'username' => 'required|string',
            'otp' => 'required|string',
        ]);

        // Encrypt the request data
        $encryptedAppID = EncryptHelper::encrypt($request->appID);
        $encryptedUsername = EncryptHelper::encrypt($request->username);
        $encryptedOTP = EncryptHelper::encrypt($request->otp);

        // Decrypt the appID and username using the EncryptHelper
        $decryptedAppID = EncryptHelper::decrypt($encryptedAppID);
        $decryptedUsername = EncryptHelper::decrypt($encryptedUsername);
        $decryptedOTP = EncryptHelper::decrypt($encryptedOTP);

        // Responses
        $error = Response::whereMessage('error')->first();
        $success = Response::whereMessage('success')->first();

        // Call the OTPValidationService to validate the OTP
        $result = $this->otpValidationService->validateOTP(
            // $validatedData['appID'],
            // $validatedData['username'],
            // $validatedData['otp']
            $decryptedAppID,
            $decryptedUsername,
            $decryptedOTP
        );
        logger($result);
        return response()->json($result);
    }
}
