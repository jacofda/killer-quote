<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Carica File (solo pdf)</h3>
            </div>
            <div class="card-body">
                <div class="input-group">

                    @if($pdfFile->value)
                        <div class="custom-file">
                            <input type="file" accept=".pdf" class="custom-file-input" id="uploadPdf" lang="it">
                            <label class="custom-file-label" for="uploadPdf" data-browse="Cambia">{{$pdfFile->value}}</label>
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-default" id="upload">Carica</button>
                            <button class="btn btn-danger" id="delete">Elimina</button>
                        </div>
                    @else
                        <div class="custom-file">
                            <input type="file" accept=".pdf" class="custom-file-input" id="uploadPdf" lang="it">
                            <label class="custom-file-label" for="uploadPdf" data-browse="Cerca">Seleziona PDF</label>
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-default" id="upload" disabled>Carica</button>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{-- <script src="{{asset('js/dropzone5-7-0.min.js')}}"></script> --}}
    <script>

    bsCustomFileInput.init();

    $('input#uploadPdf').on('change', function(){
        $('button#upload').prop('disabled', false);
        $('button#upload').removeClass('btn-default');
        $('button#upload').addClass('btn-success');
    });

    $('button#delete').on('click', function(e){
        e.preventDefault();

        let data = {};
        data._token = token;

        $.post("{{url('killerquotes/settings/upload_pdf')}}", data, function(r){
            console.log(r);
            $('button#delete').remove();
            $('label.custom-file-label').text("Seleziona PDF");
        });

    });

    $('button#upload').on('click', function(e){
        e.preventDefault();

        let pdf = $('input#uploadPdf')[0].files[0];

        var fd = new FormData();
        fd.append('file', pdf);
        fd.append('_token', token);

        let data = {};
        data._token = token;


        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "{{url('killerquotes/settings/upload_pdf')}}",
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
