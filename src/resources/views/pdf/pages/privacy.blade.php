@if($settings['privacy']->value)
    <div class="container avoid-page-break">
        <div class="row" style="margin-top:1cm;">
            <div class="col-xs-12">
                <h3 class="section-title text-center">PRIVACY POLICY</h3>
                {!! $settings['privacy']->value !!}
            </div>
        </div>
    </div>
@endif
