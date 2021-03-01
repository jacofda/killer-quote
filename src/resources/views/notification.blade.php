@component('mail::message')
# Notifica scadenze preventivi

Salve amministratore {{ config('app.name') }},<br>
@if(count($preventivi) > 1)
    i seguenti preventivi sono in scadenza.
@else
    questo preventivo è in scadenza.
@endif

@foreach($preventivi as $preventivo)
@component('mail::panel'){{ $preventivo }}@endcomponent
@endforeach

@endcomponent
