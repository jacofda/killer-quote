@if($quote->media()->where('mime', 'image')->exists())
    <div class="container avoid-page-break">
        <div class="row">
            <div class="col-xs-12">
                @foreach($quote->media()->where('mime', 'image')->get() as $image)
                    <img src="{{ $image->getOriginalAttribute() }}" class="avoid-page-break" style="max-width: 100%; width: auto;">
                @endforeach
            </div>
        </div>
    </div>
@endif
