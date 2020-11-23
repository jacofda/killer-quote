@extends('jacofda::layouts.app')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{route('killerquotes.index')}}">Preventivi Killer</a></li>
@stop

@include('jacofda::layouts.elements.title', ['title' => 'Modifica Preventivo N. '.$quote->id])

@section('css')
    <link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/dropzone/min.css')}}">
    <style>
        body.ajaxfileupload-overlay {
            position: relative;
            height: 100%;
        }

        .ajaxfileupload-panel {
            z-index: 1000;
            border: solid 1px #999;
            transform: translate(-50%, -50%);
        }

        #ajaxFileUploadInner {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 20px;
        }

        #ajaxPanelClose {
            font-weight: bold;
            font-size: 30px;
            transform: rotate(45deg);
            cursor: pointer;
            position: absolute;
            top: 0px;
            right: 10px;
        }
        .ajaxfileupload-panel h4 {
            margin-bottom: 0;
        }
        #ajaxFileUploadSubmit {
            padding: 3px 15px;
            background-color: #232848;
            color: #fff;
            font-weight: bold;
            margin-top: 10px;
            cursor: pointer;
            max-width: 50%;
            text-transform: uppercase;
            text-align: center;
            display: inline-block;
            border-radius: 5px;
        }

        body.ajaxfileupload-overlay:after {
            content: "";
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 10;
            background-color: rgba(0,0,0,0.2);
        }
    </style>
@endsection

@section('content')
    {!! Form::open(['url' => route('killerquotes.update', $quote->id), 'method' => 'PATCH', 'autocomplete' => 'off', 'id' => 'killerQuoteForm', 'class' => 'form-horizontal']) !!}
    <div class="row">
        @include('jacofda::components.errors')
        @include('killerquote::quotes.quote.components.form')
        @include('killerquote::quotes.quote.components.summary')
        @include('killerquote::quotes.quote.components.product_form')
        @include('killerquote::quotes.quote.components.products')
    </div>
    {!! Form::close() !!}
@stop

