@if($settings['firma_txt']->value)
    <div class="container avoid-page-break">
        <div class="row" style="margin-top:2cm;">
            <div class="col-xs-12">
                {!! $settings['firma_txt']->value !!}
                <br><br><br><br>
            </div>
            <div class="col-xs-6">
                <p class="text-center">{{$base_settings->rag_soc}}</p>
                @if(\KillerQuote\App\Models\KillerQuoteSetting::HasFirma())
                    <img src="{{\KillerQuote\App\Models\KillerQuoteSetting::FirmaPath()}}" style="display:block; margin-left:auto; margin-right:auto; text-align:center; width:auto; height:70px;  ">
                @endif
            </div>
            <div class="col-xs-6">
                <p class="text-center">Il cliente, per accettazione</p>
                <br><br><br>
                <p class="text-center">____________________________________</p>
            </div>
        </div>
    </div>
@endif
