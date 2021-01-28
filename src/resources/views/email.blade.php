@component('mail::message')
# {{$object}}

{!!$body!!}


@lang('killerquote::d.grazie'),<br>
{{ config('app.name') }}
@endcomponent
