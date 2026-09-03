<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\OTPConfig;
use App\Models\OTPLength;
use App\Models\OTPType;
use App\Models\Response;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $duration = OTPConfig::first();
        $length = OTPLength::first();
        $types = OTPType::all();
        $responses = Response::all();

        return view('admin.settings.index', compact('duration', 'length', 'types', 'responses'));
    }

    public function updateOTPConfig(Request $request)
    {
        $request->validate([
            'duration' => 'required|integer|min:1',
        ]);

        $config = OTPConfig::first();
        $config->update(['duration' => $request->duration]);

        return back()->with('success', 'OTP Duration updated successfully.');
    }

    public function updateOTPLength(Request $request)
    {
        $request->validate([
            'length' => 'required|integer|min:4|max:12',
        ]);

        $config = OTPLength::first();
        $config->update(['length' => $request->length]);

        return back()->with('success', 'OTP Length updated successfully.');
    }
}
