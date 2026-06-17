<?php

use App\Http\Controllers\CsatSurveyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketAssignmentController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketEscalationController;
use App\Http\Controllers\TicketResolutionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('tickets', TicketController::class);

    Route::post('/tickets/{ticket}/assign', [TicketAssignmentController::class, 'assign'])
        ->name('tickets.assign');

    Route::post('/tickets/{ticket}/comments', [TicketCommentController::class, 'store'])
        ->name('tickets.comments.store');

    Route::post('/tickets/{ticket}/escalate', [TicketEscalationController::class, 'escalate'])
        ->name('tickets.escalate');

    Route::post('/tickets/{ticket}/resolve', [TicketResolutionController::class, 'resolve'])
        ->name('tickets.resolve');

    Route::post('/tickets/{ticket}/close', [TicketResolutionController::class, 'close'])
        ->name('tickets.close');

    Route::post('/tickets/{ticket}/reopen', [TicketResolutionController::class, 'reopen'])
        ->name('tickets.reopen');

    Route::post('/tickets/{ticket}/csat', [CsatSurveyController::class, 'store'])
        ->name('tickets.csat.store');

    Route::get('/reports/tickets', [ReportController::class, 'tickets'])
        ->name('reports.tickets');
});

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;


Route::get('/cron/check-sla', function (Request $request) {
    $authHeader = $request->header('Authorization');
    $expected = 'Bearer ' . env('CRON_SECRET');

    abort_unless(
        env('CRON_SECRET') && hash_equals($expected, (string) $authHeader),
        401
    );

    Artisan::call('tickets:check-sla');

    return response()->json([
        'success' => true,
        'message' => 'SLA check executed.',
        'output' => Artisan::output(),
    ]);
});

require __DIR__ . '/auth.php';
