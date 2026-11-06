<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The starter questionnaire. Asked every year until changed in /admin.
     *
     * @var array<int, array{0: string, 1?: array<int, string>}>
     */
    protected array $questions = [
        ['Is there a hobby you\'ve been wanting to get into?'],
        ['Is there anything you really don\'t want?'],
        ['What\'s something you\'d never buy yourself but would love to receive?'],
        ['What\'s a small thing that makes your day better?'],
        ['Is anything of yours worn out and due for replacement?'],
        ['Sizes (shirt / shoe / ring) and a favorite color'],
        ['Anything you\'re allergic to or avoiding? (food, scents, materials)'],
        ['Pick one: cozy, practical, funny, or fancy', ['Cozy', 'Practical', 'Funny', 'Fancy']],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        foreach ($this->questions as $index => $question) {
            DB::table('questions')->insert([
                'text' => $question[0],
                'year' => null,
                'sort_order' => ($index + 1) * 10,
                'is_active' => true,
                'options' => isset($question[1]) ? json_encode($question[1]) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('questions')
            ->whereIn('text', array_column($this->questions, 0))
            ->delete();
    }
};
