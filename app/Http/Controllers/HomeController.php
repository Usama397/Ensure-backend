<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use App\Models\AccountsCampaign;
use App\Models\Campaigns;
use App\Models\Dashboard;
use App\Models\Operator;
use App\Models\Service;
use App\Models\SubscriptionStats;
use App\Traits\Defs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class HomeController extends Controller
{
    public function latestDashboard(Request $request)
    {
        return view('latest_dashboard');
    }
}
