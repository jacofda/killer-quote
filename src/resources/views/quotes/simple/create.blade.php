@extends('areaseb::layouts.app')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{route('killerquotes.index')}}">Preventivi</a></li>
@stop

@include('areaseb::layouts.elements.title', ['title' => 'Crea Preventivo Semplice'])

@section('content')

    <div class="card">
        <div class="card-header">
            <div class="card-title">Crea Preventivo Semplice</div>
        </div>
        {!! Form::open(['url' => route('quotes.store'), 'id' => "form-simple-quote", 'files' => true]) !!}
        <div class="card-body">

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Azienda*</label>
                <div class="col-sm-9">
                    {!! Form::select('company_id', $companies, null, ['class' => 'form-control', 'required']) !!}
                </div>
            </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Preventivo PDF*</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <div class="custom-file">
                                <input name="file" type="file" accept=".pdf" class="custom-file-input" id="upload" lang="it" required>
                                <label class="custom-file-label" for="upload" data-browse="Cerca">Seleziona pdf</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Scadenza *</label>
                    <div class="col-sm-9">
                        <div class="input-group date" id="reservationdate" data-target-input="nearest">
                            <input type="text" value="{{\Carbon\Carbon::today()->addDays(15)->format('d/m/Y')}}" name="expirancy" class="form-control datetimepicker-input" data-target="#reservationdate">
                            <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Importo *</label>
                    <div class="col-sm-9">
                        {!! Form::text('importo', null, ['class' => 'form-control input-decimal', 'required']) !!}
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
