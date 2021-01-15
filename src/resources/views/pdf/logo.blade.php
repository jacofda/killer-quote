@extends('killerquote::pdf.layout')

@section('content')
<div style="height: 100%; width:100%;">
    <div style="position:relative; top:45%; -webkit-transform: translateY(-50%); text-align: center;">
        <div style="width: 40%; margin: auto; text-align: center;">
            <img style="height: auto; max-width: 100%;" src="{{ $settings['logo']->media()->first()->original }}" />
            <div style="margin-top:1cm;">
                <h3 class="text-color" style="font-weight: bold;">
                    {{ $settings['payoff']->value }}
                </h3>
            </div>
        </div>
    </div>
    <div style="position:relative; top:80%; -webkit-transform: translateY(-50%); text-align: right;">
        <p><strong>Preventivo N. {{$quote->numero}} del {{$quote->created_at->format('d/m/Y')}}</strong><br>(Scade il {{$quote->expirancy_date->format('d/m/Y')}})</p>
    </div>
</div>
@endsection
