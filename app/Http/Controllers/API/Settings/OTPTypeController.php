<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Controllers\Controller;
use App\Models\OTPType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OTPTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $otpType = OTPType::all();
        return response()->json(['data' => $otpType]);
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
        $validatedData = Validator::make($request->all(), [
            'type' => 'required|string|in:alphabetic,numeric,alphanumeric',
            'description' => 'required',
        ]);

        if ($validatedData->fails()) {
            return [
                'status' => 'failed',
                // 'message' => 'Please validate your form.',
                'errors' => $validatedData->errors(),
            ];
        }

        // Save OTP details in the otp_master table
        $otpType = new OTPType();
        $otpType->type = $request->type;
        $otpType->description = $request->description;
        $otpType->save();

        return response()->json([
            'message' => 'OTP Type created successfully',
            'status' => 'success',
            'data' => $otpType
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
        $otpType = OTPType::findOrFail($id);
        //
        // $validatedData = Validator::make($request->all(), [
        //     'type' => 'required|string|in:alphabetic,numeric,alphanumeric',
        //     'description' => 'required',
        // ]);
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:alphabetic,numeric,alphanumeric',
            'description' => 'required',
        ]);
        if ($validatedData->fails()) {
            return [
                'status' => 'failed',
                // 'message' => 'Please validate your form.',
                'errors' => $validatedData->errors(),
            ];
        }

        // Save OTP details in the otp_master table
        // $otpType = new OTPType();
        // $otpType->type = $request->type;
        // $otpType->description = $request->description;
        // $otpType->save();
        // $otpType->update([
        //     'type' => $request->type,
        //     'description' => $request->description

        // ]);
        if ($otpType) {
            $otpType->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Updated successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
            ], 401);
        }

        // return response()->json([
        //     'message' => 'OTP Type updated successfully',
        //     'status' => 'success',
        //     'data' => $otpType
        // ], 201);
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
        $otpType = OTPType::findOrFail($id);
        $otpType->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'OTP type deleted successfully'
        ]);
    }
}
