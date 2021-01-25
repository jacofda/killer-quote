@if(in_array($quote->company->lingua, $activeLangs))
    <a target="_BLANK" href="{{ url("killerquotes/{$quote->id}/pdf/{$quote->company->lingua}") }}" title="Esporta PDF" class="btn btn-primary btn-icon btn-sm"><i class="fa fa-file-pdf"></i></a>
@else
@endif
