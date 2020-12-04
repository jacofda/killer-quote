@push("style")
    <style>
        .perche-sceglierci .bordered-box .bordered-box-text {
            font-weight: bold;
            font-size: 1.3em;
        }

        .perche-sceglierci .bordered-box {
            height: 4cm;
        }
    </style>
@endpush

@if(count($settings['perche_sceglierci']->value) > 0)
<div class="container avoid-page-break perche-sceglierci">
    <div class="row">
        <div class="col-xs-12">
            <h3 class="section-title">Perché sceglierci?</h3>
            <div class="row">
                @foreach($settings['perche_sceglierci']->value as $text)
                    <div class="col-xs-4">
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
