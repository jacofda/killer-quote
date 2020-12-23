<?php

use Illuminate\Database\Seeder;
use KillerQuote\App\Models\KillerQuoteSetting;
class KillerQuoteSettingsSeeder extends Seeder
{
    const SETTING_KEYS = [
        [
            'key' => 'logo',
            'default' => ""
        ],
        [
            'key' => 'payoff',
            'default' => ""
        ],
        [
            'key' => 'chi_siamo',
            'default' => ""
        ],
        [
            'key' => 'perche_sceglierci',
            'default' => []
        ],
        [
            'key' => 'metodi_pagamento',
            'default' => []
        ],
        [
            'key' => 'garanzie',
            'default' => ""
        ],
        [
            'key' => 'recensioni',
            'default' => []
        ],
        [
            'key' => 'glossario',
            'default' => ""
        ],
        [
            'key' => 'scadenza',
            'default' => ""
        ],
        [
            'key' => 'bonus',
            'default' => ""
        ],
        [
            'key' => 'pdf',
            'default' => ""
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(self::SETTING_KEYS as $key) {
            KillerQuoteSetting::firstOrCreate(
                ['key' => $key['key']], ['value' => @serialize($key['default'])]
            );
        }
    }
}
