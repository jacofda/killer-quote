<?php

namespace KillerQuote\Src\App\Observers;

use KillerQuote\Src\App\Models\KillerQuoteSetting;

class KillerQuoteSettingObserver
{
    public function retrieved(KillerQuoteSetting $setting) {
        $setting->value = @unserialize($setting->value);
    }
}
