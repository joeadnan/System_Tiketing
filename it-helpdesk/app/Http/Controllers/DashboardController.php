<?php

namespace App\Http\Controllers;

use App\Services\TicketReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(TicketReportService $reportService): View
    {
        return view('dashboard.index', [
            'summary' => $reportService->dashboardSummary(),
        ]);
    }
}
