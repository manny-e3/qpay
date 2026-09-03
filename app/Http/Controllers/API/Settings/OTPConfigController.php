<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Controllers\Controller;
use App\Models\OTPConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OTPConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $otpConfigs = OTPConfig::all();
        return response()->json(['data' => $otpConfigs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
            'duration' => 'required', // Assuming duration is an integer value (e.g., in minutes)
        ]);

        $otpConfig = OTPConfig::create([
            'duration' => $validatedData['duration'],
        ]);

        return response()->json([
            'message' => 'OTP configuration created successfully',
            'status' => 'success',
            'data' => $otpConfig
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        // $validatedData = $request->validate([
        //     'duration' => 'required', // Assuming duration is an integer value (e.g., in minutes)
        // ]);

        // // Validate the request data
        $validator = Validator::make($request->all(), [
            'duration' => 'required'
        ]);
        //
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'status' => 'failed',
                'message' => 'Please validate your form.',
                'errors' => $errors,
            ], 404);
        }

        $otpConfig = OTPConfig::findOrFail($id);
        if ($otpConfig) {
            // $otpConfig->update([
            //     'duration' => $validatedData['duration'],
            // ]);
            $otpConfig->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'OTP configuration updated successfully',
                'data' => $otpConfig
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $otpConfig = OTPConfig::findOrFail($id);
        $otpConfig->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'OTP configuration deleted successfully'
        ]);
    }
}
