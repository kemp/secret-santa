<?php

namespace App\Http\Controllers;

use App\Party;
use App\Participant;
use Illuminate\Http\Request;
use App\Jobs\InvitePartyParticipants;
use Illuminate\Support\Str;

class PartyController extends Controller
{
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
        $data = $request->validate([
            'names' => 'required|array|min:2',
            'names.*' => 'required|string|distinct|min:2',
            'emails' => 'required|array|min:2',
            'emails.*' => 'required|email|distinct|min:2',
        ]);

        $party = Party::create();

        $party->save();

        foreach($data["names"] as $key => $name) {
            Participant::create([
                'party_id' => $party->id,
                'invitation_token' => Str::random(64),
                'name' => $name,
                'email' => $data["emails"][$key]
            ])->save();
        }

        InvitePartyParticipants::dispatch($party);

        return view('party.created');
    }
}
