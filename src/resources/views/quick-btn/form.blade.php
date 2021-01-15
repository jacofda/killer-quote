{!! Form::open(['url' => url('contacts/make-company-and-quote'), 'id' => "makeCompanyAndQuote-".$contact->id, 'class' => 'd-none']) !!}
    <input type="hidden" name="id" value="{{$contact->id}}" />
    <button type="submit" class="d-none"></button>
{!! Form::close() !!}
