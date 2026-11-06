<?php

namespace Tests\Feature;

use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminQuestionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['santa.admin.username' => 'elf', 'santa.admin.password' => 'mistletoe']);
    }

    protected function asAdmin(): static
    {
        return $this->withBasicAuth('elf', 'mistletoe');
    }

    public function test_admin_is_hidden_when_no_password_is_configured()
    {
        config(['santa.admin.password' => null]);

        $this->get('/admin/questions')->assertNotFound();
    }

    public function test_admin_requires_credentials()
    {
        $this->get('/admin/questions')->assertUnauthorized()->assertHeader('WWW-Authenticate');
        $this->withBasicAuth('elf', 'wrong')->get('/admin/questions')->assertUnauthorized();
    }

    public function test_admin_accepts_a_bcrypt_password()
    {
        config(['santa.admin.password' => Hash::make('hashed-secret')]);

        $this->withBasicAuth('elf', 'hashed-secret')->get('/admin/questions')->assertOk();
    }

    public function test_admin_lists_questions()
    {
        $this->asAdmin()->get('/admin/questions')
            ->assertOk()
            ->assertSee('Is there anything you really don&#039;t want?', false);
    }

    public function test_admin_can_add_a_one_off_question_with_choices()
    {
        $this->asAdmin()->post(route('admin.questions.store'), [
            'text' => 'Sweet or salty?',
            'year' => 2026,
            'sort_order' => 5,
            'is_active' => '1',
            'options' => 'Sweet, Salty, , Sweet',
        ])->assertRedirect(route('admin.questions.index'));

        $question = Question::where('text', 'Sweet or salty?')->firstOrFail();

        $this->assertSame(2026, $question->year);
        $this->assertSame(['Sweet', 'Salty'], $question->options);
        $this->assertTrue($question->isAskedIn(2026));
        $this->assertFalse($question->isAskedIn(2027));
    }

    public function test_admin_can_edit_a_question()
    {
        $question = Question::first();

        $this->asAdmin()->put(route('admin.questions.update', $question), [
            'text' => 'A better question?',
            'year' => '',
            'sort_order' => 99,
            'is_active' => '0',
            'options' => '',
        ])->assertRedirect();

        $question->refresh();
        $this->assertSame('A better question?', $question->text);
        $this->assertNull($question->year);
        $this->assertSame(99, $question->sort_order);
        $this->assertFalse($question->is_active);
        $this->assertNull($question->options);
    }

    public function test_admin_can_deactivate_and_reactivate_a_question()
    {
        $question = Question::first();

        $this->asAdmin()->post(route('admin.questions.toggle', $question));
        $this->assertFalse($question->fresh()->is_active);

        $this->asAdmin()->post(route('admin.questions.toggle', $question));
        $this->assertTrue($question->fresh()->is_active);
    }

    public function test_admin_can_reorder_questions()
    {
        [$first, $second] = Question::orderBy('sort_order')->take(2)->get();

        $this->asAdmin()->post(route('admin.questions.move', [$second, 'up']));

        $this->assertSame(
            [$second->id, $first->id],
            Question::orderBy('sort_order')->take(2)->pluck('id')->all(),
        );
    }
}
