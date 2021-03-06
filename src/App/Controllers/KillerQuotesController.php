<?php

namespace KillerQuote\App\Controllers;

use Carbon\Carbon;
use Deals\App\Models\{Deal, DealEvent, DealGenericQuote};
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage, Validator, View};
use Areaseb\Core\Models\{Company, Contact, Event, Product, Setting};
use KillerQuote\App\Models\{Quote, KillerQuote, KillerQuoteItem, KillerQuoteSetting, KillerQuoteSettingLocale};
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Illuminate\Support\Facades\Schema;
use KillerQuote\Mail\SendQuote;
use \PDF;

class KillerQuotesController extends Controller
{

    public function index()
    {
        $collection = collect();

        $killer = KillerQuote::filter(request())->get();
        $collection = $collection->concat($killer);
        if(Schema::hasTable('deals'))
        {
            $generic = collect();
            if(request()->has('company'))
            {
                $dg = Deal::where('company_id', request('company'))->get();
            }
            else
            {
                $dg = Deal::all();
            }

            foreach($dg as $d)
            {
                $gq = $d->events()->where('type',1)->pluck('dealable_id')->toArray();

                if(count($gq))
                {
                    $queryDGQ = DealGenericQuote::whereIn('id',$gq);
                    if(request()->has('expired'))
                    {
                        if(is_null(request('expired')))
                        {
                        }
                        elseif(intval(request('expired')) === 1)
                        {
                            $queryDGQ = $queryDGQ->where('expirancy_date', '<', Carbon::now()->format('Y-m-d'));
                        }
                        elseif(intval(request('expired')) === 0)
                        {
                            $queryDGQ = $queryDGQ->where('expirancy_date', '>=', Carbon::now()->format('Y-m-d'));
                        }
                    }

                    foreach($queryDGQ->get() as $q)
                    {

                        $col = collect();

                        if(Schema::hasTable('testimonials'))
                        {
                            if($d->company->testimonial()->exists())
                            {
                                $testimonial = $d->company->testimonial()->first();
                                $perc = $testimonial->commission/100;
                                $col->commissione = round($q->importo*$perc, 2);
                            }
                        }

                        if(Schema::hasTable('agents'))
                        {
                            if($d->company->agent()->exists())
                            {
                                $testimonial = $d->company->agent()->first();
                                $perc = $testimonial->commission/100;
                                $col->commissione = round($q->importo*$perc, 2);
                            }
                        }

                        $col->media_id = $q->media_id;
                        $col->filename = $q->media()->first()->filename;
                        $col->accepted = $q->accepted;
                        $col->numero = $q->numero;
                        $col->expirancy_date = $q->expirancy_date;
                        $col->created_at = $q->created_at;
                        $col->company_id = $d->company_id;
                        $col->company = $d->company->rag_soc;
                        $col->id = $q->id;
                        $col->importo = $q->importo;
                        $col->deal = $d->id;
                        $generic->push($col);
                    }
                }

            }
            $collection = $collection->concat($generic);
        }
        $quotes = $collection->all();
        return view('killerquote::quotes.index.index', compact('quotes'));
    }

    public function pdf($id) {
        $quote = KillerQuote::find($id);
        if(!$quote)
            return abort(404);

        $pdf = $this->generatePdfIta($quote);

        $filename = storage_path('app/public/killerquotes/pdf/'.time().'.pdf');
        $pdf->save($filename, 'file');
        return response()->file($filename);
    }

    public function pdfLocale($id, $locale)
    {
        app()->setLocale($locale);
        $quote = KillerQuote::find($id);

        $pdf = $this->generatePdf($quote, $locale)->inline();

        $filename = storage_path('app/public/killerquotes/pdf/'.time().'.pdf');
        $pdf->save($filename, 'file');
        return response()->file($filename);
    }

    public function create()
    {
        $deals = [];
        if(class_exists("Deals\App\Models\Deal"))
            $deals = ['' => '']+Deal::where('accepted', Deal::STATUSES['open'])->orderBy('id', 'DESC')->pluck('id', 'id')->toArray();

        $companies = ['' => '']+Company::orderBy('rag_soc', 'ASC')->pluck('rag_soc', 'id')->toArray();
        $products = ['' => '']+Product::groupedOpt();
        $nazione = null;
        return view('killerquote::quotes.quote.create', compact('companies', 'products', 'deals', 'nazione'));
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
            'accepted' => 'nullable',
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
        $company = Company::find($data['company_id']);

        foreach(json_decode($data['itemsToForm']) as $item) {

            $sconto = 0;
            $percSconto = 0;

            if(!is_null($item->perc_sconto))
            {
                $percSconto = $item->perc_sconto/100;
                $sconto = $item->perc_sconto;
            }

            if(config('app.sale_on_vat') && ($company->nazione != 'IT'))
            {
                $importo = $item->prezzo;
                $iva = 0;
            }
            else
            {
                $importo = $item->prezzo;
                $iva = ($importo * ($item->perc_iva/100)) * $item->qta;
            }

            $i = new KillerQuoteItem();
            $i->product_id = $item->id;
            $i->descrizione = $item->descrizione;
            $i->qta = $item->qta;
            $i->sconto = $sconto;
            $i->perc_iva = ($iva == 0) ? 0 : $item->perc_iva;
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

        $quote->update(['importo' => $quote->calculate_importo]);

        $company = Company::find($request->company_id);
        if($company->clients()->first()->point == 1)
        {
            $company->clients()->sync(1);

            if($company->contacts()->exists())
            {
                foreach($company->contacts as $contact)
                {
                    if($contact->clients()->first())
                    {
                        $contact->clients()->sync(1);
                    }
                }
            }
        }

        return redirect(route('killerquotes.index'))->with('message', 'Preventivo Creato');
    }

    public function edit($id)
    {
        $deals = [];
        if(class_exists("Deals\App\Models\Deal"))
            $deals = ['' => '']+Deal::where('accepted', Deal::STATUSES['open'])->orderBy('id', 'DESC')->pluck('id', 'id')->toArray();

        $quote = KillerQuote::findOrFail($id);
        $companies = ['' => '']+Company::orderBy('rag_soc', 'ASC')->pluck('rag_soc', 'id')->toArray();
        $products = ['' => '']+Product::groupedOpt();
        $items = $quote->items()->with('product')->get();

        $nazione = $quote->company->nazione;

        return view('killerquote::quotes.quote.edit', compact('quote','items', 'companies', 'products', 'deals', 'nazione'));
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
        $company = Company::find($data['company_id']);

        foreach(json_decode($data['itemsToForm']) as $item) {
            $sconto = 0;
            $percSconto = 0;
            if(!is_null($item->perc_sconto))
            {
                $percSconto = $item->perc_sconto/100;
                $sconto = $item->perc_sconto;
            }

            if(config('app.sale_on_vat') && ($company->nazione != 'IT'))
            {
                //dd($item);
                $importo = $item->prezzo;
                $iva = 0;
            }
            else
            {
                $iva = ($item->prezzo * ($item->perc_iva/100)) * $item->qta;
            }

            $i = new KillerQuoteItem();
            $i->product_id = $item->id;
            $i->descrizione = $item->descrizione;
            $i->qta = $item->qta;
            $i->sconto = $sconto;
            $i->perc_iva = ($iva == 0) ? 0 : $item->perc_iva;
            $i->iva = $iva;
            $i->importo = $item->prezzo;

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

        $quote->update(['importo' => $quote->calculate_importo]);

        if($quote->accepted === 1)
        {
            $company = Company::find($request->company_id);
            $company->clients()->sync(3);

            if($company->contacts()->exists())
            {
                foreach($company->contacts as $contact)
                {
                    if($contact->clients()->first())
                    {
                        $contact->clients()->sync(3);
                    }
                }
            }
        }
        else
        {
            $company = Company::find($request->company_id);
            $company->clients()->sync(1);

            if($company->contacts()->exists())
            {
                foreach($company->contacts as $contact)
                {
                    if($contact->clients()->first())
                    {
                        $contact->clients()->sync(1);
                    }
                }
            }
        }



        return redirect(route('killerquotes.index'))->with('message', 'Preventivo Aggiornato');
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
        if($quote->items()->exists())
        {
            foreach($quote->items as $item)
            {
                $item->delete();
            }
        }

        if(KillerQuoteNote::where('killer_quote_id', $quote_id)->exists())
        {
            foreach(KillerQuoteNote::where('killer_quote_id', $quote_id)->get() as $note)
            {
                $note->delete();
            }
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
        return KillerQuote::getLastNumber();
    }


    public function sendPdf(Request $request, $id)
    {
        $quote = KillerQuote::find($id);
        if(is_null($quote->filename))
        {
            if($quote->company->lingua == 'it')
            {
                $pdf = $this->generatePdfIta($quote);
                $fileWithPath = storage_path('app/public/killerquotes/pdf/'.$quote->id.'/preventivo.pdf');
            }
            else
            {
                $pdf = $this->generatePdf($quote, $quote->company->lingua);
                $fileWithPath = storage_path('app/public/killerquotes/pdf/'.$quote->company->lingua.'/'.$quote->id.'/preventivo.pdf');
            }
            if (file_exists($fileWithPath))
            {
                unlink($fileWithPath);
            }
            $pdf->save($fileWithPath);
        }
        else
        {
            $fileWithPath = storage_path('app/public/killerquotes/original/'.$quote->filename);
        }

        $mailer = app()->makeWith('custom.mailer', Setting::smtp(0));
        try
        {
            $mailer->send(new SendQuote($fileWithPath, $quote->company, $request->object, $request->body));
            return back()->with('message', 'Preventivo inviato correttamente');
        }
        catch(\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }

    }

    private function generatePdfIta($quote)
    {
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

        $path = "public/killerquotes/pdf/{$quote->id}";

        if(Storage::exists($path))
            Storage::deleteDirectory($path);

        Storage::makeDirectory($path);

        $logoPdfPath = storage_path("app/{$path}/logo.pdf");
        $documentPdfPath = storage_path("app/{$path}/document.pdf");

        $header = View::make('killerquote::pdf.components.header', compact('settings', 'base_settings', 'fe_settings'))->render();
        $footer = View::make('killerquote::pdf.components.footer', compact('settings', 'base_settings', 'fe_settings'))->render();

        Storage::put("{$path}/header.html", $header);
        Storage::put("{$path}/footer.html", $footer);

        $headerUrl = asset("storage/killerquotes/pdf/{$quote->id}/header.html");
        $footerUrl = asset("storage/killerquotes/pdf/{$quote->id}/footer.html");

        $merger = PDFMerger::init();
// return view('killerquote::pdf.logo', compact('quote', 'settings', 'base_settings', 'fe_settings'));
        $logo = PDF::loadView('killerquote::pdf.logo', compact('quote', 'settings', 'base_settings', 'fe_settings'))
            ->setPaper('a4')
            ->setOption('enable-local-file-access', true)
            ->setOption('encoding', 'UTF-8');
// return $logo->inline();
        $logo->save($logoPdfPath);
// return $logo->inline();

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

        $filename = 'N'.$quote->numero.'--'.$quote->created_at->format('d-m-Y').'.pdf';
        $merger->setFileName($filename);
        $merger->merge();
        return $merger;
    }

    private function generatePdf($quote, $locale)
    {
        $media = [];
        foreach($quote->items as $item)
        {
            $pdf_attachment = $item->product->media()->pdf()->first();
            if($pdf_attachment)
            {
                $media[] = storage_path('app/public/products/docs/'.$pdf_attachment->filename);
            }
        }

        if(KillerQuoteSettingLocale::HasDefaultPdfAttachment($locale))
        {
            $media[] = KillerQuoteSettingLocale::DefaultPdfAttachment($locale);
        }

        $base_settings = Setting::base();
        $fe_settings = Setting::fe();
        $settings = KillerQuoteSettingLocale::assocLocale($locale);

        $path = "public/killerquotes/pdf/{$locale}/{$quote->id}";

        if(Storage::exists($path))
            Storage::deleteDirectory($path);

        Storage::makeDirectory($path);

        $logoPdfPath = storage_path("app/{$path}/logo.pdf");
        $documentPdfPath = storage_path("app/{$path}/document.pdf");

        $header = View::make('killerquote::pdf.components.header', compact('settings', 'base_settings', 'fe_settings'))->render();
        $footer = View::make('killerquote::pdf.components.footer', compact('settings', 'base_settings', 'fe_settings'))->render();

        $merger = PDFMerger::init();

        Storage::put("{$path}/header.html", $header);
        Storage::put("{$path}/footer.html", $footer);

        $headerUrl = asset("storage/killerquotes/pdf/{$locale}/{$quote->id}/header.html");
        $footerUrl = asset("storage/killerquotes/pdf/{$locale}/{$quote->id}/footer.html");

        $logo = PDF::loadView('killerquote::pdf.logo', compact('quote', 'settings', 'base_settings', 'fe_settings'))
            ->setPaper('a4')
            ->setOption('enable-local-file-access', true)
            ->setOption('encoding', 'UTF-8');
// return $logo->inline();
        $logo->save($logoPdfPath);

//return view('killerquote::pdf.quote', compact('quote', 'settings', 'base_settings', 'fe_settings'));
        $document = PDF::loadView('killerquote::pdf.quote', compact('quote', 'settings', 'base_settings', 'fe_settings'))
            ->setPaper('a4')
            ->setOption('enable-local-file-access', true)
            ->setOption('header-spacing', 10)
            ->setOption('header-html', $headerUrl)
            ->setOption('footer-html', $footerUrl)
            ->setOption('encoding', 'UTF-8');
//return $document->inline();
        $document->save($documentPdfPath);

        $merger->addPathToPDF($logoPdfPath, 'all', 'P');
        $merger->addPathToPDF($documentPdfPath, 'all', 'P');

        foreach($media as $attachment)
        {
            $merger->addPathToPDF($attachment, 'all', 'P');
        }


        $filename = str_slug(trans('killerquote::kq.preventivo')).'-N'.$quote->numero.'--'.$quote->created_at->format('d-m-Y').'.pdf';
        $merger->setFileName($filename);
        $merger->merge();
        return $merger;
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
            $company->privato = 1;
        $company->save();

        $contact->company_id = $company->id;
        $contact->save();

        $company->clients()->save($contact->clients()->first());

        return redirect('killerquotes/create?company_id='.$company->id)->with('message', 'Azienda da contatto creata!');
    }


    public function createOrderConf(Request $request, KillerQuote $quote)
    {

        $oc = \Deals\App\Models\OrderConfirmation::create([
            'killer_quote_id' => $quote->id,
            'sconto_text' => $quote->sconto_text,
            'sconto_value' => $quote->sconto_value,
            'company_id' => $quote->company_id,
            'expiration_date' => Carbon::now()->addDays(7)->format('d/m/Y'),
            'numero' => \Deals\App\Models\OrderConfirmation::getLastNumber()+1,
            'user_id' => auth()->user()->id
        ]);

        $co_item=[];
        foreach($quote->items as $item)
        {
            $co_item['product_id'] = $item->product_id;
            $co_item['descrizione'] = $item->descrizione;
            $co_item['qta'] = $item->qta;
            $co_item['importo'] = $item->importo;
            $co_item['sconto'] = $item->sconto;
            $co_item['perc_iva'] = intval($item->perc_iva);
            $co_item['order_id'] = $oc->id;
            \Deals\App\Models\OrderConfirmationItem::create($co_item);
        }


        return redirect('order_confirmations/'.$oc->id.'/edit')->with('message', "Conferma d'ordine creata");
    }


}
