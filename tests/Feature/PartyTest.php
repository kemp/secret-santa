<?php

namespace Tests\Feature;

use App\Mail\CreatorEditLink;
use App\Mail\ParticipantLink;
use App\Mail\SecretSantaInitiated;
use App\Models\Answer;
use App\Models\Participant;
use App\Models\Question;
use App\Models\SecretSanta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PartyTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads()
    {
        $this->get('/')->assertOk()->assertSee('Start the '.now()->year.' Secret Santa');
    }

    public function test_creating_a_party_takes_the_organiser_to_their_questionnaire()
    {
        Mail::fake();

        $response = $this->post('/', [
            'name' => 'Linda',
            'email' => 'linda@example.com',
            'deadline' => now()->addWeek()->toDateString(),
        ]);

        $creator = Participant::where('email', 'linda@example.com')->firstOrFail();

        $response->assertRedirect(route('participant.show', $creator));
        $this->assertNotNull($creator->edit_token);
        $this->assertSame(32, strlen($creator->access_token));
        $this->assertSame(now()->year, $creator->party->year);
        $this->assertTrue($creator->party->deadline->isSameDay(now()->addWeek()));
        Mail::assertSent(CreatorEditLink::class);
    }

    public function test_joining_sends_a_private_link_and_redirects_to_the_questionnaire()
    {
        Mail::fake();
        $party = Participant::factory()->creator()->create()->party;

        $response = $this->post(route('party.participants.store', $party), [
            'name' => 'Bob',
            'email' => 'bob@example.com',
        ]);

        $bob = Participant::where('email', 'bob@example.com')->firstOrFail();
        $response->assertRedirect(route('participant.show', $bob));
        Mail::assertQueued(ParticipantLink::class, fn ($mail) => $mail->hasTo('bob@example.com'));

        $this->followRedirects($response)->assertSee('Your questionnaire');
    }

    public function test_the_same_email_cannot_join_twice()
    {
        $party = Participant::factory()->creator()->create(['email' => 'linda@example.com'])->party;

        $this->post(route('party.participants.store', $party), [
            'name' => 'Linda again',
            'email' => 'Linda@Example.com',
        ])->assertSessionHasErrors('email');
    }

    public function test_a_lost_link_can_be_resent_without_revealing_who_joined()
    {
        Mail::fake();
        $party = Participant::factory()->creator()->create(['email' => 'linda@example.com'])->party;

        $this->post(route('party.resend-link', $party), ['email' => 'LINDA@example.com'])->assertSessionHas('success');
        $this->post(route('party.resend-link', $party), ['email' => 'nobody@example.com'])->assertSessionHas('success');

        Mail::assertQueuedCount(1);
        Mail::assertQueued(ParticipantLink::class, fn ($mail) => $mail->hasTo('linda@example.com'));
    }

    public function test_creator_can_change_the_deadline()
    {
        $creator = Participant::factory()->creator()->create();
        $party = $creator->party;

        $this->patch(route('party.deadline.update', ['party' => $party, 'edit_token' => $creator->edit_token]), [
            'deadline' => '2026-12-20',
        ])->assertRedirect();

        $this->assertSame('2026-12-20 23:59:59', $party->fresh()->deadline->format('Y-m-d H:i:s'));

        $this->patch(route('party.deadline.update', ['party' => $party, 'edit_token' => 'nope']), [
            'deadline' => '2026-12-21',
        ])->assertForbidden();
    }

    public function test_drawing_names_emails_everyone_their_recipients_answers()
    {
        Mail::fake();

        $creator = Participant::factory()->creator()->create();
        $party = $creator->party;
        Participant::factory()->for($party)->count(2)->create();

        $question = Question::first();
        $party->participants->each(fn ($participant) => Answer::create([
            'participant_id' => $participant->id,
            'question_id' => $question->id,
            'year' => $party->year,
            'body' => "{$participant->name} likes jigsaws",
        ]));

        $this->post(route('party.initiate', ['party' => $party, 'edit_token' => $creator->edit_token]))->assertRedirect();

        $this->assertNotNull($party->fresh()->began_at);
        $this->assertSame(3, SecretSanta::count());

        Mail::assertQueued(SecretSantaInitiated::class, 3);
        Mail::assertQueued(SecretSantaInitiated::class, function (SecretSantaInitiated $mail) {
            $recipient = $mail->secretSanta->to;

            return str_contains($mail->render(), "{$recipient->name} likes jigsaws");
        });
    }

    public function test_party_page_shows_participants()
    {
        $creator = Participant::factory()->creator()->create(['name' => 'Linda']);

        $this->get(route('party.show', $creator->party))
            ->assertOk()
            ->assertSee('Linda has invited you to the '.now()->year.' Secret Santa')
            ->assertSee('Want to join?');
    }

    public function test_organiser_tools_need_the_edit_token()
    {
        $creator = Participant::factory()->creator()->create();
        Participant::factory()->for($creator->party)->count(2)->create();

        $this->get(route('party.show', $creator->party))->assertDontSee('Organiser tools');
        $this->get(route('party.show', ['party' => $creator->party, 'edit_token' => $creator->edit_token]))
            ->assertSee('Organiser tools')
            ->assertSee('Draw names');
    }
}
