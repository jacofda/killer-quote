@component('mail::message')
# {{$object}}

{!!$body!!}


@lang('killerquote::kq.grazie'),<br>
{{ config('app.name') }}
@endcomponent
