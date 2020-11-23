@php
    $strings = $model->value;
    if(!$strings)
        $strings = [
            [
                'text' => '',
                'number' => ''
            ]
        ];
@endphp
<div class="row">
    <div class="col-12" id="metodi-pagamento-div">
        @foreach($strings as $index => $string)
            <div class="row metodi_pagamento_item">
                <div class="col-xs-12 col-md-8 mb-3">
                    <input type="text" class="form-control" placeholder="Testo" value="{{ !empty($string['text']) ? $string['text'] : '' }}" name="metodi_pagamento_txt[]">
                </div>
                <div class="col-xs-12 col-md-4 d-inline-block">
                    <div class="input-group">
                        <input class="form-control input-decimal" placeholder="Sconto" value="{{ !empty($string['number']) ? number_format($string['number'], 2) : '' }}" name="metodi_pagamento_num[]" type="text">
                        <div class="input-group-append">
                            <span class="input-group-text">00.00 €</span>
                            <button class="btn btn-danger float-right" data-action="delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="col-12">
        <button class="btn btn-md btn-block btn-success" id="metodi-pagamento-add">Aggiungi</button>
    </div>
</div>

@push('scripts')
    <script>
        (function(){
            const newElement = `
                <div class="row metodi_pagamento_item">
                    <div class="col-xs-12 col-md-8 mb-3">
                        <input type="text" class="form-control" placeholder="Testo" name="metodi_pagamento_txt[]">
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <div class="input-group">
                            <input class="form-control input-decimal" placeholder="Sconto" name="metodi_pagamento_num[]" type="text">
                            <div class="input-group-append">
                                <span class="input-group-text">00.00 €</span>
                                <button class="btn btn-danger float-right" data-action="delete"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#metodi-pagamento-add').click(addHandler);
            $('.metodi_pagamento_item button[data-action="delete"]').click(deleteHandler);

            function addHandler(e) {
                e.preventDefault();
                $newElement = $(newElement);
                $newElement.find('button[data-action="delete"]').click(deleteHandler);
                $('#metodi-pagamento-div').append($newElement);
            }

            function deleteHandler(e) {
                e.preventDefault();
                if(countStrings() > 1) $(e.target).closest('.metodi_pagamento_item').remove();
            }

            function countStrings() {
                return $('.metodi_pagamento_item').length;
            }
        })(jQuery)
    </script>
@endpush
