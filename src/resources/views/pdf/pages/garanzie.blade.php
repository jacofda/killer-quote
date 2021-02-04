@if($settings['garanzie']->value)
    <div class="container avoid-page-break">
        <div class="row" style="margin-top:2cm;">
            <div class="col-xs-12">
                <h3 class="section-title text-center">@lang('killerquote::kq.garanzie')</h3>

                {!! $settings['garanzie']->value !!}
            </div>
        </div>
    </div>
@endif
