<?php

namespace KillerQuote\Src\App\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Jacofda\Core\Models\Company;
use Jacofda\Core\Models\Product;
use Jacofda\Core\Models\Setting;
use KillerQuote\Src\App\Models\KillerQuote;
use KillerQuote\Src\App\Models\KillerQuoteItem;
use KillerQuote\Src\App\Models\KillerQuoteSetting;
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
            ->setOption('encoding', 'UTF-8');

        $logo->save($logoPdfPath);

        $document = PDF::loadView('killerquote::pdf.quote', compact('quote', 'settings', 'base_settings', 'fe_settings'))
            ->setPaper('a4')
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
        $companies = ['' => '']+Company::orderBy('rag_soc', 'ASC')->pluck('rag_soc', 'id')->toArray();
        $products = ['' => '']+Product::groupedOpt();
        return view('killerquote::quotes.quote.create', compact('companies', 'products'));
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->input(), [
            'itemsToForm' => 'json|required',
            'company_id' => 'exists:Jacofda\Core\Models\Product,id',
            'scadenza' => 'numeric',
            'summary' => 'nullable|string',
            'sconto_text' => 'nullable|string',
            'sconto_value' => 'nullable|numeric'
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
        $quote->sconto_text = $data['sconto_text'] ? $data['sconto_text'] : null;
        $quote->sconto_value = $data['sconto_value'] ? $data['sconto_value'] : null;
        $quote->expirancy_date = Carbon::now()->addDays(intval($data['scadenza']));
        $quote->save();
        $quote->items()->saveMany($items);

        return redirect(route('killerquotes.index'))->with('message', 'Preventivo Creato');
    }

    public function edit($id)
    {
        $quote = KillerQuote::findOrFail($id);
        $companies = ['' => '']+Company::orderBy('rag_soc', 'ASC')->pluck('rag_soc', 'id')->toArray();
        $products = ['' => '']+Product::groupedOpt();
        $items = $quote->items()->with('product')->get();
        return view('killerquote::quotes.quote.edit', compact('quote','items', 'companies', 'products'));
    }

    public function update(Request $request, $id)
    {
        $quote = KillerQuote::findOrFail($id);

        $v = Validator::make($request->input(), [
            'itemsToForm' => 'json|required',
            'company_id' => 'exists:Jacofda\Core\Models\Product,id',
            'scadenza' => 'numeric',
            'summary' => 'nullable|string',
            'sconto_text' => 'nullable|string',
            'sconto_value' => 'nullable|numeric'
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

        $oldItems = $quote->items()->get();

        $quote->company_id = $data['company_id'];
        $quote->summary = $data['summary'];
        $quote->sconto_text = $data['sconto_text'] ? $data['sconto_text'] : null;
        $quote->sconto_value = $data['sconto_value'] ? $data['sconto_value'] : null;
        $quote->expirancy_date = Carbon::now()->addDays(intval($data['scadenza']));
        $quote->save();
        $quote->items()->saveMany($items);

        foreach($oldItems as $oldItem) {
            $oldItem->delete();
        }

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
}
