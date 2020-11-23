@extends('killerquote::pdf.layout')

@section('content')
<div style="height: 100%; width:100%;">
    <div style="position:relative; top:45%; -webkit-transform: translateY(-50%); text-align: center;">
        <div style="width: 40%; margin: auto; text-align: center;">
            <img style="height: auto; max-width: 100%;" src="{{ $settings['logo']->media()->first()->getDisplayAttribute() }}" />
            <div style="margin-top:1cm;">
                <h3 class="text-color" style="font-weight: bold;">
                    {{ $settings['payoff']->value }}
                </h3>
            </div>
        </div>
    </div>
</div>
@endsection
