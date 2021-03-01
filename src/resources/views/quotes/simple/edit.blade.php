@extends('areaseb::layouts.app')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{route('killerquotes.index')}}">Preventivi</a></li>
@stop

@include('areaseb::layouts.elements.title', ['title' => 'Modifica Preventivo PDF'])

@section('content')

    <div class="card">
        <div class="card-header">
            <div class="card-title">Modifica Preventivo PDF</div>
        </div>
        {!! Form::open(['url' => route('quotes.update', $quote->id), 'method' => 'PATCH', 'id' => "form-simple-quote", 'files' => true]) !!}
        <div class="card-body">

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Azienda*</label>
                <div class="col-sm-9">
                    {!! Form::select('company_id', $companies, $quote->company_id, ['class' => 'form-control', 'required']) !!}
                </div>
            </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Scadenza *</label>
                    <div class="col-sm-9">
                        <div class="input-group date" id="reservationdate" data-target-input="nearest">
                            <input type="text" value="{{$quote->expirancy_date->format('d/m/Y')}}" name="expirancy" class="form-control datetimepicker-input" data-target="#reservationdate">
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Importo *</label>
                    <div class="col-sm-9">
                        {!! Form::text('importo', $quote->clean_importo, ['class' => 'form-control input-decimal', 'required']) !!}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Accettato</label>
                    <div class="col-sm-9">
                        {!! Form::select('accepted', [''=>'In attesa',0=>'No', 1=>'Sì'], $quote->accepted, ['class' => 'custom-select']) !!}
                    </div>
                </div>

            </div>
            <div class="card-footer p-0">
                <button type="submit" class="btn btn-success btn-block"><i class="fa fa-save"></i> Salva</button>
            </div>
            {!! Form::close() !!}
        </div>

    </div>



@stop

@push('scripts')
<script>
    $('#reservationdate').datetimepicker({minView: 2,format: 'DD/MM/YYYY'});
    $('select[name="company_id"]').select2({placeholder:"seleziona azienda", width:'100%'});
</script>
@endpush
