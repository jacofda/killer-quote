@if($settings['garanzie']->value)
    <div class="container avoid-page-break">
        <div class="row">
            <div class="col-xs-12">
                {!! $settings['garanzie']->value !!}
            </div>
        </div>
    </div>
@endif
