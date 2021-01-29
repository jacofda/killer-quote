<?php

namespace KillerQuote\App\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Areaseb\Core\Models\Media;
use KillerQuote\App\Models\{KillerQuoteSetting, KillerQuoteSettingLocale};
use \DB;
use \File;

class SettingsLocaleController extends Controller
{

    public function createDefault($locale)
    {
        $pathIta = storage_path('app/public/killerquotesettings/');
        $path = $pathIta.$locale.'/';
        if(!File::exists($path))
        {

            $dirs = [$path, $path.'full', $path.'display', $path.'thumb', $path.'original' ];
            foreach($dirs as $dir)
            {
                File::makeDirectory($dir, 0777, true, true);
            }

        }

        $settings = KillerQuoteSetting::assoc();

        $logo = Media::find($settings['logo']->value);
        File::copy($pathIta.'original/'.$logo->filename,$path.'original/'.$logo->filename);
        $logo = $this->createMedia($logo->filename, $locale);
        $logo_id = $logo->id;


        if($settings['firma_img']->value != '')
        {
            File::copy($pathIta.'original/'.$settings['firma_img']->value,$path.'original/'.$settings['firma_img']->value);
        }


        $default = [
            [
                'key' => 'logo',
                'default' => $logo_id,
                'lang' => $locale
            ],
            [
                'key' => 'payoff',
                'default' => $settings['payoff']->value,
                'lang' => $locale
            ],
            [
                'key' => 'chi_siamo',
                'default' => $settings['chi_siamo']->value,
                'lang' => $locale
            ],
            [
                'key' => 'perche_sceglierci',
                'default' => $settings['perche_sceglierci']->value,
                'lang' => $locale
            ],
            [
                'key' => 'metodi_pagamento',
                'default' => $settings['metodi_pagamento']->value,
                'lang' => $locale
            ],
            [
                'key' => 'garanzie',
                'default' => $settings['garanzie']->value,
                'lang' => $locale
            ],
            [
                'key' => 'recensioni',
                'default' => $settings['recensioni']->value,
                'lang' => $locale
            ],
            [
                'key' => 'glossario',
                'default' => $settings['glossario']->value,
                'lang' => $locale
            ],
            [
                'key' => 'scadenza',
                'default' => $settings['scadenza']->value,
                'lang' => $locale
            ],
            [
                'key' => 'bonus',
                'default' => $settings['bonus']->value,
                'lang' => $locale
            ],
            [
                'key' => 'pdf',
                'default' => "",
                'lang' => $locale
            ],
            [
                'key' => 'privacy',
                'default' => "",
                'lang' => $locale
            ],
            [
                'key' => 'firma_img',
                'default' => $settings['firma_img']->value,
                'lang' => $locale
            ],
            [
                'key' => 'firma_txt',
                'default' => $settings['firma_txt']->value,
                'lang' => $locale
            ],
            [
                'key' => 'cond_vendita',
                'default' => $settings['cond_vendita']->value,
                'lang' => $locale
            ],
            [
                'key' => 'mostra_bonus',
                'default' => $settings['mostra_bonus']->value,
                'lang' => $locale
            ]
        ];

        foreach($default as $key) {
            KillerQuoteSettingLocale::firstOrCreate([
                'key' => $key['key'],
                'value' => @serialize($key['default']),
                'lang' => $locale
            ]);
        }

        return true;

    }
    public function index($locale)
    {
        if(KillerQuoteSetting::assoc()['logo']->value == '')
        {
            return redirect('killerquotes')->with('error', 'Carica prima il logo nella versione Italiana');
        }

        $response = true;
        if(!DB::table('killer_quote_settings_locale')->where('lang', $locale)->exists())
        {
            $response = $this->createDefault($locale);
        }

        if($response)
        {
            $settings = KillerQuoteSettingLocale::assocLocale($locale);
            return view('killerquote::settings.index-locale', compact('settings', 'locale'));
        }
    }

    public function update(Request $request, $locale)
    {

        $v = Validator::make($request->input(), [
            'payoff' => 'nullable|string',
            'chi_siamo' => 'nullable|string',
            'perche_sceglierci' => 'array',
            'perche_sceglierci.*' => 'nullable|string',
            'metodi_pagamento_txt' => 'array',
            'metodi_pagamento_txt.*' => 'nullable|string',
            'metodi_pagamento_num' => 'array',
            'metodi_pagamento_num.*' => 'nullable|numeric',
            'garanzie' => 'nullable|string',
            'review' => 'array',
            'review.review_name.*' => 'nullable|string',
            'review.review_surname.*' => 'nullable|string',
            'review.review_txt.*' => 'string',
            'glossario' => 'nullable|string',
            'scadenza' => 'nullable|numeric',
            'bonus' => 'nullable|string',
            'privacy' => 'nullable|string',
            'firma_txt' => 'nullable|string',
            'cond_vendita' => 'nullable|string',
            'mostra_bonus' => 'nullable'
        ]);

        if($v->fails())
            return redirect(url('killerquotes/settings'))->with('errors', $v->errors());

        $data = $v->validated();

        // Sometimes the 'scadenza' field is not passed. In these cases set it to null
        $data['scadenza'] = isset($data['scadenza']) ? $data['scadenza'] : null;


        $reviews = [];
        foreach($data['review']['review_txt'] as $i => $val) {
            $reviews[] = [
                'review_txt' => $val,
                'review_name' => $data['review']['review_name'][$i],
                'review_surname' => $data['review']['review_surname'][$i]
            ];
        }

        $metodi_pagamento = [];
        foreach($data['metodi_pagamento_txt'] as $i => $val) {
            if(isset($data['metodi_pagamento_num'][$i]))
                $metodi_pagamento[] = [
                    'text' => $val,
                    'number' => $data['metodi_pagamento_num'][$i]
                ];
        }

        $storeData = [
            'payoff' => $data['payoff'],
            'chi_siamo' => $data['chi_siamo'],
            'perche_sceglierci' => $data['perche_sceglierci'],
            'metodi_pagamento' => $metodi_pagamento,
            'garanzie' => $data['garanzie'],
            'recensioni' => $reviews,
            'glossario' => $data['glossario'],
            'scadenza' => $data['scadenza'],
            'bonus' => $data['bonus'],
            'mostra_bonus' => $data['mostra_bonus'],
            'privacy' => $data['privacy'],
            'firma_txt' => $data['firma_txt'],
            'cond_vendita' => $data['cond_vendita']
        ];

        $this->store($storeData, $locale);
        return redirect(url('killerquotes/settings/'.$locale))->with('success', 'Settings aggiornate');
    }

    private function store($data, $locale) {
        foreach($data as $key => $value) {
            KillerQuoteSettingLocale::where('key', $key)->where('lang', $locale)->update([
                'value' => @serialize($value)
            ]);
        }
    }

    public function uploadLogo(Request $request, $locale) {
        $this->deleteCurrentLogo($locale);
        $filename = strtolower($request->file->getClientOriginalName());
        $response = $this->createMedia($filename, $locale);
        $request->file->storeAs('public/killerquotesettings/'.$locale.'/original',$filename );

        $update = KillerQuoteSettingLocale::where('key', KillerQuoteSetting::KEY_LOGO)->where('lang', $locale)
            ->update([
                'value' => @serialize($response->id)
            ]);
        if($update)
        {
            return $response->id;
        }

        Media::deleteMediaFromId($response);
        abort(500, "Update failed");
    }

    private function deleteCurrentLogo($locale)
    {
        $logo = KillerQuoteSettingLocale::where('key', KillerQuoteSetting::KEY_LOGO)->where('lang', $locale)->first();

        if(intval($logo->value) > 0)
        {
            $logo_id = $logo->value;
        }
        else
        {
            $logo_id = $logo->value->id;
        }

        $media = Media::where('id', $logo_id)->first();
        if($media)
        {
            if( file_exists ( storage_path('app/public/killerquotesettings/'.$locale.'/original/'.$media->filename) ) )
            {
                unlink(storage_path('app/public/killerquotesettings/'.$locale.'/original/'.$media->filename));
            }
            $media->delete();
        }
    }

    public function uploadPdf($locale) {
        $this->deleteCurrentPdf($locale);
        if ( request()->hasFile('file') )
        {
            $pdf = request()->file;
            $filename = $pdf->getClientOriginalName();

            $update = KillerQuoteSettingLocale::where('key', KillerQuoteSettingLocale::KEY_PDF)->where('lang', $locale)
                ->update([
                    'value' => @serialize($filename)
                ]);

            $pdf->storeAs('public/killerquotesettings/'.$locale.'/original', $filename );
        }
        return 'done';
    }

    private function deleteCurrentPdf($locale) {
        $files = \Storage::files('public/killerquotesettings/'.$locale.'/original');
        foreach($files as $file)
        {
            if(strpos($file, '.pdf') !== false)
            {
                \Storage::delete($file);

                $update = KillerQuoteSettingLocale::where('key', KillerQuoteSettingLocale::KEY_PDF)->where('lang', $locale)
                    ->update([
                        'value' => @serialize("")
                    ]);
            }
        }
    }

    public function uploadFirma($locale) {

        if(KillerQuoteSettingLocale::HasFirma($locale))
        {
            $this->deleteCurrentFirma($locale);
        }

        if ( request()->hasFile('file') )
        {
            $img = request()->file;
            $filename = $img->getClientOriginalName();

            $update = KillerQuoteSettingLocale::where('key', KillerQuoteSettingLocale::KEY_FIRMA)->where('lang', $locale)
                ->update([
                    'value' => @serialize($filename)
                ]);

            $img->storeAs('public/killerquotesettings/'.$locale.'/original', $filename );
        }
        return 'done';
    }

    private function deleteCurrentFirma($locale) {

        $img = KillerQuoteSettingLocale::where('key', 'firma_img')->where('lang', $locale)->first();
        if($img)
        {
            \Storage::delete(storage_path('app/public/killerquotesettings/'.$locale.'/original/'.$img->value));
        }
        $update = KillerQuoteSettingLocale::where('key', KillerQuoteSettingLocale::KEY_FIRMA)->where('lang', $locale)
            ->update([
                'value' => @serialize("")
            ]);
    }

    private function createMedia($filename, $locale)
    {
        return Media::create([
            'mime' => 'image',
            'type' => 'normal',
            'filename' => $filename,
            'description' => 'logo pk '. $locale,
            'media_order' => 1,
            'mediable_id' => 1,
            'mediable_type' => 'KillerQuote\App\Models\KillerQuoteSettingLocale',
        ]);
    }

}
