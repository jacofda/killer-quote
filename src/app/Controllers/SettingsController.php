<?php

namespace KillerQuote\Src\App\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Jacofda\Core\Models\Media;
use KillerQuote\Src\App\Models\KillerQuoteSetting;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
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
            'recensione_txt' => 'array',
            'recensione_txt.*' => 'nullable|string',
            'glossario' => 'nullable|string',
            'scadenza' => 'nullable|numeric'
        ]);

        if($v->fails())
            return redirect(url('killerquotes/settings'))->with('errors', $v->errors());

        $data = $v->validated();

        // Sometimes the 'scadenza' field is not passed. In these cases set it to null
        $data['scadenza'] = isset($data['scadenza']) ? $data['scadenza'] : null;

        $recensioni = KillerQuoteSetting::where('key', KillerQuoteSetting::KEY_RECENSIONI)->first()->value;
        if(!is_array($recensioni))
            $recensioni = [];

        foreach($recensioni as $i => $val) {
            if(!empty($data['recensione_txt'][$i]))
                $recensioni[$i]['review_txt'] = $data['recensione_txt'][$i];
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
            'recensioni' => $recensioni,
            'glossario' => $data['glossario'],
            'scadenza' => $data['scadenza']
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

    public function uploadReviewImg() {
        $reviewsModel = KillerQuoteSetting::where('key', KillerQuoteSetting::KEY_RECENSIONI)->first();
        $reviews = $reviewsModel->value;
        $mediaId = Media::saveImageOrFile(request());
        $reviews[] = [
            'review_txt' => '',
            'review_img' => $mediaId
        ];
        $reviewsModel->value = serialize($reviews);
        $update = $reviewsModel->save();
        if($update)
            return $mediaId;
        Media::deleteMediaFromId($mediaId);
        abort(500, "Update failed");
    }

    public function deleteReviewImg($id) {
        $reviewsModel = KillerQuoteSetting::where('key', KillerQuoteSetting::KEY_RECENSIONI)->first();
        $reviews = $reviewsModel->value;
        $reviews = array_filter($reviews, function($val) use ($id) {
            return $val['review_img'] !== intval($id);
        });

        $reviewsModel->value =  @serialize($reviews);
        if(!$reviewsModel->save())
            return abort(500, "Update failed");
        Media::deleteMediaFromId($id);
        return "The media has been deleted";
    }

    private function deleteCurrentLogo() {
        $logo = KillerQuoteSetting::where('key', KillerQuoteSetting::KEY_LOGO)->first();
        if($logo && $logo->value && intval($logo->value) > 0 && Media::where('id', $logo->value)->exists())
            Media::deleteMediaFromId(intval($logo->value));
    }

}
