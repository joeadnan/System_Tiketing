<?php

namespace App\Http\Controllers;

use App\Http\Requests\CsatSurveyRequest;
use App\Models\CsatSurvey;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;

class CsatSurveyController extends Controller
{
    public function store(CsatSurveyRequest $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->status !== Ticket::STATUS_CLOSED) {
            return back()->with('error', 'Survey hanya bisa diisi setelah tiket ditutup.');
        }

        CsatSurvey::updateOrCreate(
            [
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return back()->with('success', 'Terima kasih atas penilaiannya.');
    }
}
