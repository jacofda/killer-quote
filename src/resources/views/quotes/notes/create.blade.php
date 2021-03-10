@include('killerquote::quotes.notes.show')

{!! Form::open(['url' => route('killerquotes.notes.store', $killerquote->id), 'method' => 'POST']) !!}
    <div class="form-group">
        <label>Nota</label>
        {!! Form::textarea('note', null, ['class' => 'form-control']) !!}
    </div>
{!! Form::close() !!}
