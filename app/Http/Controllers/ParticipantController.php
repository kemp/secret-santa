<?php

namespace App\Http\Controllers;

use App\Participant;
use Illuminate\Http\Request;
use App\Jobs\InitiateSecretSanta;

class ParticipantController extends Controller
{
    /**
     * Show information about the given participant, allowing 
     * them to update their wishlist and confirm.
     */
    public function show(Participant $participant)
    {
        // Make sure the user isn't already confirmed
        abort_if($participant->confirmed_at, 403, "You are already confirmed!");

        return view('participant.show', compact('participant'));
    }

    /**
     * Add the participants wishlist (if set) and confirm the user
     */
    public function confirm(Request $request, Participant $participant)
    {
        // Make sure the user isn't already confirmed
        abort_if($participant->confirmed_at, 403, "You are already confirmed!");

        // TODO validate data, update wishlist
        $data = $request->validate([
            'wishlist' => 'nullable|string',
        ]);

        $participant->wishlist = $data["wishlist"];
        $participant->confirmed_at = now();

        $participant->save();

        if ($participant->party->allParticipantsConfirmed()) {
            InitiateSecretSanta::dispatch($participant->party);
        }

        return view('participant.confirmed');
    }
}
