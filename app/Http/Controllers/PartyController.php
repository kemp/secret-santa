<?php

namespace App\Http\Controllers;

use App\Jobs\InitiateSecretSanta;
use App\Jobs\SendEditLinkToCreator;
use App\Party;
use App\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PartyController extends Controller
{

    const PARTICIPANT_RULES = [
        'name' => 'required|string',
        'email' => 'required|email',
        'wishlist' => 'nullable|string',
    ];

    /**
     * Show the view to create a new party
     */
    public function create()
    {
        return view('party.create');
    }

    /**
     * Store the created party, and it's participants, in the database.
     */
    public function store(Request $request)
    {
        $data = $request->validate(self::PARTICIPANT_RULES);

        $party = Party::create([
            'invitation_token' => Str::random(8)
        ]);

        $participant = Participant::create([
            'party_id' => $party->id,
            'edit_token' => Str::random(64),
            'name' => $data['name'],
            'email' => $data['email'],
            'wishlist' => $data['wishlist'],
        ]);

        SendEditLinkToCreator::dispatch($party, $participant);

        return redirect()->route('party.show', ['party' => $party]);
    }

    public function show(Request $request, Party $party)
    {
        return view('party.show', compact('party'));
    }

    public function addParticipant(Request $request, Party $party)
    {
        // Validate name, email, wishlist (extract rules from above)
        $data = $request->validate(self::PARTICIPANT_RULES);

        // Ensure that the party isnt started
        abort_if($party->began_at !== null, 403);

        // Ensure a user with same email isnt already in party
        abort_if($party->participants->pluck('email')->contains($data['email']), 403);

        // Create participant
        $participant = $party->participants()->create($data);

        // Redirect back to party.show
        return redirect()->route('party.show', ['party' => $party]);
    }

    public function initiate(Request $request, Party $party)
    {
        abort_unless($party->canBeInitiated(), 403);

        InitiateSecretSanta::dispatch($party);

        $party->began_at = now();
        $party->save();

        return redirect()->back();
    }
}
