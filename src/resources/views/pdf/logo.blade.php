@if(!is_null($quote->company->lingua))
    @php
        \App::setLocale($quote->company->lingua);
    @endphp
@else
    @php
        \App::setLocale('it');
    @endphp
@endif

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('css/pdf/b3.css')}}">
    <title>{{str_slug(trans('killerquote::kq.preventivo')).'-n-'.$quote->numero.'-'.$quote->created_at->format('d-m-Y').'.pdf'}}</title>
</head>
<body>

<div style="height: 100%; width:100%; position:relative;" >
    <div style="position:absolute; top:400px;width:100%;text-align: center;">
        <div style="width: 350px; margin: auto; text-align: center;">
            @if($settings['logo']->media()->first()->mediable_type == 'KillerQuote\App\Models\KillerQuoteSetting')
                <img style="height: auto; max-width: 100%;" src="{{ $settings['logo']->media()->first()->getDisplayAttribute() }}" />
            @else
                <img style="height: auto; max-width: 100%;" src="{{asset('storage/killerquotesettings/'.$settings['logo']->lang.'/original/'. $settings['logo']->media()->first()->filename)}}" />
            @endif
            <div style="margin-top:1cm;">
                <h3 class="text-color" style="font-weight: bold;">
                    {{ $settings['payoff']->value }}
                </h3>
            </div>
        </div>
    </div>
    <div style="position:absolute; top:1200px; right: 0; text-align: right;">
        <p><strong>{{trans('killerquote::kq.preventivo')}} N. {{$quote->numero}} @lang('killerquote::kq.del') {{$quote->created_at->format('d/m/Y')}}</strong><br>(@lang('killerquote::kq.scade_il') {{$quote->expirancy_date->format('d/m/Y')}})</p>
    </div>
</div>

</body>
</html>
