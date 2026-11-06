<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidSecretSantaException;
use App\Jobs\SendEditLinkToCreator;
use App\Mail\ParticipantLink;
use App\Mail\SecretSantaInitiated;
use App\Models\Participant;
use App\Models\Party;
use App\Models\SecretSanta;
use App\Services\SecretSantaRandomizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PartyController extends Controller
{
    const PARTICIPANT_RULES = [
        'name' => 'required|string',
        'email' => 'required|email',
    ];

    /**
     * Show the view to create a new party
     */
    public function create()
    {
        return view('party.create', [
            'suggestedDeadline' => now()->setDate(now()->year, 12, 24),
        ]);
    }

    /**
     * Store the created party, and it's participants, in the database.
     */
    public function store(Request $request)
    {
        $data = $request->validate(self::PARTICIPANT_RULES + [
            'deadline' => 'nullable|date|after:today',
        ]);

        $party = Party::create([
            'invitation_token' => Str::random(8),
            'deadline' => $this->endOfDay($data['deadline'] ?? null),
        ]);

        $participant = Participant::create([
            'party_id' => $party->id,
            'edit_token' => Str::random(64),
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        SendEditLinkToCreator::dispatch($party, $participant);

        return redirect()->route('participant.show', $participant)->with([
            'success' => 'Your Secret Santa is ready! Check your email for the private link you\'ll use to start the draw.',
            'clear-saved-input' => true,
        ]);
    }

    public function show(Request $request, Party $party)
    {
        return view('party.show', compact('party'));
    }

    public function addParticipant(Request $request, Party $party)
    {
        // Validate name and email (extract rules from above)
        $data = $request->validate(self::PARTICIPANT_RULES);

        // Ensure that the party isnt started
        abort_if($party->began_at !== null, 403);

        // Ensure a user with same email isnt already in party
        if ($party->participants->pluck('email')->map(fn ($email) => Str::lower($email))->contains(Str::lower($data['email']))) {
            throw ValidationException::withMessages([
                'email' => 'Someone with that email has already joined. Lost your link? Use "Email me my link" below.',
            ]);
        }

        // Create participant
        $participant = $party->participants()->create($data);

        // Send them their private link so they can come back later
        Mail::to($participant->email)->queue(new ParticipantLink($participant));

        // Send them straight to their questionnaire
        return redirect()->route('participant.show', $participant)->with([
            'success' => 'You\'re in! Answer a few questions below to help your Secret Santa.',
            'clear-saved-input' => true,
        ]);
    }

    /**
     * Change the questionnaire deadline (creator only).
     */
    public function updateDeadline(Request $request, Party $party)
    {
        abort_unless($party->isManagedByRequest(), 403);

        $data = $request->validate([
            'deadline' => 'nullable|date',
        ]);

        $party->deadline = $this->endOfDay($data['deadline'] ?? null);
        $party->save();

        return redirect()->back()->with('success', $party->deadline
            ? 'Answers can now be edited until '.$party->deadline->format('l, F j').'.'
            : 'The questionnaire no longer has a deadline.');
    }

    /**
     * Re-send a participant their private link. Always responds the same way
     * so that it can't be used to find out who has joined.
     */
    public function resendLink(Request $request, Party $party)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $participant = $party->participants()
            ->whereRaw('lower(email) = ?', [Str::lower($data['email'])])
            ->first();

        if ($participant) {
            Mail::to($participant->email)->queue(new ParticipantLink($participant));
        }

        return redirect()->route('party.show', $party)->with(
            'success',
            'If that email has joined this Secret Santa, a link is on its way.'
        );
    }

    public function initiate(Request $request, Party $party)
    {
        abort_unless($party->canBeInitiated(), 403);

        $participants = $party->participants()->pluck('email', 'id')->toArray();
        $exclusions = $party->exclusions()->pluck('to_id', 'from_id')->toArray();

        try {
            $pairings = SecretSantaRandomizer::randomize(array_keys($participants), $exclusions);
        } catch (InvalidSecretSantaException $e) {
            throw ValidationException::withMessages([
                $e->getMessage(),
            ]);
        }

        collect($pairings)->each(function ($assignedParticipant, $participant) use ($party, $participants) {
            // Create the secret santa assigment.
            $secretSanta = SecretSanta::create([
                'party_id' => $party->id,
                'from_id' => $participant,
                'to_id' => $assignedParticipant,
            ]);

            // Notify that creator of their secret santa.
            Mail::to($participants[$participant])
                ->queue(new SecretSantaInitiated($secretSanta));
        });

        $party->began_at = now();
        $party->save();

        return redirect()->back();
    }

    /**
     * Deadlines are dates; answers stay open until the end of that day.
     */
    protected function endOfDay(?string $date)
    {
        return $date ? now()->parse($date)->endOfDay() : null;
    }
}
