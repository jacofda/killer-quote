@php
    $deal = request('deal') ? \Deals\App\Models\Deal::findOrFail(request('deal')) : null;
@endphp
   <div class="col-md-6">
       <div class="card card-outline card-warning" id="card-quote">
           <div class="card-header">
               <h3 class="card-title">Preventivo</h3>
           </div>
           <div class="card-body">

               <div class="form-group row">
                   <label class="col-sm-4 col-form-label">Azienda*</label>
                   <div class="col-sm-8">
                       @php
                            $company = null;
                            if($deal) $company = $deal->company_id;
                            else $company = isset($quote) ? $quote->company_id : null;
                       @endphp
                       {!! Form::select('company_id',$companies, $company, ['class' => 'form-control select2bs4', 'data-placeholder' => 'Seleziona Azienda', 'required', 'data-fouc']) !!}
                   </div>
               </div>

               <div class="form-group row">
                   <label class="col-sm-4 col-form-label">Data Scadenza *</label>
                   <div class="col-sm-8">

                       <div class="input-group" id="data" data-target-input="nearest">
                           @php
                               if(isset($quote))
                               {
                                   $data = $quote->expirancy_date->format('d/m/Y');
                               }
                               else
                               {
                                   $data = \Carbon\Carbon::now()->addDays(KillerQuote\App\Models\KillerQuoteSetting::DefaultExpDays())->format('d/m/Y');
                               }
                        @endphp
                        {!! Form::text('scadenza', $data, ['class' => 'form-control', 'data-target' => '#data', 'data-toggle' => 'datetimepicker', 'required']) !!}
                       <div class="input-group-append" data-target="#data" data-toggle="datetimepicker">
                           <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                       </div>
                   </div>

                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Metodo pagamento</label>
                <div class="col-sm-8">
                    {!! Form::textarea('sconto_text', isset($quote) && isset($quote->sconto_text) ? $quote->sconto_text : null, ['class' => 'metodo_pagamento', 'rows' => '4', 'id' => 'sconto-text']) !!}
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

            @if(!empty($deals) && class_exists("Deals\App\Models\Deal"))
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Trattativa</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            @php
                                $deal_id = $deal ? $deal->id : null;
                                if(!$deal_id)
                                    $deal_id = isset($quote) && !empty($quote->dealEvent) ? $quote->dealEvent->deal_id : null;
                            @endphp
                            {!! Form::select('deal_id',$deals, $deal_id, ['class' => 'form-control select2bs4', 'data-placeholder' => 'Seleziona Trattativa', 'data-fouc']) !!}
                        </div>
                    </div>
                </div>
            @endif

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
    <script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
    <script>
        (function() {
            const smOptions = {
                height: 80,
                toolbar: [
                    ['font', ['bold', 'italic']],
                ]
            };

            $('#card-quote .metodo_pagamento').summernote(smOptions);

            $('#data').datetimepicker({ format: 'DD/MM/YYYY' });

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

            $('select[name="company_id"]').on('change', function(){
                $.get( baseURL +"api/companies/"+$(this).val()+"/payment", function(response){
                    console.log(response);
                    if(response != '')
                    {
                        $('#sconto-text').summernote('pasteHTML', "<strong>"+response+"</strong>");
                    }
                });
            });

        })(jQuery)
    </script>
@endpush
