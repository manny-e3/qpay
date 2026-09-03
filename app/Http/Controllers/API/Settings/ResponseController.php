<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Controllers\Controller;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $responses = Response::all();
        return response()->json(['data' => $responses]);
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
        // // Validate the request data
        // $validator = Validator::make($request->all(), [
        //     'code' => 'required|numeric',
        //     'message' => 'required',
        //     'description' => 'required'
        // ]);
        $validatedData = $request->validate([
            'code' => 'required|numeric|unique:responses',
            'message' => 'required',
            'description' => 'required'
        ]);

        $responses = Response::create([
            'code' => $validatedData['code'],
            'message' => $validatedData['message'],
            'description' => $validatedData['description'],
        ]);
        //
        // if ($validator->fails()) {
        //     $errors = $validator->errors();
        //     return response()->json([
        //         'status' => 'failed',
        //         'message' => 'Please validate your form.',
        //         'errors' => $errors,
        //     ], 404);
        // }

        return response()->json([
            'status' => 'success',
            'message' => 'Response updated successfully'
        ]);
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
        $validator = Validator::make($request->all(), [
            'code' => 'required|numeric|unique:responses',
            'message' => 'required',
            'description' => 'required'
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

        $responses = Response::findOrFail($id);
        if ($responses) {
            // $otpConfig->update([
            //     'duration' => $validatedData['duration'],
            // ]);
            $responses->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Updated successfully',
                // 'data' => $otpConfig
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
            ], 401);
        }

        // //
        // $validatedData = $request->validate([
        //     'code' => 'required',
        //     'message' => 'required',
        // ]);

        // $responses = Response::findOrFail($id);

        // $responses->update([
        //     'code' => $validatedData['code'],
        //     'message' => $validatedData['message'],
        // ]);

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Response updated successfully',
        //     // 'data' => $responses
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
        $responses = Response::findOrFail($id);
        $responses->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Response deleted successfully'
        ]);
    }
}
