<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Participant;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParticipantController extends Controller
{
    /**
     * A participant's private page: their questionnaire and, once the draw
     * has happened, the profile of the person they're buying for.
     */
    public function show(Participant $participant)
    {
        $party = $participant->party;
        $recipient = $party->began_at ? $participant->assignment?->to : null;

        return view('participant.show', [
            'participant' => $participant,
            'party' => $party,
            'questions' => $party->questions(),
            'answers' => $participant->answersForYear($party->year),
            'recipient' => $recipient,
            'profile' => $recipient?->profile() ?? collect(),
        ]);
    }

    /**
     * Save the participant's questionnaire answers for their party's year.
     */
    public function updateAnswers(Request $request, Participant $participant)
    {
        $party = $participant->party;

        abort_unless($party->acceptsAnswers(), 403, 'The questionnaire is closed for this year.');

        $questions = $party->questions();

        $data = $request->validate(
            $questions->mapWithKeys(fn (Question $question) => [
                "answers.{$question->id}" => array_filter([
                    'nullable',
                    'string',
                    'max:2000',
                    $question->hasOptions() ? Rule::in($question->options) : null,
                ]),
            ])->all() + ['answers' => 'array'],
            [],
            $questions->mapWithKeys(fn (Question $question) => [
                "answers.{$question->id}" => "\"{$question->text}\"",
            ])->all(),
        );

        $existing = $participant->answers()->where('year', $party->year)->get()->keyBy('question_id');

        foreach ($questions as $question) {
            $body = $data['answers'][$question->id] ?? null;

            // Don't create empty rows for questions that were never answered.
            if (blank($body) && ! $existing->has($question->id)) {
                continue;
            }

            Answer::updateOrCreate(
                ['participant_id' => $participant->id, 'question_id' => $question->id, 'year' => $party->year],
                ['body' => $body],
            );
        }

        return redirect()->route('participant.show', $participant)->with(
            'success',
            'Saved! Your Secret Santa will see your answers. You can come back and change them any time'
            .($party->deadline ? ' before '.$party->deadline->format('F j').'.' : '.')
        );
    }
}
