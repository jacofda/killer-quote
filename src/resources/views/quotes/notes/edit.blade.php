{!! Form::open(['url' => route('killerquotes.notes.update', , ['killerquote' => $killerquote->id, 'note' => $note->id]), 'method' => 'PATCH']) !!}

    <div class="form-group">
        <label>Nota</label>
        {!! Form::textarea('note', $note->note, ['class' => 'form-control']) !!}
    </div>

{!! Form::close() !!}
