<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\OTPMaster;
use App\Models\OTPHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_apps' => AppConfig::count(),
            'active_apps' => AppConfig::where('status', 'Active')->count(),
            'otps_generated_24h' => OTPMaster::where('created_at', '>=', now()->subHours(24))->count(),
            'total_otp_history' => OTPHistory::count(),
        ];

        // OTP Generation Trends (Last 15 days)
        $trends = OTPMaster::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(15))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Status Distribution
        $distribution = OTPMaster::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Top Applications by Volume
        $top_apps = OTPMaster::select('otp_master.appID', 'app_config.appName', DB::raw('count(*) as count'))
            ->leftJoin('app_config', 'otp_master.appID', '=', 'app_config.appID')
            ->groupBy('otp_master.appID', 'app_config.appName')
            ->orderBy('count', 'DESC')
            ->take(5)
            ->get();

        $recent_activity = OTPMaster::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_activity', 'trends', 'distribution', 'top_apps'));
    }
}
