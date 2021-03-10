<?php

namespace KillerQuote\App\Models;

use Carbon\Carbon;
use Areaseb\Core\Models\Calendar;
use Areaseb\Core\Models\Company;
use Areaseb\Core\Models\Media;
use KillerQuote\App\Models\KillerQuote;

class KillerQuoteNote extends Primitive
{
    protected $table = "killer_quote_notes";

    //an item belongs to an invoice
    public function invoice()
    {
        return $this->belongsTo(KillerQuote::class);
    }

}
