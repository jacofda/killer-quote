<div class="container summary">
    @include('killerquote::pdf.components.intestation')
    <div class="row page-break-avoid">
        <div class="col-xs-12">
            <div class="text-center" style="margin-bottom: 1cm;">
                <h3 class="section-title">PREVENTIVO</h3>
            </div>
            {!! $quote->summary !!}
        </div>
    </div>
</div>
