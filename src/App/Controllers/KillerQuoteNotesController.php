<?php

namespace KillerQuote\App\Controllers;

use Illuminate\Http\Request;
use KillerQuote\App\Models\{KillerQuote, KillerQuoteNote};


class KillerQuoteNotesController extends Controller
{

    public function show(KillerQuote $killerquote)
    {
        return view('killerquote::quotes.notes.create', compact('killerquote'));
    }

    public function create(KillerQuote $killerquote)
    {
        $notes = KillerQuoteNote::where('killer_quote_id', $killerquote->id)->get();
        return view('killerquote::quotes.notes.create', compact('killerquote', 'notes'));
    }

    public function store(Request $request, KillerQuote $killerquote)
    {
        KillerQuoteNote::create([
            'killer_quote_id' => $killerquote->id,
            'note' => $request->note
        ]);
        return back();
    }

    public function edit(KillerQuote $killerquote, KillerQuoteNote $note)
    {
        return view('killerquote::quotes.notes.edit', compact('killerquote', 'note'));
    }

    public function update(Request $request, KillerQuote $killerquote, KillerQuoteNote $note)
    {
        $note = KillerQuoteNote::find($id);
    }

    public function destroy(KillerQuote $killerquote, KillerQuoteNote $note)
    {

        $note->delete();
        if(request()->has('origin'))
        {
            return back();
        }
        return 'done';
    }



}
