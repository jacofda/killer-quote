<div class="col-md-6">
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title">Sommario</h3>
        </div>
        <div class="card-body">

            <div class="form-group row" style="height:100%;">
                <div class="col-sm-12" style="height:100%;">
                    {!! Form::textarea('summary', isset($quote) ? $quote->summary : '', ['id' => 'summary', 'style' => 'height: 100%']) !!}
                </div>
            </div>


        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('js/summernote-images-upload.js') }}"></script>
    <script>
        (function() {
            const smOptions = {
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'ajaximageupload']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                height: $('#card-quote').height()-184.18
            };
            $('textarea#summary').summernote(smOptions);
            $('#scadenza').select2({
                placeholder: "Seleziona un'opzione",
                tags: true,
                allowClear: true
            });
        })(jQuery)
    </script>
@endpush
