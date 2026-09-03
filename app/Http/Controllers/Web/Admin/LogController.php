<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\OTPMaster;
use App\Models\OTPHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        // Combine master and history logs conceptually for a unified view
        $masterLogs = OTPMaster::query();
        $historyLogs = OTPHistory::query();

        if ($search) {
            $masterLogs->where('username', 'like', "%$search%")
                      ->orWhere('appID', 'like', "%$search%")
                      ->orWhere('IP', 'like', "%$search%");
            
            $historyLogs->where('username', 'like', "%$search%")
                       ->orWhere('appID', 'like', "%$search%")
                       ->orWhere('IP', 'like', "%$search%");
        }

        $logs = $masterLogs->latest()->paginate(20, ['*'], 'master_page')
            ->appends(['search' => $search]);

        return view('admin.logs.index', compact('logs', 'search'));
    }
}
