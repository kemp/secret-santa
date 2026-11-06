<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    /**
     * List every question, grouped into what's asked this year and the rest.
     */
    public function index(Request $request)
    {
        $year = (int) $request->query('year', now()->year);

        $questions = Question::query()
            ->withCount(['answers' => fn ($query) => $query->whereNotNull('body')->where('body', '!=', '')])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.questions.index', [
            'year' => $year,
            'asked' => $questions->filter(fn (Question $question) => $question->isAskedIn($year))->values(),
            'other' => $questions->reject(fn (Question $question) => $question->isAskedIn($year))->values(),
        ]);
    }

    /**
     * Show the form to add a question.
     */
    public function create()
    {
        return view('admin.questions.form', [
            'question' => new Question([
                'is_active' => true,
                'sort_order' => (Question::max('sort_order') ?? 0) + 10,
            ]),
        ]);
    }

    /**
     * Store a new question.
     */
    public function store(Request $request)
    {
        $question = Question::create($this->validated($request));

        return redirect()->route('admin.questions.index')->with('success', "Added \"{$question->text}\".");
    }

    /**
     * Show the form to edit a question.
     */
    public function edit(Question $question)
    {
        return view('admin.questions.form', compact('question'));
    }

    /**
     * Update a question.
     */
    public function update(Request $request, Question $question)
    {
        $question->update($this->validated($request));

        return redirect()->route('admin.questions.index')->with('success', "Updated \"{$question->text}\".");
    }

    /**
     * Turn a question on or off. Questions are never deleted so that past
     * years' answers keep their labels.
     */
    public function toggle(Question $question)
    {
        $question->update(['is_active' => ! $question->is_active]);

        return redirect()->back()->with('success', $question->is_active
            ? "\"{$question->text}\" is back on the questionnaire."
            : "\"{$question->text}\" has been switched off.");
    }

    /**
     * Swap a question with its neighbour in the sort order.
     */
    public function move(Question $question, string $direction)
    {
        $neighbour = Question::query()
            ->where('id', '!=', $question->id)
            ->when(
                $direction === 'up',
                fn ($query) => $query->where('sort_order', '<=', $question->sort_order)->orderByDesc('sort_order')->orderByDesc('id'),
                fn ($query) => $query->where('sort_order', '>=', $question->sort_order)->orderBy('sort_order')->orderBy('id'),
            )
            ->first();

        if ($neighbour) {
            [$a, $b] = [$question->sort_order, $neighbour->sort_order];

            // Equal sort orders can't be swapped meaningfully; nudge them apart.
            if ($a === $b) {
                $b = $direction === 'up' ? $a - 1 : $a + 1;
            }

            $question->update(['sort_order' => max(0, $b)]);
            $neighbour->update(['sort_order' => max(0, $a)]);
        }

        return redirect()->back();
    }

    /**
     * Validate and normalise the question form.
     *
     * @return array<string, mixed>
     */
    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'text' => 'required|string|max:255',
            'year' => 'nullable|integer|min:2000|max:2100',
            'sort_order' => 'required|integer|min:0|max:100000',
            'is_active' => 'nullable|boolean',
            'options' => 'nullable|string|max:500',
        ]);

        $options = collect(explode(',', $data['options'] ?? ''))
            ->map(fn ($option) => Str::squish($option))
            ->filter()
            ->unique()
            ->values();

        return [
            'text' => $data['text'],
            'year' => $data['year'] ?? null,
            'sort_order' => $data['sort_order'],
            'is_active' => $request->boolean('is_active'),
            'options' => $options->isEmpty() ? null : $options->all(),
        ];
    }
}
