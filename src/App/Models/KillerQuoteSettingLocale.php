<?php

namespace KillerQuote\App\Models;

use Areaseb\Core\Models\Media;

class KillerQuoteSettingLocale extends Primitive
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
    const KEY_FIRMA = "firma_img";
    const KEY_FIRMATXT = "firma_txt";
    const KEY_PRIVACY = "privacy";
    const KEY_COND = "cond_vendita";
    const KEY_MOSTRA_BONUS = "mostra_bonus";


    const CREATED_AT = null;
    protected $table = "killer_quote_settings_locale";


    // Returns all locale settings in an associative array
    public static function assocLocale($locale) {
        $settings = self::where('lang', $locale)->get();
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
        elseif($this->key === self::KEY_FIRMA)
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

    public static function HasDefaultPdfAttachment($locale)
    {
        $pdf = self::where('key', 'pdf')->where('lang', $locale)->first();
        if(!is_null($pdf))
        {
            if($pdf->value != "")
            {
                return true;
            }
        }
        return false;
    }

    public static function DefaultPdfAttachment($locale)
    {
        $pdf = self::where('key', 'pdf')->where('lang', $locale)->first();
        return storage_path('app/public/killerquotesettings/original/'.$pdf->value);
    }

    public static function HasFirma($locale)
    {
        $firma = self::where('key', 'firma_img')->where('lang', $locale)->first();
        if(!is_null($firma))
        {
            if($firma->value != "")
            {
                return true;
            }
        }
        return false;
    }

    public static function FirmaPath($locale)
    {
        $img = self::where('key', 'firma_img')->where('lang', $locale)->first();
        return asset('storage/killerquotesettings/'.$locale.'/original/'.$img->value);
    }


}
