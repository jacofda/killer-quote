<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" href="{{asset('css/pdf/b3.css')}}">
        <style>
            footer {
                padding: 5mm 2mm 2mm 0;
                border-top: 2mm solid {{ $base_settings->default_color }};
            }
            footer p {
                margin:0;
                padding:0;
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
                            Sede {{ $base_settings->indirizzo }},
                            {{ $base_settings->cap }} {{$base_settings->citta}} ({{$base_settings->provincia}})
                        </p>
                        <p>
                            C.F. {{ $base_settings->cod_fiscale }} |  P.IVA {{ $base_settings->piva}}
                        </p>
                        <p>
                            Tel. {{ $base_settings->telefono }}
                        </p>
                    </div>
                    <div class="col-xs-6 text-right">
                        @if($fe_settings->banca)
                            <p>Banca {{ $fe_settings->banca }}</p>
                        @endif
                        @if($fe_settings->IBAN)
                            <p>IBAN: {{ $fe_settings->IBAN }}</p>
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
