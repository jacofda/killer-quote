@php ($sum = 0) @endphp

@foreach($quote->items as $item)
    @php
        $iva_multiplier = 1;
        if($item->perc_iva)
        {
            $iva_multiplier = (1+($item->perc_iva/100));
        }
        $sum += ($item->importo*$item->qta*$iva_multiplier)
    @endphp

    <tr>
        <td class="text-left px-2">
            @if($item->product->nome)
                <b>{{ $item->product->name }}</b>
            @else
                <b>{{ $item->product->codice }}</b>
            @endif
            @if( ($item->descrizione != "") || !is_null($item->descrizione))
                <br><small>{{ucfirst($item->descrizione)}}</small>
            @endif
        </td>
        <td>{{ $item->qta }}</td>
        <td>€ {{ number_format($item->importo*$iva_multiplier, 2, ',', '.') }}</td>
    </tr>
@endforeach


@if($quote->sconto_value)
    <tr class="">
        <td class=" text-right"><b>@lang('killerquote::kq.totale')</b></td>
        <td colspan="2"><b>€ {{ number_format($sum, 2, ',', '.') }}</td>
    </tr>
    <tr class="">
        <td  class=" text-right"><b>Extra Sconto</b></td>
        <td colspan="2"><b>{{$quote->sconto_value}} %</b></td>
    </tr>
    @php
        $discounted = $sum * (1-($quote->sconto_value/100));
    @endphp
    <tr class="total">
        <td  class="total-label text-right">Totale Scontato</td>
        <td colspan="2">€ {{ number_format($discounted, 2, ',', '.') }} </td>
    </tr>
@else
    <tr class="total">
        <td class="total-label text-right">@lang('killerquote::kq.totale')</td>
        <td colspan="2">€ {{ number_format($sum, 2, ',', '.') }} </td>
    </tr>
@endif
