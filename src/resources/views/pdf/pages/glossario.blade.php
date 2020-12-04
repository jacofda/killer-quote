@if($settings['glossario']->value)
    <div class="container avoid-page-break">
        <div class="row" style="margin-top:1cm;">
            <div class="col-xs-12">
                <h3 class="section-title text-center">GLOSSARIO</h3>
                {!! $settings['glossario']->value !!}
            </div>
        </div>
    </div>
@endif
