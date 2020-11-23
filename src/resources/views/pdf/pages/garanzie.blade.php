@if($settings['garanzie']->value)
    <div class="container avoid-page-break">
        <div class="row" style="margin-top:1cm;">
            <div class="col-xs-12">
                {!! $settings['garanzie']->value !!}
            </div>
        </div>
    </div>
@endif
