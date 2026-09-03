<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Controllers\Controller;
use App\Models\OTPLength;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OTPLengthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $otpLength = OTPLength::all();
        return response()->json(['data' => $otpLength]);
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
            'length' => 'required', // Assuming duration is an integer value (e.g., in minutes)
        ]);

        $otpLength = OTPLength::create([
            'length' => $validatedData['length'],
        ]);

        return response()->json([
            'message' => 'OTP length created successfully',
            'status' => 'success',
            'data' => $otpLength
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
        //     'length' => 'required', // Assuming duration is an integer value (e.g., in minutes)
        // ]);
        $validator = Validator::make($request->all(), [
            'length' => 'required|numeric'
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
        $otpLength = OTPLength::findOrFail($id);
        if ($otpLength) {
            // $otpConfig->update([
            //     'duration' => $validatedData['duration'],
            // ]);
            $otpLength->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Updated successfully',
                'data' => $otpLength
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
            ], 401);
        }
        // $otpLength->update([
        //     'length' => $validatedData['length'],
        // ]);

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'OTP length updated successfully',
        //     'data' => $otpLength
        // ]);
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
        $otpLength = OTPLength::findOrFail($id);
        $otpLength->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'OTP length deleted successfully'
        ]);
    }
}
