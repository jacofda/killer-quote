@extends('killerquote::pdf.layout')

@section('content')
    @include('killerquote::pdf.pages.chi_siamo')
    @include('killerquote::pdf.pages.perche_sceglierci')
    <div class="page-break"></div>
    @include('killerquote::pdf.pages.summary')
    <div class="page-break"></div>
    @include('killerquote::pdf.pages.items_images')
    <div class="page-break"></div>
    @include('killerquote::pdf.pages.garanzie')
    @include('killerquote::pdf.pages.bonus')
    <div class="page-break"></div>
    @include('killerquote::pdf.pages.payment')
    <div class="page-break"></div>
    @include('killerquote::pdf.pages.firma')
    <div class="page-break"></div>
    @include('killerquote::pdf.pages.reviews')
    @include('killerquote::pdf.pages.glossario')
    @include('killerquote::pdf.pages.images')
    <div class="page-break"></div>
    @include('killerquote::pdf.pages.cond_vendita')
    <div class="page-break"></div>
    @include('killerquote::pdf.pages.privacy')

@endsection
