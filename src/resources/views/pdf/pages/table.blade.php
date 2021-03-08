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

                    @if($quote->company->privato)
                        @if($quote->company->nazione == 'IT')
                            @include('killerquote::pdf.pages.cases.privato-IT')
                        @else
                            @include('killerquote::pdf.pages.cases.privato-NONIT')
                        @endif
                    @else
                        @if($quote->company->nazione == 'IT')
                            @include('killerquote::pdf.pages.cases.azienda-IT')
                        @else
                            @include('killerquote::pdf.pages.cases.azienda-NONIT')
                        @endif
                    @endif



                </tbody>
            </table>
        </div>

        @if(!config('app.sale_on_vat'))
            @if(\Areaseb\Core\Models\Country::where('iso2', $quote->company->nazione)->first()->is_eu)
                <div class="col-xs-12" style="margin-top:-15px;">
                    <small><i style="color:#888;">*If your company will pass the <q>VAT number validation</q>, your total will be tax free.</i></small>
                </div>
            @else
            @endif
        @endif

    </div>


</div>
