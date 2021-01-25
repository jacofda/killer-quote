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
                        <th>@lang('killerquote::kq.prezzo')</th>
                    </tr>
                </thead>
                <tbody>
                    @php($sum = 0)

                    @if($quote->company->privato)

                        @foreach($quote->items as $item)
                            @php($sum += ($item->importo_scontato_con_iva*$item->qta))

                            <tr>
                                <td class="text-left px-2">
                                    @if($item->product->nome)
                                        <b>{{ $item->product->nome }}</b>
                                    @else
                                        <b>{{ $item->product->codice }}</b>
                                    @endif
                                    @if( ($item->descrizione != "") || !is_null($item->descrizione))
                                        <br><small>{{ucfirst($item->descrizione)}}</small>
                                    @endif
                                </td>
                                <td>{{ $item->qta }}</td>
                                <td>€ {{ number_format($item->importo_scontato_con_iva, 2, ',', '.') }} <small> @lang('killerquote::kq.iva_inc')</small></td>
                            </tr>
                        @endforeach

                        <tr class="total">
                            <td  class="total-label text-right">@lang('killerquote::kq.totale')</td>
                            <td colspan="2">€ {{ number_format($sum, 2, ',', '.') }} <small> @lang('killerquote::kq.iva_inc')</small></td>
                        </tr>

                    @else

                        @foreach($quote->items as $item)
                            @php($sum += ($item->importo_scontato*$item->qta))
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
                                <td>€ {{ number_format($item->importo_scontato, 2, ',', '.') }} <small>@lang('killerquote::kq.+_IVA')</small></td>
                            </tr>
                        @endforeach

                        <tr class="total">
                            <td class="total-label text-right">@lang('killerquote::kq.totale')</td>
                            <td colspan="2">€ {{ number_format($sum, 2, ',', '.') }} <small>@lang('killerquote::kq.+_IVA')</small></td>
                        </tr>

                    @endif


                </tbody>
            </table>
        </div>
    </div>


</div>
