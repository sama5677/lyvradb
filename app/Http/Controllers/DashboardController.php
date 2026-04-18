<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getDashboard()
    {
        $data = $this->dashboardService->getDashboardData();

        return response()->json([
            'message' => 'Dashboard data fetched successfully',
            'data' => $data,
        ], 200);
    }
}
