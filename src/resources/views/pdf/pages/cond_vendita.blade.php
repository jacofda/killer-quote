@if($settings['cond_vendita']->value != "" || !is_null($settings['cond_vendita']->value))
    <div class="container mb-5 avoid-page-break">
        <div class="row">
            <div class="col-xs-12">
                <h3 class="section-title">@lang('killerquote::kq.cond_vendita')</h3>
                {!! $settings['cond_vendita']->value !!}
            </div>
        </div>
    </div>
@endif
