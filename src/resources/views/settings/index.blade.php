@extends('areaseb::layouts.app')

@include('areaseb::layouts.elements.title', ['title' => 'Settings - Preventivi Killer'])

@section('css')
    <link rel="stylesheet" href="{{asset('css/dropzone5-7-0.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/popup/min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}">
    <style>
        #reviews-txt .review_txt_div:last-child {
            margin-bottom: 0 !important;
        }

        .col-vertical-center {
            display: flex;
            align-items:center;
        }

        .row.same-height {
            display: flex;
            display: -webkit-flex;
            flex-wrap: wrap;
        }

        /* Summernote Ajax File Upload */
        body.ajaxfileupload-overlay {
            position: relative;
            height: 100%;
        }

        .ajaxfileupload-panel {
            z-index: 1000;
            border: solid 1px #999;
            transform: translate(-50%, -50%);
        }

        #ajaxFileUploadInner {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 20px;
        }

        #ajaxPanelClose {
            font-weight: bold;
            font-size: 30px;
            transform: rotate(45deg);
            cursor: pointer;
            position: absolute;
            top: 0px;
            right: 10px;
        }
        .ajaxfileupload-panel h4 {
            margin-bottom: 0;
        }
        #ajaxFileUploadSubmit {
            padding: 3px 15px;
            background-color: #232848;
            color: #fff;
            font-weight: bold;
            margin-top: 10px;
            cursor: pointer;
            max-width: 50%;
            text-transform: uppercase;
            text-align: center;
            display: inline-block;
            border-radius: 5px;
        }

        body.ajaxfileupload-overlay:after {
            content: "";
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 10;
            background-color: rgba(0,0,0,0.2);
        }
    </style>
@stop

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{route('killerquotes.index')}}">Preventivi Killer</a></li>
@stop

@include('areaseb::layouts.elements.title', ['title' => 'Settings'])


@section('content')
    <div class="row">
        @include('areaseb::components.errors')
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form id="killerquotes-settings-form" method="POST" action="{{ url('killerquotes/settings') }}">

                        {!! csrf_field() !!}

                        <!-- Logo -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Logo</label>
                            <div class="col-sm-9">
                                @include('killerquote::settings.components.media', ['model' => $settings['logo']])
                            </div>
                        </div>

                        <!-- Payoff -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Payoff</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="payoff" value="{{$settings['payoff']->value}}" />
                            </div>
                        </div>

                        <!-- Chi siamo -->
                        <div class="form-group row">
                            <label for="chi-siamo" class="col-sm-3 col-form-label">Chi siamo</label>
                            <div class="col-sm-9">
                                <textarea name="chi_siamo" id="chi-siamo">
                                    {!! $settings['chi_siamo']->value !!}
                                </textarea>
                            </div>
                        </div>

                        <!-- Perché sceglierci -->
                        <div class="form-group row my-5">
                            <label class="col-sm-3 col-form-label">Perché sceglierci</label>
                            <div class="col-sm-9">
                                @include('killerquote::settings.components.perche_sceglierci', ['model' => $settings['perche_sceglierci']])
                            </div>
                        </div>

                        <!-- Bonus -->
                        <div class="form-group row my-5">
                            <label class="col-sm-3 col-form-label">Bonus</label>
                            <div class="col-sm-9">
                                <textarea name="bonus" id="bonus">
                                    {!! $settings['bonus']->value !!}
                                </textarea>
                            </div>
                        </div>

                        <!-- Mostra Bonus -->
                        <div class="form-group row my-5">
                            <label class="col-sm-3 col-form-label">Mostra Bonus</label>
                            <div class="col-sm-9">
                                {!! Form::select('mostra_bonus', [1 => 'Sì', 0 => "No"], $settings['mostra_bonus']->value, ['class' => 'custom-select', 'id' => 'mostra_bonus']) !!}
                            </div>
                        </div>


                        <!-- Metodi di pagamento -->
                        <div class="form-group row my-5">
                            <label class="col-sm-3 col-form-label">Metodi di pagamento</label>
                            <div class="col-sm-9">
                                @include('killerquote::settings.components.metodi_pagamento', ['model' => $settings['metodi_pagamento']])
                            </div>
                        </div>

                        <!-- Garanzie -->
                        <div class="form-group row">
                            <label for="garanzie" class="col-sm-3 col-form-label">Garanzie</label>
                            <div class="col-sm-9">
                                <textarea name="garanzie" id="garanzie">
                                    {!! $settings['garanzie']->value !!}
                                </textarea>
                            </div>
                        </div>

                        <!-- Recensioni -->
                        <div class="form-group row my-5">
                            <label class="col-sm-3 col-form-label">Recensioni</label>
                            <div class="col-sm-9">
                                @include('killerquote::settings.components.recensioni', ['model' => $settings['recensioni']])
                            </div>
                        </div>

                        <!-- Glossario -->
                        <div class="form-group row">
                            <label for="glossario" class="col-sm-3 col-form-label">Glossario</label>
                            <div class="col-sm-9">
                                <textarea name="glossario" id="glossario">
                                    {!! $settings['glossario']->value !!}
                                </textarea>
                            </div>
                        </div>



                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Privacy Policy</label>
                            <div class="col-sm-9">
                                <textarea name="privacy" id="privacy">
                                    {!! $settings['privacy']->value !!}
                                </textarea>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Eventuale Allegato PDF</label>
                            <div class="col-sm-9">
                                @include('killerquote::settings.components.upload-pdf', ['pdfFile' => $settings['pdf']])
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Immagine Firma</label>
                            <div class="col-sm-9">
                                @include('killerquote::settings.components.upload-firma', ['firmaFile' => $settings['firma_img']])
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Testo Firma</label>
                            <div class="col-sm-9">
                                <textarea name="firma_txt" id="firma_txt">
                                    {!! $settings['firma_txt']->value !!}
                                </textarea>
                            </div>
                        </div>

                        {{-- COndizioni di Vendita --}}
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Condizioni di Vendita</label>
                            <div class="col-sm-9">
                                <textarea name="cond_vendita" id="cond_vendita">
                                    {!! $settings['cond_vendita']->value !!}
                                </textarea>
                            </div>
                        </div>

                        <!-- Scadenza Preventivo -->
                        <div class="form-group row">
                            <label for="scadenza" class="col-sm-3 col-form-label">Scadenza Preventivo (giorni)</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" name="scadenza" id="scadenza">
                                    @php
                                        $scadenza = intval($settings['scadenza']->value);
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



                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success" id="submitForm"><i class="fa fa-save"></i> Salva</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


@push('scripts')
    <script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
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
                ]
            };
            $('textarea#garanzie').summernote(smOptions);
            $('textarea#glossario').summernote(smOptions);
            $('textarea#bonus').summernote(smOptions);
            $('textarea#chi-siamo').summernote(smOptions);
            $('textarea#privacy').summernote(smOptions);
            $('textarea#firma_txt').summernote(smOptions);
            $('textarea#cond_vendita').summernote(smOptions);
            $('#scadenza').select2({
                placeholder: "Seleziona un'opzione",
                tags: true,
                allowClear: true
            })
        })(jQuery)
    </script>
@endpush
