@foreach($quote->items as $item)
    @php
        $images = $item->product->media()->where('mime','image')->orderBy('media_order', 'ASC')->take(3)->get();
    @endphp

    @if(count($images))
        <div class="container-fluid avoid-page-break">
            <div class="row">
                <div class="col-xs-12">
                    <h3 class="section-title">{{ $item->product->name }}</h3>
                </div>
            </div>
            <div class="row">
                @foreach($images as $i => $image)
                    <div class="{{ $i === 0 ? 'col-xs-12' : 'col-xs-6' }} avoid-page-break" style="margin-bottom: 2mm; padding-left: 2mm; padding-right: 2mm;">
                        <img src="{{ $image->getFullAttribute() }}" style="width: 100%; height: auto;" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endforeach
