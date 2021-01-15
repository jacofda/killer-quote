@if((!is_null($contact->email)) && (is_null($contact->company_id)))
    <a href="#" class="btn btn-secondary btn-icon btn-sm makeCompanyAndQuote" title="trasforma in azienda e crea preventivo" data-id="{{$contact->id}}"><i class="fas fas fa-file-invoice-dollar"></i></a>
@else
    <a href="{{url('killerquotes/create?company_id='.$contact->company->id)}}" class="btn btn-secondary btn-icon btn-sm" title="crea preventivo" data-id="{{$contact->id}}"><i class="fas fas fa-file-invoice-dollar"></i></a>
@endif
