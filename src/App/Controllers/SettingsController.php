<?php

namespace KillerQuote\App\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Areaseb\Core\Models\Media;
use KillerQuote\App\Models\KillerQuoteSetting;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        //dd(KillerQuoteSetting::assoc()['pdf']->value);
        return view('killerquote::settings.index', [
            'settings' => KillerQuoteSetting::assoc()
        ]);
    }

    public function update(Request $request)
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

        $this->store($storeData);
        return redirect(url('killerquotes/settings'))->with('success', 'Settings aggiornate');
    }

    private function store($data) {
        foreach($data as $key => $value) {
            KillerQuoteSetting::where('key', $key)->update([
                'value' => @serialize($value)
            ]);
        }
    }

    public function uploadLogo() {
        $this->deleteCurrentLogo();
        $response = Media::saveImageOrFile(request());
        $update = KillerQuoteSetting::where('key', KillerQuoteSetting::KEY_LOGO)
            ->update([
                'value' => @serialize($response)
            ]);
        if($update)
            return $response;
        Media::deleteMediaFromId($response);
        abort(500, "Update failed");
    }

    private function deleteCurrentLogo() {
        $logo = KillerQuoteSetting::where('key', KillerQuoteSetting::KEY_LOGO)->first();
        if($logo && $logo->value && intval($logo->value) > 0 && Media::where('id', $logo->value)->exists())
            Media::deleteMediaFromId(intval($logo->value));
    }

    public function uploadPdf() {
        $this->deleteCurrentPdf();
        if ( request()->hasFile('file') )
        {
            $pdf = request()->file;
            $filename = $pdf->getClientOriginalName();

            $update = KillerQuoteSetting::where('key', KillerQuoteSetting::KEY_PDF)
                ->update([
                    'value' => @serialize($filename)
                ]);

            $pdf->storeAs('public/killerquotesettings/original', $filename );
        }
        return 'done';
    }

    private function deleteCurrentPdf() {
        $files = \Storage::files('public/killerquotesettings/original');
        foreach($files as $file)
        {
            if(strpos($file, '.pdf') !== false)
            {
                \Storage::delete($file);

                $update = KillerQuoteSetting::where('key', KillerQuoteSetting::KEY_PDF)
                    ->update([
                        'value' => @serialize("")
                    ]);
            }
        }
    }

    public function uploadFirma() {

        if(KillerQuoteSetting::HasFirma())
        {
            $this->deleteCurrentFirma();
        }

        if ( request()->hasFile('file') )
        {
            $img = request()->file;
            $filename = $img->getClientOriginalName();

            $update = KillerQuoteSetting::where('key', KillerQuoteSetting::KEY_FIRMA)
                ->update([
                    'value' => @serialize($filename)
                ]);

            $img->storeAs('public/killerquotesettings/original', $filename );
        }
        return 'done';
    }

    private function deleteCurrentFirma() {
        \Storage::delete(KillerQuoteSetting::FirmaPath());
        $update = KillerQuoteSetting::where('key', KillerQuoteSetting::KEY_FIRMA)
            ->update([
                'value' => @serialize("")
            ]);
    }

}
