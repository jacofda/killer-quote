<?php

namespace KillerQuote\App\Models;

use Carbon\Carbon;
use App\User;
use Areaseb\Core\Models\Calendar;
use Areaseb\Core\Models\Company;
use Areaseb\Core\Models\Media;
use Deals\App\Models\Deal;
use Deals\App\Models\DealEvent;
use KillerQuote\App\Models\KillerQuoteItem;

class KillerQuote extends Primitive
{
    protected $table = "killer_quotes";
    protected $guarded = [];

    public function orderConfirmation() {
        if(class_exists('Deals\App\Models\OrderConfirmation'))
            return $this->hasOne('Deals\App\Models\OrderConfirmation', 'killer_quote_id', 'id');
        return null;
    }

    public function dealEvent() {
        if(class_exists('Deals\App\Models\DealEvent'))
            return $this->morphOne(DealEvent::class, 'dealable');
        return null;
    }

    public function getDates() {
        return ['created_at', 'updated_at', 'expirancy_date'];
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function items() {
        return $this->hasMany(KillerQuoteItem::class, "invoice_id");
    }

    public static function getLastNumber()
    {
        $max_killer = self::whereYear('created_at', date('Y'))->max('numero');
        $max_generic = 0;
        if(class_exists('Deals\App\Models\DealGenericQuote'))
        {
            $max_generic = \Deals\App\Models\DealGenericQuote::whereYear('created_at', date('Y'))->max('numero');
        }
        return max($max_killer, $max_generic) + 1;
    }

    public static function Calendar()
    {
        return Calendar::firstOrCreate(['nome' => 'preventivi', 'user_id' => User::first()->id])->id;
    }

    public function getCommissioneAttribute()
    {
        $sum = 0;
        $company = $this->company;;
        if($company->testimonial()->exists() || $company->agent()->exists())
        {
            if($company->testimonial()->exists())
            {
                $testimonial = $company->testimonial()->first();


                foreach($this->items as $item)
                {
                    if($item->product->perc_agente > 0)
                    {
                        $perc = ($item->product->perc_agente + ($item->product->perc_agente*($testimonial->commission/100)))/100;
                    }
                    else
                    {
                        $perc = $testimonial->commission/100;
                    }
                    if($company->privato)
                    {
                        $sum += ($item->importo_scontato_con_iva*$item->qta)*$perc;
                    }
                    else
                    {
                        $sum += ($item->importo_scontato*$item->qta)*$perc;
                    }
                }

            }
            else
            {
                $testimonial = $company->agent()->first();

                foreach($this->items as $item)
                {
                    $perc = $testimonial->commission/100;
                    if($company->privato)
                    {
                        $sum += ($item->importo_scontato_con_iva*$item->qta)*$perc;
                    }
                    else
                    {
                        $sum += ($item->importo_scontato*$item->qta)*$perc;
                    }
                }

            }
        }
        return $sum;

    }


    public function getCalculateImportoAttribute()
    {
        $sum = 0;
        if($this->items()->exists())
        {
            if($this->company->privato)
            {
                foreach($this->items as $item)
                {
                    $sum += $item->importo_scontato_con_iva*$item->qta;
                }
                return $sum;
            }
            else
            {
                foreach($this->items as $item)
                {
                    $sum += $item->importo_scontato*$item->qta;
                }
                return $sum;
            }
        }
        return $sum;
    }

    public function getCleanImportoAttribute()
    {
        return $this->attributes['importo'];
    }

    public function getImportoAttribute()
    {
        $sum = $this->attributes['importo'];
        if($this->company->privato)
        {
            return '€ ' . number_format($sum, '2', ',', '.');
        }
        else
        {
            if($this->company->nazione == 'IT')
            {
                return '€ ' . number_format($sum, '2', ',', '.') . ' + IVA ' . config('app.iva').'%';
            }
            else
            {
                return '€ ' . number_format($sum, '2', ',', '.');
            }
        }
        return $sum;
    }


    public static function filter($data)
    {
        $query = self::with('company');


        if(auth()->user()->hasRole('testimonial'))
        {
            $ids = \DB::table('testimonial_company')->where('testimonial_id', auth()->user()->testimonial->id)->pluck('company_id')->toArray();
            $query = $query->whereIn('company_id', $ids);
        }

        if(auth()->user()->hasRole('agent'))
        {
            $ids = \DB::table('agent_company')->where('agent_id', auth()->user()->agent->id)->pluck('company_id')->toArray();
            $query = $query->whereIn('company_id', $ids);
        }


        if($data->has('anno'))
        {
            if(!is_null($data->anno))
            {
                $query = $query->whereYear('created_at', $data->anno);
            }
        }

        if($data->has('mese'))
        {
            if(!is_null($data->mese))
            {
                $query = $query->whereMonth('created_at', $data->mese);
            }
        }


        if($data->has('company'))
        {
            if(!is_null($data->company))
            {
                $query = $query->where('company_id', $data->company);
            }
        }

        if($data->get('range'))
        {
            $range = explode(' - ', $data->range);
            $da = Carbon::createFromFormat('d/m/Y', $range[0])->format('Y-m-d');
            $a =  Carbon::createFromFormat('d/m/Y', $range[1])->format('Y-m-d');

            $query = $query->whereBetween( 'created_at', [$da, $a] );
        }

        if($data->has('expired'))
        {
            $expired = $data->get('expired');
            if($expired == 1)
            {
                $query = $query->where('expirancy_date', '<', Carbon::now()->format('Y-m-d'));
            }
            elseif($expired == 0)
            {
                $query = $query->where('expirancy_date', '>=', Carbon::now()->format('Y-m-d'));
            }

        }

        if($data->get('sort'))
        {
            $arr = explode('|', $data->sort);
            $field = $arr[0];
            $value = $arr[1];
            $query = $query->orderBy($field, $value);
        }

        return $query;
    }

}
