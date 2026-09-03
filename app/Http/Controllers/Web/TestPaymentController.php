<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestPaymentController extends Controller
{
    public function index()
    {
        $app = \App\Models\AppConfig::first(); // Use the first app for testing
        return view('test_payment', compact('app'));
    }
}
