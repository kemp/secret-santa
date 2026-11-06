<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Participant;
use App\Models\Party;
use App\Models\Question;
use App\Models\SecretSanta;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed a demo party for local development. Questions come from the migrations.
     */
    public function run(): void
    {
        $party = Party::factory()->create(['deadline' => now()->setDate(now()->year, 12, 24)->endOfDay()]);

        $creator = Participant::factory()->creator()->for($party)->create(['name' => 'Linda', 'email' => 'linda@example.com']);
        $people = collect([$creator])->merge(
            collect(['Marcus', 'Priya', 'Tom'])->map(fn ($name) => Participant::factory()->for($party)->create(['name' => $name]))
        );

        $samples = [
            'Pottery! Or maybe bouldering.',
            'Please no more scented candles.',
            'A really nice fountain pen.',
            'A good coffee in the morning ☕',
            'My running shoes are falling apart.',
            'M / 9.5 US / no rings. Forest green.',
            'Allergic to lavender.',
            'Cozy',
        ];

        $questions = Question::forYear($party->year)->get();

        // Marcus has answered everything, Priya a few things, Tom nothing.
        foreach (['Marcus' => 8, 'Priya' => 3] as $name => $count) {
            $participant = $people->firstWhere('name', $name);

            $questions->take($count)->each(fn (Question $question, $index) => Answer::create([
                'participant_id' => $participant->id,
                'question_id' => $question->id,
                'year' => $party->year,
                'body' => $samples[$index] ?? 'Surprise me!',
            ]));
        }

        // A second party that has already been drawn, to preview the Santa view.
        $drawn = Party::factory()->create(['began_at' => now()]);
        $santa = Participant::factory()->creator()->for($drawn)->create(['name' => 'Sam']);
        $recipient = Participant::factory()->for($drawn)->create(['name' => 'Marcus']);
        Participant::factory()->for($drawn)->create(['name' => 'Joy']);

        $questions->each(fn (Question $question, $index) => Answer::create([
            'participant_id' => $recipient->id,
            'question_id' => $question->id,
            'year' => $drawn->year,
            'body' => $index === 4 ? null : $samples[$index],
        ]));

        SecretSanta::create(['party_id' => $drawn->id, 'from_id' => $santa->id, 'to_id' => $recipient->id]);
    }
}
