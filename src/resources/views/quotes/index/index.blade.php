@extends('areaseb::layouts.app')

@include('areaseb::layouts.elements.title', ['title' => 'Preventivi Killer'])

@section('css')
<link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}">
@stop

@php
    $activeLangs = \Areaseb\Core\Models\Setting::ActiveLangs();
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary-light">
                    <h3 class="card-title">Preventivi Killer</h3>
                    <div class="card-tools">
                        <div class="form-group mr-3 mb-0 mt-2" style="float:left;">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="customSwitch1" @if(request()->input()) checked @endif>
                                <label class="custom-control-label" for="customSwitch1">Ricerca Avanzata</label>
                            </div>
                        </div>

                        @can('killerquotes.write')
                            <a class="btn btn-primary" href="{{route('killerquotes.create')}}"><i class="fas fa-plus"></i> Crea Preventivo</a>
                        @endcan
                        @can('killerquotes.configure')
                            <a class="btn btn-default" href="{{url('killerquotes/settings')}}"><i class="fas fa-cog"></i></a>
                            @include('killerquote::quotes.index.components.locale')
                        @endcan
                    </div>

                </div>
                <div class="card-body">

                    @include('killerquote::quotes.index.components.search')
                    <div class="table-responsive">
                        <table id="table" class="table table-sm table-font-xs table-bordered table-striped table-php">
                            <thead>
                            <tr>
                                <th style="width:85px;">Numero</th>
                                <th data-field="company_id" data-order="asc">Azienda <i class="fas fa-sort"></i></th>
                                <th>Importo</th>
                                <th data-field="data" data-order="asc" style="width: 150px;">Data <i class="fas fa-sort"></i></th>
                                <th data-field="data_scadenza" data-order="asc" style="width: 150px;">Scadenza <i class="fas fa-sort"></i></th>
                                <th style="width: 90px;">Attivo </th>
                                <th style="width: 90px;">Accettato </th>
                                <th style="width: 181px;"></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($quotes as $quote)
                                    <tr id="row-{{$quote->id}}">
                                        <td>{{$quote->numero}}</td>
                                        <td>{{$quote->company->rag_soc}}</td>
                                        <td>{{$quote->importo}}</td>
                                        <td>{{$quote->created_at->format('d/m/Y')}}</td>
                                        <td>{{$quote->expirancy_date->format('d/m/Y')}}</td>
                                        <td class="text-center">
                                            @if($quote->expirancy_date < \Carbon\Carbon::now())
                                                <span class="badge badge-warning">Scaduto</span>
                                            @else
                                                <span class="badge badge-success">Attivo</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($quote->accepted === null)
                                                <span class="badge badge-default">In attesa</span>
                                            @else
                                                @if($quote->accepted)
                                                    <span class="badge badge-success">Accettato</span>
                                                @else
                                                    <span class="badge badge-danger">Rifiutato</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {!! Form::open(['method' => 'delete', 'url' => route('killerquotes.destroy', $quote->id), 'id' => "form-".$quote->id]) !!}
                                                @can('killerquotes.read')
                                                    @if($quote->company->lingua != 'it')
                                                        @include('killerquote::quotes.index.components.pdf-locale')
                                                    @else
                                                        <a target="_BLANK" href="{{ url("killerquotes/{$quote->id}/pdf") }}" title="Esporta PDF" class="btn btn-primary btn-icon btn-sm"><i class="fa fa-file-pdf"></i></a>
                                                    @endif
                                                @endcan
                                                @can('killerquotes.write')
                                                    <a href="#" title="Invia al cliente" data-numero="{{$quote->numero}}" data-date="{{$quote->created_at->format('d/m/Y')}}" data-id="{{$quote->id}}" data-company="{{$quote->company->rag_soc}}" class="btn btn-info btn-icon btn-sm sendQuote"><i class="far fa-paper-plane"></i></a>
                                                    <a href="{{ route('killerquotes.edit', $quote->id) }}" title="Modifica" class="btn btn-warning btn-icon btn-sm"><i class="fa fa-edit"></i></a>
                                                    <a href="#" title="Duplica" class="btn btn-secondary btn-icon btn-sm btn-duplicate" data-id="{{$quote->id}}"><i class="fa fa-clone"></i></a>
                                                @endcan
                                                @if($quote->accepted !== 1)
                                                    @can('killerquotes.delete')
                                                        <button type="submit" id="{{$quote->id}}" title="Elimina" class="btn btn-danger btn-icon btn-sm delete"><i class="fa fa-trash"></i></button>
                                                    @endcan
                                                @endif
                                            {!! Form::close() !!}

                                            {!! Form::open(['url' => route('killerquotes.duplicate', $quote->id), 'id' => "duplica-".$quote->id, 'class' => 'd-none']) !!}
                                                <button type="submit" class="d-none">SUBMIT</button>
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="table-responsive">

                        </div>
                        <div class="card-footer text-center">
                            <p class="text-left text-muted">{{$quotes->count()}} di {{ $quotes->total() }} preventivi</p>
                            {{ $quotes->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>


<div class="modal" tabindex="-1" role="dialog" id="quote-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invia Preventivo al cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {!! Form::open(['url' => '#']) !!}
                    <div class="form-group">
                        <label>Oggetto</label>
                        {{Form::text('object', null, ['class' => 'form-control'])}}
                    </div>
                    <div class="form-group">
                        <label>Corpo Email</label>
                        {{Form::textarea('body', null, ['class' => 'form-control'])}}
                    </div>

                    <button type="submit" class="btn btn-success btn-block">Invia</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>


            @stop

@section('scripts')
    <script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
    <script>

    const smOptions = {
            height: 180,
            toolbar: [
                ['font', ['bold', 'italic']],
            ]
        };

        $('a.sendQuote').on('click', function(e){
            e.preventDefault();
            let oggetto = "Invio preventivo N. "+$(this).attr('data-numero')+" del "+$(this).attr('data-date');
            let testo = 'Spett. '+ $(this).attr('data-company');
            let action = baseURL+'killerquotes/'+$(this).attr('data-id')+'/send-pdf';
            $('#quote-modal').modal('show');
            $('#quote-modal input[name="object"]').val(oggetto);
            $('#quote-modal textarea').val(testo);
            $('#quote-modal textarea').summernote(smOptions);
            $('#quote-modal form').attr('action', action);

        });

        $('#customSwitch1').on('change', function(){
            if($(this).prop('checked') === true)
            {
                $('#advancedSearchBox').removeClass('d-none');
            }
            else
            {
                $('#advancedSearchBox').addClass('d-none');
            }
        });

        $('#refresh').on('click', function(e){
            e.preventDefault();
            let currentUrl = window.location.href;
            let arr = currentUrl.split('?');
            window.location.href = arr[0];
        });

        $('a.btn-duplicate').on('click', function(e){
            e.preventDefault();
            let f = $('form#duplica-'+$(this).attr('data-id'))[0];
            f.submit();
        });
    </script>
@stop
