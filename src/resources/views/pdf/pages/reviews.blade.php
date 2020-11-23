@push('style')
    <style>
        .reviews {
            margin-bottom: 1cm;
        }

        .reviews .bordered-box {
            height: 7cm;
            padding-top: 5mm;
            padding-bottom: 5mm;
        }

        .reviews .review-stars * {
            display: inline-block;
            font-size: 22px;
        }

        .reviews .review-stars, .reviews .review-image {
            margin-bottom: 3mm;
        }

        .reviews .review-image img {
            height: 1.5cm;
            width: auto;
            margin-bottom: 3mm;
        }

        .reviews .review-text {
            max-height: 105px;
            display: -webkit-box;
            overflow: hidden !important;
            text-overflow: ellipsis;
            -webkit-line-clamp: 5;
            -webkit-box-orient: vertical;
        }
    </style>
@endpush
@if(count($settings['recensioni']->value))
<div class="row reviews avoid-page-break">
    <div class="col-xs-12">
        <h3 class="section-title text-center">DICONO DI NOI</h3>
        <div class="row">
            @foreach($settings['recensioni']->value as $review)
                <div class="col-xs-4">
                    <div class="bordered-box">
                        <div class="review-stars">
                            @for($i = 0; $i < 5; $i++)
                                <i class="glyphicon glyphicon-star" style="color: #ffc800;"></i>
                            @endfor
                        </div>
                        <div class="review-image">
                            <img src="{{ \Jacofda\Core\Models\Media::find($review['review_img'])->getDisplayAttribute() }}" />
                        </div>
                        <div class="review-text">
                            {{ $review['review_txt'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
