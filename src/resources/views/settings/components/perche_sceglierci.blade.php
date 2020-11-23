@php
    $strings = $model->value;
    if(!$strings)
        $strings = [""];
@endphp
<div class="row">
    <div class="col-12" id="perche-sceglierci-div">
        @foreach($strings as $index => $string)
            <div class="perche_sceglierci_string mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" name="perche_sceglierci[]" value="{{ $string }}" />
                    <div class="input-group-append">
                        <button class="btn btn-danger" data-action="delete"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="col-12">
        <button class="btn btn-md btn-block btn-success" id="perche-sceglierci-add">Aggiungi</button>
    </div>
</div>

@push('scripts')
    <script>
        (function(){
            const newElement = `
                <div class="perche_sceglierci_string mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" name="perche_sceglierci[]" />
                        <div class="input-group-append">
                            <button class="btn btn-danger" data-action="delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            `;
            $('#perche-sceglierci-add').click(addHandler);
            $('.perche_sceglierci_string button[data-action="delete"]').click(deleteHandler);

            function addHandler(e) {
                e.preventDefault();
                $newElement = $(newElement);
                $newElement.find('button[data-action="delete"]').click(deleteHandler);
                $('#perche-sceglierci-div').append($newElement);
            }

            function deleteHandler(e) {
                e.preventDefault();
                if(countStrings() > 1) $(e.target).closest('.perche_sceglierci_string').remove();
            }

            function countStrings() {
                return $('.perche_sceglierci_string').length;
            }
        })(jQuery)
    </script>
@endpush
