@push("style")
    <style>
        .preventivo-table tr {
            page-break-inside: avoid;
        }
        .preventivo-table tbody tr td {
            color: black !important;
        }
        .preventivo-table thead tr th {
            background-color: {{ $base_settings->default_color }};
            text-align: center;
            color: white;
        }
        .preventivo-table .total-label {
            color: white !important;
            background-color: {{ $base_settings->default_color }};
        }
        .preventivo-table .total {
            font-size: 1.4em;
            font-weight: bold;
        }
    </style>
@endpush
<div class="container">
    @include('killerquote::pdf.components.intestation')
    <div class="row">
        <div class="col-xs-12">
            <h3 class="section-title text-center">@lang('killerquote::kq.preventivo_economico')</h3>
            <table class="table table-bordered preventivo-table text-center">
                <thead>
                    <tr>
                        <th>@lang('killerquote::kq.prodotto')</th>
                        <th>@lang('killerquote::kq.quantita')</th>
                        <th style="width:150px">@lang('killerquote::kq.prezzo')</th>
                    </tr>
                </thead>
                <tbody>
                    @php ($sum = 0) @endphp

                    @if($quote->company->privato)

                        @foreach($quote->items as $item)
                            @php $sum += ($item->importo_scontato_con_iva*$item->qta) @endphp

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
                                <td>€ {{ number_format($item->importo_scontato_con_iva, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach


                        @if($quote->sconto_value)
                            <tr class="">
                                <td class=" text-right"><b>@lang('killerquote::kq.totale')</b></td>
                                <td colspan="2"><b>€ {{ number_format($sum, 2, ',', '.') }} <small style="font-size:60%;"> @lang('killerquote::kq.iva_inc')</small></b></td>
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
                                <td colspan="2">€ {{ number_format($discounted, 2, ',', '.') }} <small style="font-size:60%;"> @lang('killerquote::kq.iva_inc')</small></td>
                            </tr>
                        @else
                            <tr class="total">
                                <td class="total-label text-right">@lang('killerquote::kq.totale')</td>
                                <td colspan="2">€ {{ number_format($sum, 2, ',', '.') }} <small style="font-size:60%;"> @lang('killerquote::kq.iva_inc')</small></td>
                            </tr>
                        @endif

                    @else

                        @foreach($quote->items as $item)
                            @php $sum += ($item->importo_scontato*$item->qta) @endphp
                            <tr>
                                <td class="text-left px-2">
                                    @if($item->product->name)
                                        <b>{{ $item->product->name }}</b>
                                    @else
                                        <b>{{ $item->product->codice }}</b>
                                    @endif
                                    @if( ($item->descrizione != "") || !is_null($item->descrizione))
                                        <br><small>{{ucfirst($item->descrizione)}}</small>
                                    @endif
                                </td>
                                <td>{{ $item->qta }}</td>
                                <td>€ {{ number_format($item->importo_scontato, 2, ',', '.') }} </td>
                            </tr>
                        @endforeach

                        @if($quote->sconto_value)
                            <tr class="">
                                <td class=" text-right"><b>@lang('killerquote::kq.totale')</b></td>
                                <td colspan="2"><b>€ {{ number_format($sum, 2, ',', '.') }} </b></td>
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
                                <td colspan="2">€ {{ number_format($discounted, 2, ',', '.') }} <small style="font-size:60%;"> @lang('killerquote::kq.+_IVA')</small></td>
                            </tr>
                        @else
                            <tr class="total">
                                <td class="total-label text-right">@lang('killerquote::kq.totale')</td>
                                <td colspan="2">€ {{ number_format($sum, 2, ',', '.') }} <small style="font-size:60%;"> @lang('killerquote::kq.+_IVA')</small></td>
                            </tr>
                        @endif

                    @endif


                </tbody>
            </table>
        </div>
    </div>


</div>
