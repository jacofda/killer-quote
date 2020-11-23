<div class="col-md-6">
    <div class="card card-outline card-warning" id="card-quote">
        <div class="card-header">
            <h3 class="card-title">Preventivo</h3>
        </div>
        <div class="card-body">

            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Azienda*</label>
                <div class="col-sm-8">
                    {!! Form::select('company_id',$companies, isset($quote) ? $quote->company_id : null, ['class' => 'form-control select2bs4', 'data-placeholder' => 'Seleziona Azienda', 'required', 'data-fouc']) !!}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Scadenza (gg)*</label>
                <div class="col-sm-8">
                    <select class="form-control select2bs4" name="scadenza" id="scadenza" required>
                        @php
                            $scadenza = null;
                            if(isset($quote))
                                $scadenza = (\Carbon\Carbon::now()->diff($quote->expirancy_date))->days;
                        @endphp
                        <option {{ !$scadenza ? "selected" : "" }} disabled></option>
                        <option {{ $scadenza === 45 ? "selected" : "" }} value="45">45</option>
                        <option {{ $scadenza === 30 ? "selected" : "" }} value="30">30</option>
                        <option {{ $scadenza === 15 ? "selected" : "" }} value="15">15</option>
                        @if($scadenza > 0 && $scadenza !== 45 && $scadenza !== 30 && $scadenza !== 15)
                            <option selected value="{{ $scadenza }}">{{$scadenza}}</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Sconto (testo)</label>
                <div class="col-sm-8">
                    {!! Form::text('sconto_text', isset($quote) && isset($quote->sconto_text) ? $quote->sconto_text : null, ['class' => 'form-control', 'id' => 'sconto-text']) !!}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Sconto (%)</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        {!! Form::text('sconto_value', isset($quote) && isset($quote->sconto_value) ? $quote->sconto_value : null, ['class' => 'form-control input-decimal', 'id' => 'sconto-value']) !!}
                        <div class="input-group-append">
                            <span class="input-group-text input-group-text-sm" id="basic-addon2">00.00%</span>
                        </div>
                    </div>
                </div>
            </div>

            @isset($quote)
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">Immagini</label>
                    <div class="col-sm-12">
                        <div class="dropzone" id="imagesDropzoneForm">
                            <div class="row">
                                <div class="fallback">
                                    <input name="file" type="file"  />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endisset
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{asset('js/dropzone5-7-0.min.js')}}"></script>
    <script>
        (function() {
            $('#scadenza').select2({
                placeholder: "Seleziona un'opzione",
                tags: true,
                allowClear: true
            });


            @isset($quote)
                Dropzone.options.imagesDropzoneForm = {
                    url: "{{ url('api/media/upload') }}",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    addRemoveLinks: true,
                    paramName: "file",
                    maxFilesize: 200,
                    sending: function(file, xhr, formData) {
                        formData.append("mediable_id", "{{$quote->id}}");
                        formData.append("mediable_type", "{{str_replace("\\","\\\\", $quote->full_class)}}");
                        formData.append("directory", "{{$quote->directory}}");
                    },
                    success: function(file, response) {
                        file.serverId = Number(response);
                    },
                    init: function() {
                        // Load existing files
                        let mockFile = {};
                        @foreach($quote->media()->where('mime', 'image')->get() as $media)
                            mockFile = {
                            name: "{{$media->filename}}",
                            size: {{$media->size*1024}},
                            serverId: {{ $media->id }}
                        };
                        this.displayExistingFile(mockFile, "{{$media->getDisplayAttribute()}}");
                        this.files.push(mockFile);
                        @endforeach

                        this.on('removedfile', function(file) {
                            let form = `
                                <form method="POST" class="d-none" action="{{ url('api/media/delete') }}">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="id" value="`+file.serverId+`">
                                    <input name="_method" type="hidden" value="DELETE">
                                </form>
                            `;
                            $(form).appendTo('body').submit();
                        });
                    }
                };
            @endisset
        })(jQuery)
    </script>
@endpush
