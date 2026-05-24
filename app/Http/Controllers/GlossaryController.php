<?php

namespace App\Http\Controllers;

use App\Models\GlossaryTerm;

class GlossaryController extends Controller
{
    public function index()
    {
        $terms = GlossaryTerm::orderBy('letter')->orderBy('term')->get();

        $byLetter = $terms->groupBy('letter');
        $letters  = $byLetter->keys()->sort()->values();
        $total    = $terms->count();

        return view('glosario.index', compact('byLetter', 'letters', 'total'));
    }
}
