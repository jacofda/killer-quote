<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('css/pdf/b3.css')}}">
    <link rel="stylesheet" href="{{asset('css/pdf/pdf.css')}}">
    <style>
        @page {
            margin-top: 4cm;
        }

        .text-color {
            color: {{ $base_settings->default_color }};
        }

        .v-align-middle {
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }

        .full-height {
            height: 100%;
        }

        body {
            color: black;
            font-size: 15px;
            margin-top: 2cm;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-bottom: 2cm;
        }

        footer {
            position: fixed;
            bottom: 2.3cm;
            left: 0cm;
            right: 0cm;
            padding: 5mm 2mm 2mm 0;
            border-top: 2mm solid {{ $base_settings->default_color }};
        }

        footer p {
            margin:0;
            padding:0;
        }

        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3.3cm;
        }

        .section-title {
            font-weight: bold;
            font-size: 1.5em;
            margin-bottom: 0.5cm;
        }

        .avoid-page-break {
            page-break-inside: avoid;
        }

        .page-break {
            page-break-after: always;
        }

        .bordered-box {
            width: 100%;
            padding-left: 3mm;
            padding-right: 3mm;
            text-align: center;
            border: 1mm solid {{ $base_settings->default_color }} !important;
        }

        .bordered-box-text {
            position:relative;
            top:50%;
            -webkit-transform: translateY(-50%);
        }
    </style>
    @stack('style')
</head>
<body>
    @yield('content')
</body>
</html>
