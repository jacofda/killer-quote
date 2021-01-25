@push('style')
    <style>
        .intestation .uppercase {
            text-transform: uppercase;
        }
    </style>
@endpush
<div class="row intestation page-break-avoid" style="margin-bottom: 1.5cm;">
    <div class="col-xs-4">
        {{ $base_settings->citta }}, {{ $quote->created_at->format('d/m/Y') }}
    </div>
    <div class="col-xs-5 col-xs-offset-3">
        <b>@lang('pk.spet')</b>
        <p class="uppercase mb-0">{{ $quote->company->rag_soc }}</p>
        <br>
        <p class="uppercase mb-0">{{ $quote->company->indirizzo }}</p>
        <p class="uppercase mb-0">{{ $quote->company->cap }} {{ $quote->company->citta }} @if($quote->company->nazione == 'IT') ({{ $quote->company->provincia }}) @endif</p>
        <p class="uppercase mb-0">{{ $quote->company->nazione  }}</p>
    </div>
</div>
