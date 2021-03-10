<?php

namespace KillerQuote\App\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage, Validator, View};
use Areaseb\Core\Models\{Company, Event, Product, Setting};
use KillerQuote\App\Models\{KillerQuote, KillerQuoteItem};
use Illuminate\Support\Facades\Schema;
use KillerQuote\Mail\SendQuote;
use \PDF;

class QuotesController extends Controller
{
    public function create()
    {
        $companies = ['' => '']+Company::orderBy('rag_soc', 'ASC')->pluck('rag_soc', 'id')->toArray();
        return view('killerquote::quotes.simple.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $this->validate(request(),[
            'company_id' => 'required',
            'importo' => 'required',
            'expirancy' => 'required',
        ]);

        $company = Company::find($request->company_id);
        if($company->clients()->first()->id == 1)
        {
            $company->clients()->sync(2);
        }

        $path = $request->file('file')->store('public/killerquotes/original');
        $arr = explode('/', $path);
        $filename = end($arr);

        $quote = KillerQuote::create([
            'numero' => KillerQuote::getLastNumber(),
            'company_id' => $company->id,
            'user_id' => auth()->user()->id,
            'importo' => $request->importo,
            'filename' => $filename,
            'expirancy_date' => Carbon::createFromFormat('d/m/Y', $request->expirancy)
        ]);

        $this->syncEvent($quote);
        return redirect('killerquotes')->with('message', 'Preventivo PDF Creato');
    }

    public function update(Request $request, $id)
    {
        $this->validate(request(),[
            'company_id' => 'required',
            'importo' => 'required',
            'expirancy' => 'required',
        ]);

        $company = Company::find($request->company_id);
        if($request->accepted)
        {
            if($company->clients()->first()->id != 3)
            {
                $company->clients()->sync(3);
            }
        }

        $quote = KillerQuote::find($id);


        $this->syncEvent($quote);


        $quote->update([
            'importo' => $request->importo,
            'expirancy_date' => Carbon::createFromFormat('d/m/Y', $request->expirancy),
            'accepted' => $request->accepted
        ]);

        $this->syncEvent($quote);

        return redirect('killerquotes')->with('message', 'Preventivo PDF Aggiornato');
    }

    public function edit($id)
    {
        $quote = Killerquote::find($id);
        $company = Company::find($quote->company_id);
        $companies = [$company->id => $company->rag_soc];
        return view('killerquote::quotes.simple.edit', compact('companies', 'quote'));
    }

    public function syncEvent($quote)
    {
        $event = Event::where('calendar_id', KillerQuote::Calendar())->where('eventable_id', $quote->id)->where('eventable_type', get_class($quote))->first();

        if(is_null($event))
        {
            $event = Event::create([
                'calendar_id' => KillerQuote::Calendar(),
                'user_id' => auth()->user()->id,
                'eventable_id' => $quote->id,
                'eventable_type' => get_class($quote)
            ]);
        }

        $event->title = 'Prev. n. '.$quote->numero;
        $event->summary = 'Preventivo ' . $quote->numero . '/' . $quote->expirancy_date->format('Y') . ' a '. $quote->company->rag_soc;
        $event->starts_at = $quote->expirancy_date->format('Y-m-d').' 10:00:00';
        $event->ends_at = $quote->expirancy_date->format('Y-m-d').' 11:00:00';

        if(Carbon::now()->gt($quote->expirancy_date))
        {

            $event->backgroundColor = '#ecb204';
        }
        else
        {
            $event->backgroundColor = '#3788d8';
        }


        $event->save();

        $event->users()->sync(auth()->user()->id);
        $event->companies()->sync($quote->company_id);
    }


}
