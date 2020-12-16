<?php

namespace KillerQuote\App\Models;

use Areaseb\Core\Models\Category;
use Areaseb\Core\Models\Product;

class KillerQuoteItem extends Primitive
{
    protected $table = "killer_quote_items";
    public $timestamps = false;

//an item belongs to an invoice
    public function invoice()
    {
        return $this->belongsTo(KillerQuote::class);
    }

//an item has one product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

//GETTER
    public function getImportoFormattedAttribute()
    {
        return $this->fmt($this->importo);
    }

    public function getImportoDecimalAttribute()
    {
        return $this->decimal($this->importo);
    }

    public function getImportoScontatoAttribute()
    {
        return $this->importo * (1-($this->sconto)/100);
    }

    public function getImportoScontatoConIvaAttribute()
    {
        $scontato = $this->importo * (1-($this->sconto)/100);
        return $scontato * (1+($this->perc_iva/100));
    }

    public function getTotaleRigaAttribute()
    {
        return $this->importo*$this->qta * (1-($this->sconto)/100);
    }

    public function getTotaleRigaFormattedAttribute()
    {
        return $this->fmt($this->totale_riga);
    }

    public function getTotaleRigaDecimalAttribute()
    {
        return $this->decimal($this->totale_riga);
    }

    public function getIvaFormattedAttribute()
    {
        return $this->fmt($this->iva);
    }

    public function getIvaDecimalAttribute()
    {
        return $this->decimal($this->iva);
    }

    public function getIsSpesaAttribute()
    {
        $spese_id = Category::where('nome', 'Spese')->first()->id;
        return $this->product->categories()->where('category_id', $spese_id)->exists();
    }


//SCOPES

    public function scopeDefault($query)
    {
        $query = $query->where('product_id', Product::default());
    }

    public function scopeAnno($query, $value)
    {
        $query = $query->whereYear('data', $value);
    }

}
