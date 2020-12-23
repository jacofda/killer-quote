<?php

namespace KillerQuote\App\Models;

use Areaseb\Core\Models\Media;

class KillerQuoteSetting extends Primitive
{
    // Keys
    const KEY_LOGO = "logo";
    const KEY_PAYOFF = "payoff";
    const KEY_CHI_SIAMO = "chi_siamo";
    const KEY_PERCHE_SCEGLIERCI = "perche_sceglierci";
    const KEY_METODI_PAGAMENTO = "metodi_pagamento";
    const KEY_GARANZIE = "garanzie";
    const KEY_RECENSIONI = "recensioni";
    const KEY_GLOSSARIO = "glossario";
    const KEY_SCADENZA = "scadenza";
    const KEY_BONUS = "bonus";
    const KEY_PDF = "pdf";

    const CREATED_AT = null;
    protected $table = "killer_quote_settings";


    // Returns all the settings in an associative array
    public static function assoc() {
        $settings = self::all();
        $mapped = [];
        foreach($settings as $setting) {
            $mapped[$setting->key] = $setting;
        }
        return $mapped;
    }

    // Returns the value field unserialized
    public function getValueField() {
        return @unserialize($this->value);
    }

    public static function getMediaById($id) {
        return Media::where('id', $id)->first();
    }

    public function media() {
        if($this->key === self::KEY_LOGO)
        {
            return Media::where('id', intval($this->value));
        }
        elseif($this->key === self::KEY_PDF)
        {
            return Media::where('id', intval($this->value));
        }

        return null;
    }

    public static function DefaultExpDays()
    {
        $ex = self::where('key', 'scadenza')->first();
        if(!is_null($ex))
        {
            return $ex->value;
        }
        return 30;
    }

    public static function HasDefaultPdfAttachment()
    {
        $pdf = self::where('key', 'pdf')->first();
        if(!is_null($pdf))
        {
            return true;
        }
        return false;
    }

    public static function DefaultPdfAttachment()
    {
        $pdf = self::where('key', 'pdf')->first();
        return storage_path('app/public/killerquotesettings/original/'.$pdf->value);
    }

}
