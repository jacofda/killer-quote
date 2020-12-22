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
            <div class="row same-height metodi_pagamento_item">
                <div class="col-xs-12 col-md-8">
                    <textarea name="metodi_pagamento_txt[]">
                        {!! !empty($string['text']) ? $string['text'] : '' !!}
                    </textarea>
                </div>
                <div class="col-xs-12 col-md-4 col-vertical-center">
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
    <script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
    <script>
        (function(){
            const smOptions = {
                disableResizeEditor: true,
                toolbar: [
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            };
            const savedItems = $('.metodi_pagamento_item');
            var lastId = savedItems.last().data('id');
            savedItems.each(function(i, e) {
                bindSummernote($(e).find('textarea[name="metodi_pagamento_txt[]"]'));
            });
            const newElement = `
                <div class="row same-height metodi_pagamento_item">
                    <div class="col-xs-12 col-md-8">
                        <textarea class="metodi_pagamento_textarea" name="metodi_pagamento_txt[]"></textarea>
                    </div>
                    <div class="col-xs-12 col-md-4 col-vertical-center">
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

            function bindSummernote(element) {
                $(element).summernote(smOptions);
            }

            function addHandler(e) {
                e.preventDefault();
                $newElement = $(newElement);
                bindSummernote($newElement.find('textarea.metodi_pagamento_textarea'));
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
