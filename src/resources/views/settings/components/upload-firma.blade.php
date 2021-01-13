<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Carica File (solo immagini)</h3>
            </div>
            <div class="card-body">
                <div class="input-group">

                    @if($firmaFile->value)
                        <div class="custom-file">
                            <input type="file" accept=".png,.jpg" class="custom-file-input" id="uploadFirma" lang="it">
                            <label class="custom-file-label" for="uploadFirma" data-browse="Cambia">{{$firmaFile->value}}</label>
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-default" id="upload_firma">Carica</button>
                            <button class="btn btn-danger" id="delete_firma">Elimina</button>
                        </div>
                    @else
                        <div class="custom-file">
                            <input type="file" accept=".png,.jpg" class="custom-file-input" id="uploadFirma" lang="it">
                            <label class="custom-file-label" for="uploadFirma" data-browse="Cerca">Seleziona jpg o png</label>
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-default" id="upload_firma" disabled>Carica</button>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')

    <script>

    //bsCustomFileInput.init();

    $('input#uploadFirma').on('change', function(){
        $('button#upload_firma').prop('disabled', false);
        $('button#upload_firma').removeClass('btn-default');
        $('button#upload_firma').addClass('btn-success');
    });

    $('button#delete_firma').on('click', function(e){
        e.preventDefault();

        let data = {};
        data._token = token;

        $.post("{{url('killerquotes/settings/upload_firma')}}", data, function(r){
            console.log(r);
            $('button#delete_firma').remove();
            $('label.custom-file-label').text("Seleziona jpg o png");
        });

    });

    $('button#upload_firma').on('click', function(e){
        e.preventDefault();

        let img = $('input#uploadFirma')[0].files[0];

        var fd = new FormData();
        fd.append('file', img);
        fd.append('_token', token);

        let data = {};
        data._token = token;


        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "{{url('killerquotes/settings/upload_firma')}}",
            data: fd,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {
                console.log(data)
            }
        });

    });


    </script>
@endpush
