<?php

namespace KillerQuote\App\Observers;

use KillerQuote\App\Models\KillerQuoteSettingLocale;

class KillerQuoteSettingLocaleObserver
{
    public function retrieved(KillerQuoteSettingLocale $setting) {
        $setting->value = @unserialize($setting->value);
    }
}
