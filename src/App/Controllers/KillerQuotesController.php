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
use Areaseb\Core\Models\Company;
use Areaseb\Core\Models\Product;
use Areaseb\Core\Models\Setting;
use KillerQuote\App\Models\KillerQuote;
use KillerQuote\App\Models\KillerQuoteItem;
use KillerQuote\App\Models\KillerQuoteSetting;
use GrofGraf\LaravelPDFMerger\Facades\PDFMergerFacade as PDFMerger;
use \PDF;

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

        // $headerUrl = asset("storage/killerquotes/{$id}/header.html");
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

        $merger->addPathToPDF($logoPdfPath, [1], 'P');
        $merger->addPathToPDF($documentPdfPath, 'all', 'P');
        $merger->merge();
        return $merger->inline();
    }

    public function create()
    {
        $deals = [];
        if(class_exists("Deals\App\Models\Deal"))
            $deals = ['' => '']+Deal::where('status', Deal::STATUSES['open'])->orderBy('id', 'DESC')->pluck('id', 'id')->toArray();

        $companies = ['' => '']+Company::orderBy('rag_soc', 'ASC')->pluck('rag_soc', 'id')->toArray();
        $products = ['' => '']+Product::groupedOpt();
        return view('killerquote::quotes.quote.create', compact('companies', 'products', 'deals'));
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->input(), [
            'itemsToForm' => 'json|required',
            'company_id' => 'exists:Areaseb\Core\Models\Product,id',
            'scadenza' => 'required',
            'summary' => 'nullable|string',
            'sconto_text' => 'nullable|string',
            'sconto_value' => 'nullable|numeric',
            'notes' => 'nullable',
            'deal_id' => 'nullable|exists:Deals\App\Models\Deal',
            'accepted' => 'nullable'
        ]);


        if($v->fails())
            return redirect(route('killerquotes.create'))->with('errors', $v->errors());

        $data = $v->validated();
        $items = [];

        foreach(json_decode($data['itemsToForm']) as $item) {
            $sconto = 0;
            if(!is_null($item->sconto))
                $sconto = round( (1-$item->sconto/$item->prezzo)*100, 4);

            $i = new KillerQuoteItem();
            $i->product_id = $item->id;
            $i->descrizione = $item->descrizione;
            $i->qta = $item->qta;
            $i->sconto = $sconto;
            $i->perc_iva = $item->perc_iva;
            $i->iva = $item->ivato;
            $i->importo = $item->prezzo;

            $items[] = $i;
        }

        $quote = new KillerQuote();
        $quote->company_id = $data['company_id'];
        $quote->user_id = Auth::user()->id;
        $quote->summary = $data['summary'];
        $quote->notes = $data['notes'] ? $data['notes'] : null;
        if(isset($data['accepted']))
            $quote->accepted = $data['accepted'];
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
        $deals = [];
        if(class_exists("Deals\App\Models\Deal"))
            $deals = ['' => '']+Deal::where('status', Deal::STATUSES['open'])->orderBy('id', 'DESC')->pluck('id', 'id')->toArray();

        $quote = KillerQuote::findOrFail($id);
        $companies = ['' => '']+Company::orderBy('rag_soc', 'ASC')->pluck('rag_soc', 'id')->toArray();
        $products = ['' => '']+Product::groupedOpt();
        $items = $quote->items()->with('product')->get();
        return view('killerquote::quotes.quote.edit', compact('quote','items', 'companies', 'products', 'deals'));
    }

    public function attachToDeal($quote, $dealId) {
        DealEvent::where('dealable_id', $quote->id)->where('dealable_type', $quote->full_class)->delete();
        DealEvent::createEvent($dealId, DealEvent::EVENTS['killer_quote'], $quote->id, $quote->full_class, $quote->created_at);
    }

    public function update(Request $request, $id)
    {
        $quote = KillerQuote::findOrFail($id);

        $v = Validator::make($request->input(), [
            'itemsToForm' => 'json|required',
            'company_id' => 'exists:Areaseb\Core\Models\Product,id',
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
        $items = [];

        foreach(json_decode($data['itemsToForm']) as $item) {
            $sconto = 0;
            if(!is_null($item->sconto))
                $sconto = round( (1-$item->sconto/$item->prezzo)*100, 4);

            $i = new KillerQuoteItem();
            $i->product_id = $item->id;
            $i->descrizione = $item->descrizione;
            $i->qta = $item->qta;
            $i->sconto = $sconto;
            $i->perc_iva = $item->perc_iva;
            $i->iva = $item->ivato;
            $i->importo = $item->prezzo;

            $items[] = $i;
        }

        $oldItems = $quote->items()->get();
        $quote->company_id = $data['company_id'];
        $quote->summary = $data['summary'];
        $quote->notes = $data['notes'];
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

        if(!empty($data['deal_id']))
            $this->attachToDeal($quote, $data['deal_id']);

        return redirect(route('killerquotes.edit', $quote->id))->with('message', 'Preventivo Salvato');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(KillerQuote $quote)
    {
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

        $event->title = 'new prova';
        $event->summary = 'new message';
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
            $newQuote->accepted = $quote->accepted;
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

}
