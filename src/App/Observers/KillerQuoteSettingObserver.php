<?php

namespace KillerQuote\App\Observers;

use KillerQuote\App\Models\KillerQuoteSetting;

class KillerQuoteSettingObserver
{
    public function retrieved(KillerQuoteSetting $setting) {
        $setting->value = @unserialize($setting->value);
    }
}
