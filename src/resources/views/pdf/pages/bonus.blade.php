@if(intval($settings['mostra_bonus']->value))

    @if($settings['bonus']->value != "" || !is_null($settings['bonus']->value))
        <div class="container mb-5 avoid-page-break">
            <div class="row">
                <div class="col-xs-12">
                    <h3 class="section-title">@lang('killerquote::kq.bonus')</h3>
                    {!! $settings['bonus']->value !!}
                </div>
            </div>
        </div>
    @endif

@endif
