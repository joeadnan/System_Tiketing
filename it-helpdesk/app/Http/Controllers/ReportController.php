<?php

namespace App\Http\Controllers;

use App\Services\TicketReportService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function tickets(TicketReportService $reportService): View
    {
        return view('reports.tickets', [
            'kpi' => $reportService->kpi(),
        ]);
    }
}
