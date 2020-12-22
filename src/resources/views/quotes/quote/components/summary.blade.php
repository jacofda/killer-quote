<div class="col-md-6">
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title">Riepilogo Trattativa</h3>
        </div>
        <div class="card-body">

            <div class="form-group row" style="height:100%;">
                <div class="col-sm-12" style="height:100%;">
                    {!! Form::textarea('summary', isset($quote) ? $quote->summary : '', ['id' => 'summary', 'style' => 'height: 100%']) !!}
                </div>
            </div>



            @isset($quote)
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Accettato</label>
                    <div class="col-sm-8">
                        {!! Form::select('accepted', [null=>'',0=>'No', 1=>'Sì'], $quote->accepted,['class' => 'form-control']) !!}
                    </div>
                </div>

                <div id="note" class="form-group row @if($quote->accepted !== 0) d-none @endif">
                    <div class="col-sm-12">
                        {!! Form::textarea('notes', isset($quote) ? $quote->notes : null, ['class' => 'form-control', 'rows' => '2', 'placeholder' => 'perché non è stato accettato ...']) !!}
                    </div>
                </div>
            @endisset


        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('js/summernote-images-upload.js') }}"></script>
    <script>

        @if(isset($quote))
            @if($quote->accepted !== 0)
                let offset = 240;
            @else
                let offset = 300;
            @endif
        @else
            let offset = 185;
        @endif

        $('select[name="accepted"]').on('change', function(){
            let note = $('#note');
            if(parseInt($(this).val()) === 0)
            {
                note.removeClass('d-none');
            }
            else
            {
                note.addClass('d-none');
            }
        });

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
                height: $('#card-quote').height()-offset
            };
            $('textarea#summary').summernote(smOptions);

        })(jQuery)
    </script>
@endpush
