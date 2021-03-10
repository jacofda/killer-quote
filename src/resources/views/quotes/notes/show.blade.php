@if(!$notes->isEmpty())
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Note</h3>
        </div>
        <div class="card-body accessi">

                @foreach($notes as $note)
                    <div class="col-12 mb-3" id="cardAccesso-{{$note->id}}">
                        <div class="card">
                            <div class="card-header card-sm">
                                <h3 class="card-title">{{$note->created_at->format('d/m/Y')}}</h3>
                                <div class="card-tools">
                                    {{-- <a data-note_id="{{$note->id}}" href="{{route('killerquotes.notes.destroy', ['killerquote' => $killerquote->id, 'note' => $note->id])}}" class="deleteNote text-danger"> <i class="fa fa-trash"></i></a> --}}
                                    {!! Form::open(['method' => 'delete', 'url' => route('killerquotes.notes.destroy', ['killerquote' => $killerquote->id, 'note' => $note->id]), 'id' => "formNote-".$loop->index]) !!}
                                        <input type="hidden" name="origin" value="index" >
                                        <button type="submit" id="deleteAccesso{{$loop->index}}" class="btn btn-tool deleteAccesso"><i class="fa fa-trash text-danger"></i></button>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                            <div class="card-body">
                                {{$note->note}}
                            </div>
                        </div>
                    </div>
                @endforeach

        </div>
    </div>
@endif
