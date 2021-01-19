@push("style")
    <style>
        .perche-sceglierci .bordered-box .bordered-box-text {
            font-weight: bold;
            font-size: 1.05em;
        }

        .perche-sceglierci .bordered-box {
            height: 3.1cm;
        }
    </style>
@endpush

@if(count($settings['perche_sceglierci']->value) > 0)
<div class="container avoid-page-break perche-sceglierci mb-0">
    <div class="row">
        <div class="col-xs-12">
            <h3 class="section-title mb-5">Che cosa apprezzano di più i nostri clienti</h3><br>
            <div class="row">
                @foreach($settings['perche_sceglierci']->value as $text)
                    <div class="col-xs-4 mb-5">
                        <div class="bordered-box">
                            <div class="bordered-box-text">{{ $text }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
