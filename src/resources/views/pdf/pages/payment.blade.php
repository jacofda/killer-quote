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

        .payments .bordered-box {
            height: 4cm;
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
    <div class="row" style="margin-bottom:1cm;">
        <div class="col-xs-12">
            <h3 class="section-title text-center">PREVENTIVO ECONOMICO</h3>
            <table class="table table-bordered preventivo-table text-center">
                <thead>
                    <tr>
                        <th>Prodotto</th>
                        <th>Quantità</th>
                        <th width="20%">Prezzo</th>
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
                                <td>€ {{ number_format($item->importo_scontato_con_iva, 2, ',', '.') }} <small> IVA inc.</small></td>
                            </tr>
                        @endforeach

                        <tr class="total">
                            <td colspan="2" class="total-label text-right">Totale</td>
                            <td>€ {{ number_format($sum, 2, ',', '.') }}</td>
                        </tr>

                    @else

                        @foreach($quote->items as $item)
                            @php($sum += ($item->importo_scontato*$item->qta))
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
                                <td>€ {{ number_format($item->importo_scontato, 2, ',', '.') }} <small>+ IVA</small></td>
                            </tr>
                        @endforeach

                        <tr class="total">
                            <td colspan="1" class="total-label text-right">Totale</td>
                            <td colspan="2">€ {{ number_format($sum, 2, ',', '.') }} <small>+ IVA</small></td>
                        </tr>

                    @endif


                </tbody>
            </table>
        </div>
    </div>

    <div class="row payments">
        <div class="col-xs-12">
            <h3 class="section-title text-center">PAGAMENTO</h3>
            <div class="row">
                @if($quote->sconto_text || $quote->sconto_value)
                    <div class="col-xs-6 col-xs-offset-3">
                        <div class="bordered-box">
                            <div class="bordered-box-text">
                                {!! $quote->sconto_text  !!}
                                @if($quote->sconto_value)
                                    <h4 style="margin: 3mm 0 0 0; font-weight: bold;">{{ $quote->sconto_value }}% sconto</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    @foreach($settings['metodi_pagamento']->value as $sconto)
                        <div class="col-xs-4">
                            <div class="bordered-box">
                                <div class="bordered-box-text">
                                    {!!  $sconto['text'] !!}
                                    @if($sconto['number'] != "0.00")
                                        <h4 style="margin: 3mm 0 0 0; font-weight: bold;">{{ $sconto['number']+0 }}% sconto</h4>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
