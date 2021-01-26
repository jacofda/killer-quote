<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="{{asset('css/pdf/b3.css')}}">
    <style>
        footer {
            padding: 5mm 2mm 2mm 0;
            border-top: 1.5mm solid {{ $base_settings->default_color }};
        }
        footer p {
            margin:0;
            padding:0;
            font-size:10pt;
        }
    </style>
</head>
<body>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-xs-6 text-left">
                <p>{{ $base_settings->rag_soc }}</p>
                <p>
                    @lang('killerquote::kq.sede') {{ $base_settings->indirizzo }},
                    {{ $base_settings->cap }} {{$base_settings->citta}} ({{$base_settings->provincia}})
                </p>
                <p>
                    @lang('killerquote::kq.cf') {{ $base_settings->cod_fiscale }} |  @lang('killerquote::kq.piva') {{ $base_settings->piva}}
                </p>
                <p>
                    Tel. {{ $base_settings->telefono }}
                </p>
            </div>
            <div class="col-xs-6 text-right">
                @if($base_settings->banca)
                    <p>@lang('killerquote::kq.banca') {{ $base_settings->banca }}</p>
                @endif
                @if($base_settings->IBAN)
                    <p>IBAN: {{ $base_settings->IBAN }}</p>
                @endif
                @if($base_settings->SWIFT)
                    <p>BIC/SWIFT: {{ $base_settings->SWIFT }}</p>
                @endif
                @if($base_settings->email || $base_settings->sitoweb)
                    <p>
                        {{ $base_settings->email ? $base_settings->email : "" }}
                        {{ $base_settings->email && $base_settings->sitoweb ? '-' : '' }}
                        {{ $base_settings->sitoweb ? $base_settings->sitoweb : "" }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</footer>
</body>
</html>
