<?php

namespace KillerQuote;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use KillerQuote\App\Models\{KillerQuoteSetting, KillerQuoteSettingLocale};
use KillerQuote\App\Observers\{KillerQuoteSettingObserver, KillerQuoteSettingLocaleObserver};
use GrofGraf\LaravelPDFMerger\Providers\PDFMergerServiceProvider;

class KillerQuoteServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->register( PDFMergerServiceProvider::class);
        $this->loadViewsFrom(__DIR__.'/resources/views', 'killerquote');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'killerquote');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->publishes([
            __DIR__.'/database/seeds/KillerQuoteSettingsSeeder.php' => database_path('seeds/KillerQuoteSettingsSeeder.php'),
            __DIR__.'/database/seeds/KillerQuotePermissionsSeeder.php' => database_path('seeds/KillerQuotePermissionsSeeder.php')
        ], 'killerquote.seeds');

        $this->publishes([
            __DIR__.'/public/css/dropzone5-7-0.min.css' => public_path('css/dropzone5-7-0.min.css'),
            __DIR__.'/public/js/dropzone5-7-0.min.js' => public_path('js/dropzone5-7-0.min.js'),
            __DIR__.'/public/js/summernote-images-upload.js' => public_path('js/summernote-images-upload.js')
        ], 'killerquote.public');

        KillerQuoteSetting::observe(KillerQuoteSettingObserver::class);
        KillerQuoteSettingLocale::observe(KillerQuoteSettingLocaleObserver::class);
        $this->createFolders();
    }

    private function createFolders() {
        $paths = [
            'public/killerquotesettings',
            'public/killerquotesettings/original',
            'public/killerquotesettings/full',
            'public/killerquotesettings/display',
            'public/killerquotesettings/thumb',
            'public/killerquotes/original',
            'public/killerquotes/full',
            'public/killerquotes/display',
            'public/killerquotes/thumb',
            'public/killerquotesettings/en',
            'public/killerquotesettings/en/original',
            'public/killerquotesettings/en/full',
            'public/killerquotesettings/en/display',
            'public/killerquotesettings/en/thumb',
            'public/killerquotesettings/de',
            'public/killerquotesettings/de/original',
            'public/killerquotesettings/de/full',
            'public/killerquotesettings/de/display',
            'public/killerquotesettings/de/thumb',
            'public/killerquotesettings/es',
            'public/killerquotesettings/es/original',
            'public/killerquotesettings/es/full',
            'public/killerquotesettings/es/display',
            'public/killerquotesettings/es/thumb',
            'public/killerquotesettings/fr',
            'public/killerquotesettings/fr/original',
            'public/killerquotesettings/fr/full',
            'public/killerquotesettings/fr/display',
            'public/killerquotesettings/fr/thumb',
            'public/killerquotesettings/es',
            'public/killerquotesettings/es/original',
            'public/killerquotesettings/es/full',
            'public/killerquotesettings/es/display',
            'public/killerquotesettings/es/thumb',
            'public/killerquotesettings/pt',
            'public/killerquotesettings/pt/original',
            'public/killerquotesettings/pt/full',
            'public/killerquotesettings/pt/display',
            'public/killerquotesettings/pt/thumb',
        ];

        foreach($paths as $path) {
            if(!Storage::exists('app/'.storage_path($path)))
                Storage::makeDirectory($path);
        }
    }
}
