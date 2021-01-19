<?php

namespace KillerQuote\App\Controllers;

use Carbon\Carbon;
use Deals\App\Models\Deal;
use Deals\App\Models\DealEvent;
use Deals\App\Models\DealGenericQuote;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Areaseb\Core\Models\Event;
use Areaseb\Core\Models\{Contact, Company};
use Areaseb\Core\Models\Product;
use Areaseb\Core\Models\Setting;
use KillerQuote\App\Models\KillerQuote;
use KillerQuote\App\Models\KillerQuoteItem;
use KillerQuote\App\Models\KillerQuoteSetting;
use GrofGraf\LaravelPDFMerger\Facades\PDFMergerFacade as PDFMerger;
use \PDF;
use Illuminate\Support\Facades\Schema;

class KillerQuotesController extends Controller
{
    public function index()
    {
        $quotes = KillerQuote::filter(request())->orderBy('id', 'DESC')->paginate(50);
        return view('killerquote::quotes.index.index', compact('quotes'));
    }

    public function pdf($id) {
        $quote = KillerQuote::find($id);
        if(!$quote)
            return abort(404);

        $media = [];
        foreach($quote->items as $item)
        {
            $pdf_attachment = $item->product->media()->pdf()->first();
            if($pdf_attachment)
            {
                $media[] = storage_path('app/public/products/docs/'.$pdf_attachment->filename);
            }
        }
        if(KillerQuoteSetting::HasDefaultPdfAttachment())
        {
            $media[] = KillerQuoteSetting::DefaultPdfAttachment();
        }

        $base_settings = Setting::base();
        $fe_settings = Setting::fe();
        $settings = KillerQuoteSetting::assoc();

        $path = "public/killerquotes/pdf/{$id}";

        if(Storage::exists($path))
            Storage::deleteDirectory($path);

        Storage::makeDirectory($path);

        $logoPdfPath = storage_path("app/{$path}/logo.pdf");
        $documentPdfPath = storage_path("app/{$path}/document.pdf");

        $header = View::make('killerquote::pdf.components.header', compact('settings', 'base_settings', 'fe_settings'))->render();
        $footer = View::make('killerquote::pdf.components.footer', compact('settings', 'base_settings', 'fe_settings'))->render();

        Storage::put("{$path}/header.html", $header);
        Storage::put("{$path}/footer.html", $footer);

        $headerUrl = asset("storage/killerquotes/pdf/{$id}/header.html");
        $footerUrl = asset("storage/killerquotes/pdf/{$id}/footer.html");

        $merger = PDFMerger::init();

        $logo = PDF::loadView('killerquote::pdf.logo', compact('quote', 'settings', 'base_settings', 'fe_settings'))
            ->setPaper('a4')
            ->setOption('enable-local-file-access', true)
            ->setOption('encoding', 'UTF-8');
        $logo->save($logoPdfPath);

        $document = PDF::loadView('killerquote::pdf.quote', compact('quote', 'settings', 'base_settings', 'fe_settings'))
            ->setPaper('a4')
            ->setOption('enable-local-file-access', true)
            ->setOption('header-spacing', 10)
            ->setOption('header-html', $headerUrl)
            ->setOption('footer-html', $footerUrl)
            ->setOption('encoding', 'UTF-8');
        $document->save($documentPdfPath);

        $merger->addPathToPDF($logoPdfPath, 'all', 'P');
        $merger->addPathToPDF($documentPdfPath, 'all', 'P');

        foreach($media as $attachment)
        {
            $merger->addPathToPDF($attachment, 'all', 'P');
        }

        $merger->merge();
        return $merger->inline();
    }

    public function create()
    {
        $deals = [];
        if(class_exists("Deals\App\Models\Deal"))
        {
            $deals = ['' => ''];
            $dealsC = Deal::whereNull('accepted')->orWhere('accepted', true)->orderBy('created_at', 'DESC')->where('created_at', '>',Carbon::today()->subMonth(4))->get();
            foreach($dealsC as $deal)
            {
                $deals[$deal->id] = $deal->company->rag_soc . " N." . sprintf('%03d', $deal->numero) . ' del ' . $deal->created_at->format('d/m/Y');
            }
        }

        $companies = ['' => '']+Company::orderBy('rag_soc', 'ASC')->pluck('rag_soc', 'id')->toArray();
        $products = ['' => '']+Product::groupedOpt();
        return view('killerquote::quotes.quote.create', compact('companies', 'products', 'deals'));
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->input(), [
            'itemsToForm' => 'json|required',
            'company_id' => 'exists:Areaseb\Core\Models\Company,id',
            'scadenza' => 'required',
            'summary' => 'nullable|string',
            'sconto_text' => 'nullable|string',
            'sconto_value' => 'nullable|numeric',
            'notes' => 'nullable',
            'deal_id' => 'nullable|exists:Deals\App\Models\Deal,id',
            'accepted' => 'nullable'
        ]);

        if($v->fails())
            return redirect(route('killerquotes.create'))->with('errors', $v->errors());

        $data = $v->validated();

        if(!isset($data['notes']))
        {
            $note = null;
        }
        else
        {
            $note = $data['notes'];
        }

        $items = [];

        $general_sconto = $data['sconto_value'] ? $data['sconto_value'] : 0;

        foreach(json_decode($data['itemsToForm']) as $item) {
            $sconto = 0;
            $percSconto = 0;

            if(!is_null($item->perc_sconto))
            {
                $percSconto = $item->perc_sconto/100;
                $sconto = $item->perc_sconto;
            }
            if($general_sconto)
            {
                $percSconto = $general_sconto/100;
                $sconto = $general_sconto;
            }

            if(config('sale_on_vat'))
            {
                $pYesIva = $item->prezzo * (1+($item->perc_iva/100)) * (1-$percSconto);
                $pNoiva = (100/(100+$item->perc_iva))*$pYesIva;
                $importo = $pNoiva;
                $iva = ($pYesIva-$pNoiva)* $item->qta;
            }
            else
            {
                $importo = $item->prezzo * (1-$percSconto);
                $iva = ($importo * (1-($item->perc_iva/100))) * $item->qta;
            }

            $i = new KillerQuoteItem();
            $i->product_id = $item->id;
            $i->descrizione = $item->descrizione;
            $i->qta = $item->qta;
            $i->sconto = $sconto;
            $i->perc_iva = $item->perc_iva;
            $i->iva = $iva;
            $i->importo = $importo;

            $items[] = $i;
        }

        $quote = new KillerQuote();
        $quote->company_id = $data['company_id'];
        $quote->user_id = Auth::user()->id;
        $quote->summary = $data['summary'];
        $quote->notes = $note;
        $quote->accepted = isset($data['accepted']) ? $data['accepted'] : null;
        $quote->sconto_text = $data['sconto_text'] ? $data['sconto_text'] : null;
        $quote->sconto_value = $data['sconto_value'] ? $data['sconto_value'] : null;
        $quote->expirancy_date = Carbon::createFromFormat('d/m/Y', $request->scadenza);
        $quote->numero = $this->getLatestNumber();
        $quote->save();
        $quote->items()->saveMany($items);
        $this->syncEvent($quote);

        if(!empty($data['deal_id']))
            $this->attachToDeal($quote, $data['deal_id']);

        return redirect(route('killerquotes.index'))->with('message', 'Preventivo Creato');
    }

    public function edit($id)
    {
        $quote = KillerQuote::findOrFail($id);
        $deals = [];
        if(class_exists("Deals\App\Models\Deal"))
        {
            $deals = ['' => ''];
            $dealsC = Deal::where('company_id', $quote->company_id)->orderBy('created_at', 'DESC')->where('created_at', '>',Carbon::today()->subMonth(4))->get();
            foreach($dealsC as $deal)
            {
                $deals[$deal->id] = $deal->company->rag_soc . " N." . sprintf('%03d', $deal->numero)  . ' del ' . $deal->created_at->format('d/m/Y');
            }
        }

        $companies = ['' => '']+Company::orderBy('rag_soc', 'ASC')->pluck('rag_soc', 'id')->toArray();
        $products = ['' => '']+Product::groupedOpt();
        $items = $quote->items()->with('product')->get();
        return view('killerquote::quotes.quote.edit', compact('quote','items', 'companies', 'products', 'deals'));
    }

    public function attachToDeal($quote, $dealId) {
        $deal = Deal::findOrFail($dealId);
        DealEvent::where('dealable_id', $quote->id)->where('dealable_type', $quote->full_class)->delete();
        $deal->rejectAllQuotes();

        DealEvent::createEvent($dealId, DealEvent::EVENTS['killer_quote'], $quote->id, $quote->full_class, $quote->created_at);
    }

    public function update(Request $request, $id)
    {
        $quote = KillerQuote::findOrFail($id);

        $v = Validator::make($request->input(), [
            'itemsToForm' => 'json|required',
            'company_id' => 'exists:Areaseb\Core\Models\Company,id',
            'scadenza' => 'required',
            'summary' => 'nullable|string',
            'sconto_text' => 'nullable|string',
            'sconto_value' => 'nullable|numeric',
            'notes' => 'nullable',
            'deal_id' => 'nullable',
            'accepted' => 'nullable'
        ]);

        if($v->fails())
            return redirect()->back()->with('errors', $v->errors());

        $data = $v->validated();

        if(!isset($data['notes']))
        {
            $note = null;
        }
        else
        {
            $note = $data['notes'];
        }

        $items = [];

        $general_sconto = $data['sconto_value'] ? $data['sconto_value'] : 0;

        foreach(json_decode($data['itemsToForm']) as $item) {
            $sconto = 0;
            $percSconto = 0;
            if(!is_null($item->perc_sconto))
            {
                $percSconto = $item->perc_sconto/100;
                $sconto = $item->perc_sconto;
            }
            if($general_sconto)
            {
                $percSconto = $general_sconto/100;
                $sconto = $general_sconto;
            }

            if(config('sale_on_vat'))
            {
                $importo = $item->prezzo * (1+($item->perc_iva/100)) * (1-$percSconto);
                $pNoiva = (100/(100+$item->perc_iva))*$importo;
                $iva = ($importo-$pNoiva)* $item->qta;
            }
            else
            {
                $importo = $item->prezzo * (1-$percSconto);
                $iva = ($importo * (1-($item->perc_iva/100))) * $item->qta;
            }

            $i = new KillerQuoteItem();
            $i->product_id = $item->id;
            $i->descrizione = $item->descrizione;
            $i->qta = $item->qta;
            $i->sconto = $sconto;
            $i->perc_iva = $item->perc_iva;
            $i->iva = $iva;
            $i->importo = $importo;

            $items[] = $i;
        }

        $oldItems = $quote->items()->get();
        $quote->company_id = $data['company_id'];
        $quote->summary = $data['summary'];
        $quote->notes = $note;
        $quote->accepted = $data['accepted'];
        $quote->sconto_text = $data['sconto_text'] ? $data['sconto_text'] : null;
        $quote->sconto_value = $data['sconto_value'] ? $data['sconto_value'] : null;
        $quote->expirancy_date = Carbon::createFromFormat('d/m/Y', $request->scadenza);
        $quote->save();
        $quote->items()->saveMany($items);

        foreach($oldItems as $oldItem) {
            $oldItem->delete();
        }

        $this->syncEvent($quote);

        if(!empty($data['deal_id'])) {
            $this->attachToDeal($quote, $data['deal_id']);
        }

        return redirect(route('killerquotes.edit', $quote->id))->with('message', 'Preventivo Salvato');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($quote_id)
    {
        $quote = KillerQuote::find($quote_id);
        foreach(Event::where('eventable_type', get_class($quote))->where('eventable_id', $quote->id)->get() as $event )
        {
            $event->delete();
        }

        foreach($quote->items as $item)
        {
            $item->delete();
        }

        if( Schema::hasTable('deal_events'))
        {
            if(\DB::table('deal_events')->where('dealable_type', get_class($quote))->where('dealable_id', $quote->id)->exists())
            {
                \DB::table('deal_events')->where('dealable_type', get_class($quote))->where('dealable_id', $quote->id)->delete();
            }
        }

        $quote->delete();

        return 'done';
    }

    /**
     * createOrUpdate Event "scadenza preventivo"
     * @param  [eloquent] $quote
     * @return [void]
     */
    public function syncEvent($quote)
    {
        $event = Event::firstOrCreate([
            'calendar_id' => KillerQuote::Calendar(),
            'user_id' => auth()->user()->id,
            'eventable_id' => $quote->id,
            'eventable_type' => get_class($quote)
        ]);

        $event->title = 'Prev. n. '.$quote->numero;
        $event->summary = 'Preventivo ' . $quote->numero . '/' . $quote->expirancy_date->format('Y') . ' a '. $quote->company->rag_soc;
        $event->starts_at = $quote->expirancy_date->format('Y-m-d').' 10:00:00';
        $event->ends_at = $quote->expirancy_date->format('Y-m-d').' 11:00:00';
        $event->backgroundColor = '#3788d8';

        $event->save();

        $event->users()->sync(auth()->user()->id);
        $event->companies()->sync($quote->company_id);
    }

//killerquotes/{quotes}/duplicate - POST
    public function duplicate(Request $request, KillerQuote $quote)
    {

        $newQuote = new KillerQuote();
            $newQuote->company_id = $quote->company_id;
            $newQuote->user_id = auth()->user()->id;
            $newQuote->summary = $quote->summary;
            $newQuote->notes = $quote->notes;
            $newQuote->sconto_text = $quote->sconto_text;
            $newQuote->sconto_value = $quote->sconto_value;
            $newQuote->expirancy_date = $quote->expirancy_date;
            $newQuote->numero = $this->getLatestNumber();
        $newQuote->save();

        foreach($quote->items as $item)
        {
            $newItem = $item->replicate();
            $newItem->invoice_id = $newQuote->id;
            $newItem->save();
        }

        $this->syncEvent($newQuote);
        return redirect(route('killerquotes.edit', $newQuote->id));
    }

    public function getLatestNumber() {
        $numero = KillerQuote::getLastNumber();
        if(class_exists(DealGenericQuote::class)) {
            $numero = max($numero, DealGenericQuote::getLastNumber());
        }
        return $numero+1;
    }

    public function makeCompanyAndQuote(Request $request)
    {
        $contact = Contact::find($request->id);
        $company = new Company;
            $company->rag_soc = $contact->fullname;
            $company->indirizzo = $contact->indirizzo;
            $company->cap = $contact->cap;
            $company->citta = $contact->citta;
            $company->provincia = $contact->provincia;
            $company->city_id = $contact->city_id;
            $company->nazione = $contact->nazione;
            $company->lingua = $contact->lingua;
            $company->email = $contact->email;
        $company->save();

        $contact->company_id = $company->id;
        $contact->save();

        $company->clients()->save($contact->clients()->first());

        return redirect('killerquotes/create?company_id='.$company->id)->with('message', 'Azienda da contatto creata!');
    }

}
