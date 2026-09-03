<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OTPHistory;
use App\Models\OTPMaster;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    /**
     * Get paginated logs from otp_history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory(Request $request)
    {
        $query = OTPHistory::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('appID', 'like', "%{$search}%")
                  ->orWhere('IP', 'like', "%{$search}%")
                  ->orWhere('OTP', 'like', "%{$search}%");
            });
        }

        if ($request->filled('appID')) {
            $query->where('appID', $request->input('appID'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = $request->input('per_page', 10);
        $logs = $query->latest()->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $logs
        ]);
    }

    /**
     * Merges pending active logs and history logs into a unified paginated flow.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnifiedLogs(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $appID = $request->input('appID');
        $status = $request->input('status');

        $masterQuery = DB::table('otp_master')
            ->select('id', 'appID', 'username', 'OTP', 'IP', 'status', DB::raw("'master' as log_type"), 'created_at');

        $historyQuery = DB::table('otp_history')
            ->select('id', 'appID', 'username', 'OTP', 'IP', 'status', DB::raw("'history' as log_type"), 'created_at');

        if ($request->filled('search')) {
            $masterQuery->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('appID', 'like', "%{$search}%")
                  ->orWhere('IP', 'like', "%{$search}%")
                  ->orWhere('OTP', 'like', "%{$search}%");
            });
            $historyQuery->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('appID', 'like', "%{$search}%")
                  ->orWhere('IP', 'like', "%{$search}%")
                  ->orWhere('OTP', 'like', "%{$search}%");
            });
        }

        if ($request->filled('appID')) {
            $masterQuery->where('appID', $appID);
            $historyQuery->where('appID', $appID);
        }

        if ($request->filled('status')) {
            $masterQuery->where('status', $status);
            $historyQuery->where('status', $status);
        }

        // Union query
        $unionQuery = $masterQuery->unionAll($historyQuery);

        // Subquery select to allow outer ordering & pagination
        $results = DB::table(DB::raw("({$unionQuery->toSql()}) as unified"))
            ->mergeBindings($unionQuery)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }

    /**
     * Get system response status mapping templates.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResponses()
    {
        $responses = Response::all();

        return response()->json([
            'status' => 'success',
            'data' => $responses
        ]);
    }

    /**
     * Get paginated logs from api_request_logs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApiRequestLogs(Request $request)
    {
        $query = \App\Models\ApiRequestLog::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('method', 'like', "%{$search}%")
                  ->orWhere('path', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('user_agent', 'like', "%{$search}%")
                  ->orWhere('source_app', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('method')) {
            $query->where('method', $request->input('method'));
        }

        if ($request->filled('status_code')) {
            $query->where('status_code', $request->input('status_code'));
        }

        $perPage = $request->input('per_page', 10);
        $logs = $query->latest()->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $logs
        ]);
    }

    /**
     * Get paginated unified logs filtered by a specific appID parameter in the route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $appID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnifiedLogsByAppID(Request $request, $appID)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');

        $masterQuery = DB::table('otp_master')
            ->select('id', 'appID', 'username', 'OTP', 'IP', 'status', DB::raw("'master' as log_type"), 'created_at')
            ->where('appID', $appID);

        $historyQuery = DB::table('otp_history')
            ->select('id', 'appID', 'username', 'OTP', 'IP', 'status', DB::raw("'history' as log_type"), 'created_at')
            ->where('appID', $appID);

        if ($request->filled('search')) {
            $masterQuery->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('IP', 'like', "%{$search}%")
                  ->orWhere('OTP', 'like', "%{$search}%");
            });
            $historyQuery->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('IP', 'like', "%{$search}%")
                  ->orWhere('OTP', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $masterQuery->where('status', $status);
            $historyQuery->where('status', $status);
        }

        // Union query
        $unionQuery = $masterQuery->unionAll($historyQuery);

        // Subquery select to allow outer ordering & pagination
        $results = DB::table(DB::raw("({$unionQuery->toSql()}) as unified"))
            ->mergeBindings($unionQuery)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }
}
