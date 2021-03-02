@push('style')
<style>
.payments .bordered-box {
    height: 4cm;
}
</style>
@endpush

<div class="container avoid-page-break">

    <div class="row payments">
        <div class="col-xs-12">
            <h3 class="section-title text-center">@lang('killerquote::kq.pagamento')</h3>
            <div class="row">
                @if($quote->sconto_text || $quote->sconto_value)
                    <div class="col-xs-12">
                        <div class="bordered-box">
                            <div class="bordered-box-text">
                                {!! $quote->sconto_text  !!}
                                @if($quote->sconto_value)
                                     {{-- <h4 style="margin: 3mm 0 0 0; font-weight: bold;">{{ $quote->sconto_value }}% @lang('killerquote::kq.sconto')</h4> --}}
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    @foreach($settings['metodi_pagamento']->value as $sconto)
                        <div class="col-xs-4">
                            <div class="bordered-box">
                                <div class="bordered-box-text">
                                    {!!  $sconto['text'] !!}
                                    @if($sconto['number'] != "0.00")
                                        <h4 style="margin: 3mm 0 0 0; font-weight: bold;">{{ $sconto['number']+0 }}% @lang('killerquote::kq.sconto')</h4>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
