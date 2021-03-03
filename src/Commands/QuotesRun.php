<?php

namespace KillerQuote\Commands;

use Illuminate\Console\Command;
use Areaseb\Core\Models\{Company, Event, Cron, Setting};
use KillerQuote\Mail\SendExpirationNotification;
use Illuminate\Support\Facades\Schema;
use KillerQuote\App\Models\KillerQuote;
use \Carbon\Carbon;

class QuotesRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotes:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old quotes and change color if expired, and send notification email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $events = Event::where('eventable_type', 'like', '%Quote')->get();
        foreach ($events as $event)
        {
            $quote = null;
            if(strpos($event->eventable_type, 'Deal') !== false)
            {
                $quote = \Deals\App\Models\DealGenericQuote::find($event->eventable_id);
            }
            elseif( strpos($event->eventable_type, 'Killer') !== false )
            {
                $quote = \KillerQuote\App\Models\KillerQuote::find($event->eventable_id);
            }

            if($quote)
            {

                if(!is_null($quote->accepted))
                {
                    $event->delete();
                }
            }
        }

        $events = Event::where('eventable_type', 'like', '%Quote')->get();
        foreach ($events as $event)
        {
            $quote = null;
            if(strpos($event->eventable_type, 'Deal') !== false)
            {
                $quote = \Deals\App\Models\DealGenericQuote::find($event->eventable_id);
            }
            elseif( strpos($event->eventable_type, 'Killer') !== false )
            {
                $quote = KillerQuote::find($event->eventable_id);
            }

            if($quote)
            {
                if($quote->expirancy_date->lt(Carbon::now()))
                {
                    if($event->backgroundColor == "#3788d8")
                    {
                        $event->update([
                            "backgroundColor" => "#ecb204"
                        ]);
                    }
                }
            }

        }



        $quote = null;
        $today = Carbon::today();
        $collection = collect();
        if(Schema::hasTable('killer_quotes'))
        {
            $killer = KillerQuote::whereDate('expirancy_date', $today->format('Y-m-d'))->get();
            $collection = $collection->concat($killer);
        }


        if(Schema::hasTable('deals'))
        {
            $dg = \Deals\App\Models\Deal::all();
            $generic = collect();
            foreach($dg as $d)
            {
                $gq = $d->events()->where('type',1)->pluck('dealable_id')->toArray();
                if(count($gq))
                {
                    $queryDGQ = \Deals\App\Models\DealGenericQuote::whereIn('id',$gq)->whereDate('expirancy_date', $today->format('Y-m-d'))->get();

                    foreach($queryDGQ as $q)
                    {
                        $col = collect();
                        $col->numero = $q->numero;
                        $col->expirancy_date = $q->expirancy_date;
                        $col->company = $d->company->rag_soc;
                        $col->company_id = $d->company->id;
                        $col->importo = $q->importo;
                        $col->deal = $d->id;

                        $generic->push($col);
                    }

                }

            }
            $collection = $collection->concat($generic);
        }

        $quotes = $collection;

        $preventivi = [];
        foreach($quotes as $quote)
        {
            $preventivi[] = 'Preventivo n. '.$quote->numero . ' fatto a ' . Company::find($quote->company_id)->rag_soc . ' scade ' . $quote->expirancy_date->format('d/m/Y');
        }


        $mailer = app()->makeWith('custom.mailer', Setting::smtp(0));
        $mailer->send(new SendExpirationNotification($preventivi));


        $this->info('done');
        Cron::create(['name' => 'QuotesRun']);
    }

}
