<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Participant;
use App\Models\Party;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionnaireTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_questions_are_seeded()
    {
        $this->assertSame(8, Question::count());
        $this->assertSame(['Cozy', 'Practical', 'Funny', 'Fancy'], Question::where('text', 'like', 'Pick one%')->first()->options);
    }

    public function test_participant_sees_only_questions_for_their_year()
    {
        $party = Party::factory()->create(['year' => 2026]);
        $participant = Participant::factory()->for($party)->create();

        Question::factory()->create(['text' => 'Only in 2026?', 'year' => 2026]);
        Question::factory()->create(['text' => 'Only in 2025?', 'year' => 2025]);
        Question::factory()->create(['text' => 'Switched off?', 'is_active' => false]);

        $this->get(route('participant.show', $participant))
            ->assertOk()
            ->assertSee('Is there a hobby you&#039;ve been wanting to get into?', false)
            ->assertSee('Only in 2026?')
            ->assertDontSee('Only in 2025?')
            ->assertDontSee('Switched off?');
    }

    public function test_questions_are_shown_in_sort_order()
    {
        Question::query()->delete();
        $party = Party::factory()->create();
        $participant = Participant::factory()->for($party)->create();

        Question::factory()->create(['text' => 'Second?', 'sort_order' => 20]);
        Question::factory()->create(['text' => 'First?', 'sort_order' => 10]);

        $this->get(route('participant.show', $participant))->assertSeeInOrder(['First?', 'Second?']);
    }

    public function test_an_unknown_link_is_not_found()
    {
        $this->get('/you/not-a-real-token')->assertNotFound();
    }

    public function test_participant_can_save_answers_and_blank_ones_are_not_stored()
    {
        $participant = Participant::factory()->create();
        [$first, $second] = Question::forYear($participant->party->year)->take(2)->get();

        $this->put(route('participant.answers.update', $participant), [
            'answers' => [
                $first->id => 'Pottery',
                $second->id => '',
            ],
        ])->assertRedirect(route('participant.show', $participant))->assertSessionHas('success');

        $this->assertSame(1, Answer::count());
        $this->assertDatabaseHas('answers', [
            'participant_id' => $participant->id,
            'question_id' => $first->id,
            'year' => $participant->party->year,
            'body' => 'Pottery',
        ]);
    }

    public function test_existing_answers_are_prefilled_and_can_be_edited_and_cleared()
    {
        $participant = Participant::factory()->create();
        [$first, $second] = Question::forYear($participant->party->year)->take(2)->get();
        $year = $participant->party->year;

        Answer::create(['participant_id' => $participant->id, 'question_id' => $first->id, 'year' => $year, 'body' => 'Bouldering']);
        Answer::create(['participant_id' => $participant->id, 'question_id' => $second->id, 'year' => $year, 'body' => 'Socks']);

        $this->get(route('participant.show', $participant))
            ->assertSee('Bouldering')
            ->assertSee('Socks')
            ->assertSee('2</span> of 8 answered', false);

        $this->put(route('participant.answers.update', $participant), [
            'answers' => [$first->id => 'Bread baking', $second->id => ''],
        ]);

        $this->assertSame('Bread baking', $participant->answersForYear($year)->get($first->id));
        $this->assertNull($participant->answersForYear($year)->get($second->id));
        $this->assertSame(2, Answer::count());
    }

    public function test_choice_questions_only_accept_listed_options()
    {
        $participant = Participant::factory()->create();
        $choice = Question::whereNotNull('options')->first();

        $this->put(route('participant.answers.update', $participant), [
            'answers' => [$choice->id => 'Spooky'],
        ])->assertSessionHasErrors("answers.{$choice->id}");

        $this->put(route('participant.answers.update', $participant), [
            'answers' => [$choice->id => 'Cozy'],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('answers', ['question_id' => $choice->id, 'body' => 'Cozy']);
    }

    public function test_answers_to_questions_not_on_the_questionnaire_are_ignored()
    {
        $participant = Participant::factory()->create();
        $off = Question::factory()->create(['is_active' => false]);

        $this->put(route('participant.answers.update', $participant), [
            'answers' => [$off->id => 'Sneaky'],
        ]);

        $this->assertSame(0, Answer::count());
    }

    public function test_answers_cannot_be_changed_after_the_deadline()
    {
        $party = Party::factory()->create(['deadline' => now()->subMinute()]);
        $participant = Participant::factory()->for($party)->create();
        $question = Question::first();

        $this->get(route('participant.show', $participant))
            ->assertOk()
            ->assertSee('The questionnaire closed')
            ->assertDontSee('Save my answers');

        $this->put(route('participant.answers.update', $participant), [
            'answers' => [$question->id => 'Too late'],
        ])->assertForbidden();

        $this->assertSame(0, Answer::count());
    }

    public function test_answers_can_be_changed_before_the_deadline()
    {
        $party = Party::factory()->create(['deadline' => now()->addDay()]);
        $participant = Participant::factory()->for($party)->create();
        $question = Question::first();

        $this->put(route('participant.answers.update', $participant), [
            'answers' => [$question->id => 'Just in time'],
        ])->assertRedirect();

        $this->assertSame(1, Answer::count());
    }
}
