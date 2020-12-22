<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="{{asset('css/pdf/b3.css')}}">
    <style>
        .text-color {
            color: {{ $base_settings->default_color }};
        }
        .header-bar {
            padding: 5mm 2mm 0 0;
            border-bottom: 1mm solid {{ $base_settings->default_color }};
        }
        header p {
            margin:0;
            padding:0;
        }

        .header-height {
            height: 2cm;
            padding-bottom:0.3cm;
        }

        .max-height {
            max-height: 99%;
        }
    </style>
</head>
<body>
<header>
    @if(Areaseb\Core\Models\Setting::DefaultLogo())
        <div class="container">
            <div class="row header-bar">
                <div class="col-xs-4 text-center header-height ">
                    <img class="img-responsive max-height" style="margin:auto;" src="{{Areaseb\Core\Models\Setting::FatturaLogo()}}">
                </div>
                <div class="col-xs-8 text-right header-height" style="padding-top:10px;">
                    <span class="text-color" style="font-weight:bolder; font-size:16pt;text-transform:uppercase;">{{ $settings['payoff']->value }}</span>
                </div>
            </div>
        </div>
    @endif
</header>
</body>
</html>




{{-- <!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="{{asset('css/pdf/b3.css')}}">
        <style>
            .text-color {
                color: {{ $base_settings->default_color }};
            }

            .full-height {
                height: 100%;
            }
            header {
                height: 3.3cm;
            }
            .v-align-middle {
                position: relative;
                top: 50%;
                -webkit-transform: translateY(-50%);
            }
        </style>
    </head>
    <body>
        <header>
            <div class="container full-height">
                <div class="row full-height">
                    <div class="col-xs-5 full-height">
                        <img src="{{ $settings['logo']->media()->first()->getDisplayAttribute() }}" style="height: 100%; width: auto;" />
                    </div>
                    <div class="col-xs-7 full-height">
                        <div class="v-align-middle">
                            <h2 class="text-color" style="margin: auto; font-weight: bold;">
                                {{ $settings['payoff']->value }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    </body>
</html>
 --}}
