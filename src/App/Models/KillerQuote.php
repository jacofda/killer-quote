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

    public static function getLastNumber() {
        $now = Carbon::now();
        $last = self::whereYear('created_at', $now->format('Y'))->orderBy('numero', 'DESC')->first();
        if(!$last)
            return 0;
        return $last->numero;
    }

    public static function Calendar()
    {
        return Calendar::firstOrCreate(['nome' => 'preventivi', 'user_id' => User::first()->id])->id;
    }


    public function getImportoAttribute()
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
                return '€ ' . number_format($sum, '2', ',', '.');
            }
            else
            {
                foreach($this->items as $item)
                {
                    $sum += $item->importo_scontato*$item->qta;
                }
                return '€ ' . number_format($sum, '2', ',', '.') . ' + IVA ' . config('app.iva').'%';
            }
        }
        return $sum;
    }


    public static function filter($data)
    {
        $query = self::with('company');

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
            if($expired)
                $query = $query->where('expirancy_date', '<', Carbon::now()->format('Y-m-d'));
            elseif($expired !== '')
                $query = $query->where('expirancy_date', '>=', Carbon::now()->format('Y-m-d'));
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
