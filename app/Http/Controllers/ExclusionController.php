<?php

namespace App\Http\Controllers;

use App\Models\Exclusion;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExclusionController extends Controller
{
    public function store(Request $request, Party $party)
    {
        $data = $request->validate([
            'from_id' => [
                'required',
                'exists:participants,id',
            ],
            'to_id' => [
                'required',
                'exists:participants,id',
                'different:from_id',
            ],
        ]);

        abort_if($party->began_at, Response::HTTP_FORBIDDEN);

        [$from, $to] = collect([
            $data['from_id'],
            $data['to_id'],
        ])->sort()->values();

        abort_if(
            $party->exclusions()->where('from_id', $from)->where('to_id', $to)->exists(),
            Response::HTTP_FORBIDDEN,
            'The given exclusion already exists'
        );

        Exclusion::create([
            'party_id' => $party->id,
            'from_id' => $from,
            'to_id' => $to,
        ]);

        return redirect()->back();
    }

    public function destroy(Request $request, Party $party, Exclusion $exclusion)
    {
        abort_if($party->began_at, Response::HTTP_FORBIDDEN);

        $exclusion->delete();

        return redirect()->back();
    }
}
