@extends('killerquote::pdf.layout')

@section('content')
<div style="height: 100%; width:100%;">
    <div style="position:relative; top:45%; -webkit-transform: translateY(-50%); text-align: center;">
        <div style="width: 40%; margin: auto; text-align: center;">
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
    <div style="position:relative; top:60%; -webkit-transform: translateY(-60%); text-align: right;">
    <p><strong>{{trans('killerquote::kq.preventivo')}} N. {{$quote->numero}} @lang('killerquote::kq.del') {{$quote->created_at->format('d/m/Y')}}</strong><br>(@lang('killerquote::kq.scade_il') {{$quote->expirancy_date->format('d/m/Y')}})</p>
</div>
</div>
@endsection
