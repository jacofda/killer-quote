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
                            <a class="btn btn-primary" href="{{route('quotes.create')}}"><i class="fas fa-plus"></i> PDF</a>
                            <a class="btn btn-primary" href="{{route('killerquotes.create')}}"><i class="fas fa-plus"></i> Killer</a>
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
                        <div style="position:relative">
                            <div style="position:absolute;">
                                <span class="btn btn-warning btn-sm text-warning">CIA</span> = scaduti
                            </div>
                        </div>
                        <table id="table" class="table table-sm table-font-xs table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width:85px;">Numero</th>
                                <th>Azienda</th>

                                <th>Importo</th>
                                <th>Data</th>
                                <th>Scadenza</th>

                                <th>Accettato </th>

                                @if(\Illuminate\Support\Facades\Schema::hasTable('testimonials') || \Illuminate\Support\Facades\Schema::hasTable('agents'))
                                    <th>Referente</th>
                                    <th>Premio</th>
                                @endif

                                <th data-sortable="false"></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($quotes as $quote)

                                    @php
                                        $bg = '';
                                        if( ( $quote->expirancy_date->lt(\Carbon\Carbon::now()) ) && (is_null($quote->accepted)) )
                                        {
                                            $bg = 'bg-warning';
                                        }

                                        if($quote->accepted === null)
                                        {
                                            $sort = 1;
                                            $badge = '<span class="badge badge-default">In attesa</span>';
                                        }
                                        else
                                        {
                                            if($quote->accepted)
                                            {
                                                $sort = 2;
                                                $badge = '<span class="badge badge-success">Accettato</span>';
                                            }
                                            else
                                            {
                                                $sort = 3;
                                                $badge = '<span class="badge badge-danger">Rifiutato</span>';
                                            }
                                        }

                                    @endphp

                                    <tr id="row-{{$quote->id}}">
                                        <td class="{{$bg}}">{{$quote->numero}}</td>
                                        <td class="{{$bg}}">
                                            @if(isset($quote->deal))
                                                {{$quote->company}}
                                            @else
                                                {{$quote->company->rag_soc}}
                                            @endif
                                        </td>


                                        @if(isset($quote->deal))
                                            <td data-order="{{$quote->importo}}" class="{{$bg}}">€{{number_format($quote->importo, 2, ',', '.')}}</td>
                                        @else
                                            <td data-order="{{$quote->clean_importo}}" class="{{$bg}}">{{$quote->importo}}</td>
                                        @endif
                                        <td data-order="{{$quote->created_at->timestamp}}" class="{{$bg}}" >{{$quote->created_at->format('d/m/Y')}}</td>
                                        <td data-order="{{$quote->expirancy_date->timestamp}}">{{$quote->expirancy_date->format('d/m/Y')}}</td>

                                        <td data-order="{{$sort}}" class="text-center">
                                            {!!$badge!!}
                                        </td>

                                        @if(\Illuminate\Support\Facades\Schema::hasTable('testimonials') || \Illuminate\Support\Facades\Schema::hasTable('agents'))

                                            @if(isset($quote->deal))

                                                @if(\Illuminate\Support\Facades\Schema::hasTable('testimonials') || \Illuminate\Support\Facades\Schema::hasTable('agents'))

                                                    @php
                                                        $comp = \Areaseb\Core\Models\Company::where('rag_soc', $quote->company)->first();
                                                        $test = null;
                                                        if(\Illuminate\Support\Facades\Schema::hasTable('agents') && $comp->agent()->exists())
                                                        {
                                                            $test = $comp->agent()->first()->contact->fullname;
                                                        }
                                                        if(\Illuminate\Support\Facades\Schema::hasTable('testimonials') && $comp->testimonial()->exists())
                                                        {
                                                            if(is_null($test))
                                                            {
                                                                $test = $comp->testimonial()->first()->contact->fullname;
                                                            }
                                                        }
                                                    @endphp

                                                    @if($test)
                                                        <td class="{{$bg}}">
                                                            {{$test}}
                                                        </td>
                                                        <td class="{{$bg}}">
                                                            € {{ number_format($quote->commissione, 2, ',', '.' )}}
                                                        </td>
                                                    @else
                                                        <td class="{{$bg}}"></td>
                                                        <td class="{{$bg}}"></td>
                                                    @endif


                                                @else
                                                    <td class="{{$bg}}"></td>
                                                    <td class="{{$bg}}"></td>
                                                @endif


                                            @else

                                                @if(\Illuminate\Support\Facades\Schema::hasTable('testimonials'))
                                                    @if($quote->company->testimonial()->exists())
                                                        <td class="{{$bg}}">
                                                            {{$quote->company->testimonial()->first()->contact->fullname}}
                                                        </td>
                                                        <td class="{{$bg}}">
                                                            € {{ number_format($quote->commissione, 2, ',', '.' )}}
                                                        </td>
                                                    @else
                                                        <td class="{{$bg}}"></td>
                                                        <td class="{{$bg}}"></td>
                                                    @endif

                                                @elseif(\Illuminate\Support\Facades\Schema::hasTable('agents'))
                                                    @if($quote->company->agent()->exists() && (isset($quote->deal)))
                                                        <td class="{{$bg}}">
                                                            {{$quote->company->agent()->first()->contact->fullname}}
                                                        </td>
                                                        <td class="{{$bg}}">
                                                            € {{ number_format($quote->commissione, 2, ',', '.' )}}
                                                        </td>
                                                    @else
                                                        <td class="{{$bg}}"></td>
                                                        <td class="{{$bg}}"></td>
                                                    @endif
                                                @endif

                                            @endif
                                    @endif

                                        <td class="text-center">
                                            @if(!isset($quote->deal))
                                                {!! Form::open(['method' => 'delete', 'url' => route('killerquotes.destroy', $quote->id), 'id' => "form-".$quote->id]) !!}

                                                    <a href="{{route('killerquotes.notes.create', $quote->id)}}"  data-title="Aggiungi nota" title="aggiungi nota" class="btn btn-sm btn-default btn-modal"><b>N</b></a>

                                                    @can('killerquotes.read')
                                                        @if(is_null($quote->filename))
                                                            @if($quote->company->lingua != 'it')
                                                                @include('killerquote::quotes.index.components.pdf-locale')
                                                            @else
                                                                <a target="_BLANK" href="{{ url("killerquotes/{$quote->id}/pdf") }}" title="Esporta PDF" class="btn btn-primary btn-icon btn-sm"><i class="fa fa-file-pdf"></i></a>
                                                            @endif
                                                        @else
                                                            <a target="_BLANK" href="{{ config('app.url') }}storage/killerquotes/original/{{$quote->filename}}" title="Apri PDF" class="btn btn-primary btn-icon btn-sm"><i class="fa fa-file-pdf"></i></a>
                                                            <a href="{{route('quotes.edit', $quote->id)}}" title="modifica" class="btn btn-warning btn-icon btn-sm"><i class="fa fa-edit"></i></a>
                                                        @endif
                                                    @endcan
                                                    @can('killerquotes.write')
                                                        <a href="#" title="Invia al cliente" data-numero="{{$quote->numero}}" data-date="{{$quote->created_at->format('d/m/Y')}}" data-id="{{$quote->id}}" data-company="{{$quote->company->rag_soc}}" class="btn btn-info btn-icon btn-sm sendQuote"><i class="far fa-paper-plane"></i></a>
                                                        @if(is_null($quote->filename))

                                                            <a href="{{ route('killerquotes.edit', $quote->id) }}" title="Modifica" class="btn btn-warning btn-icon btn-sm"><i class="fa fa-edit"></i></a>
                                                            <a href="#" title="Duplica" class="btn btn-secondary btn-icon btn-sm btn-duplicate" data-id="{{$quote->id}}"><i class="fa fa-clone"></i></a>
                                                            @if($quote->accepted == 1)
                                                                @if(\Illuminate\Support\Facades\Schema::hasTable('deals'))
                                                                    @if(!\Deals\App\Models\OrderConfirmation::where('killer_quote_id', $quote->id)->exists())
                                                                        <a href="#" title="Crea Conferma d'ordine" class="btn btn-success btn-icon btn-sm btn-conferma" data-id="{{$quote->id}}"><i class="fas fa-thumbs-up"></i></a>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endcan
                                                    @if($quote->accepted !== 1)
                                                        @can('killerquotes.delete')
                                                            <button type="submit" id="{{$quote->id}}" title="Elimina" class="btn btn-danger btn-icon btn-sm delete"><i class="fa fa-trash"></i></button>
                                                        @endcan
                                                    @endif
                                                {!! Form::close() !!}

                                                @if(is_null($quote->filename) && ($quote->accepted == 1))
                                                    {!! Form::open(['url' => route('killerquotes.create-co', $quote->id), 'id' => "co-".$quote->id, 'class' => 'd-none']) !!}
                                                        <button type="submit" class="d-none">SUBMIT</button>
                                                    {!! Form::close() !!}
                                                @endif

                                                {!! Form::open(['url' => route('killerquotes.duplicate', $quote->id), 'id' => "duplica-".$quote->id, 'class' => 'd-none']) !!}
                                                    <button type="submit" class="d-none">SUBMIT</button>
                                                {!! Form::close() !!}
                                            @else
                                                <a target="_BLANK" href="{{ config('app.url') }}storage/deals/docs/{{$quote->filename}}" title="Apri PDF" class="btn btn-primary btn-icon btn-sm"><i class="fa fa-file-pdf"></i></a>
                                                <a target="_BLANK" href="{{ route('deals.edit', $quote->deal)}}" title="vedi trattativa" class="btn btn-success btn-icon btn-sm"><i class="fas fa-handshake"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

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

    const tableOptions = {
        aaSorting: [ [5,'asc'], [2,'desc'] ],
        responsive: true,
        autoWidth: false,
        pageLength: 300,
        bLengthChange : false,
        language: {
            search: '_INPUT_',
            searchPlaceholder: 'Scrivi per filtrare...',
            lengthMenu: '_MENU_',
            info: "_START_ di _END_ su un totale di _TOTAL_ preventivi",
            infoFiltered:   "(filtrati da _MAX_ totali)",
            zeroRecords: "Non ci sono dati",
            infoEmpty: "Non ci sono dati",
            paginate: {
                first:      "Primo",
                previous:   "Prec",
                next:       "Succ",
                last:       "Ultimo"
            },
        }
    }
    //$('#table_length').css({display:none});
    $('table#table').dataTable(tableOptions);

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

         $('a.btn-conferma').on('click', function(e){
             e.preventDefault();
             let fco = $('form#co-'+$(this).attr('data-id'))[0];
             fco.submit();
         });

    </script>
@stop
