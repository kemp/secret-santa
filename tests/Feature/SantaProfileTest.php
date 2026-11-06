<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Participant;
use App\Models\Party;
use App\Models\Question;
use App\Models\SecretSanta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SantaProfileTest extends TestCase
{
    use RefreshDatabase;

    protected Party $party;

    protected Participant $santa;

    protected Participant $recipient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->party = Party::factory()->create(['began_at' => now()]);
        $this->santa = Participant::factory()->for($this->party)->create(['name' => 'Santa']);
        $this->recipient = Participant::factory()->for($this->party)->create(['name' => 'Rudy']);

        SecretSanta::create(['party_id' => $this->party->id, 'from_id' => $this->santa->id, 'to_id' => $this->recipient->id]);
    }

    public function test_santa_sees_recipient_answers_and_blank_questions_are_skipped()
    {
        [$first, $second, $third] = Question::forYear($this->party->year)->take(3)->get();

        Answer::create(['participant_id' => $this->recipient->id, 'question_id' => $first->id, 'year' => $this->party->year, 'body' => 'Learning the ukulele']);
        Answer::create(['participant_id' => $this->recipient->id, 'question_id' => $second->id, 'year' => $this->party->year, 'body' => null]);
        // An answer from a different year should not show up.
        Answer::create(['participant_id' => $this->recipient->id, 'question_id' => $third->id, 'year' => $this->party->year - 1, 'body' => 'Old news']);

        $profile = $this->recipient->profile();

        $this->assertCount(1, $profile);
        $this->assertSame($first->id, $profile->first()['question']->id);

        $this->get(route('participant.show', $this->santa))
            ->assertOk()
            ->assertSee("You're the Secret Santa for", false)
            ->assertSee('Rudy')
            ->assertSee('Learning the ukulele')
            ->assertDontSee('Old news')
            ->assertDontSee("hasn't answered any questions yet", false);
    }

    public function test_friendly_empty_state_when_recipient_answered_nothing()
    {
        $this->get(route('participant.show', $this->santa))
            ->assertOk()
            ->assertSee("Rudy hasn't answered any questions yet.", false);
    }

    public function test_legacy_wishlist_is_still_shown()
    {
        $this->recipient->update(['wishlist' => 'A nice scarf']);

        $this->get(route('participant.show', $this->santa))
            ->assertSee('Wishlist')
            ->assertSee('A nice scarf')
            ->assertDontSee("hasn't answered any questions yet", false);
    }

    public function test_recipient_is_hidden_before_the_draw()
    {
        $this->party->update(['began_at' => null]);

        $this->get(route('participant.show', $this->santa))
            ->assertOk()
            ->assertSee("Names haven't been drawn yet", false)
            ->assertDontSee("You're the Secret Santa for", false);
    }

    public function test_recipient_does_not_see_who_their_santa_is()
    {
        $this->get(route('participant.show', $this->recipient))
            ->assertOk()
            ->assertDontSee("You're the Secret Santa for Santa", false);
    }
}
