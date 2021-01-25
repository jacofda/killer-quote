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

        .reviews .review-name {
            height: 1.5cm;
            line-height: 1.5cm;
            font-size: 1.2em;
            font-weight: bold;
            width: auto;
            margin-bottom: 3mm;
        }

        .reviews .review-text {
            display: -webkit-box;
            overflow: hidden !important;
        }
    </style>
@endpush
@if(count($settings['recensioni']->value) > 1)
<div class="row reviews mt-5 avoid-page-break">
    <div class="col-xs-12">
        <h3 class="section-title text-center">@lang('killerquote::kq.recensioni')</h3>
        <div class="row">
            @foreach($settings['recensioni']->value as $review)
                <div class="col-xs-4">
                    <div class="bordered-box">
                        <div class="review-stars">
                            @for($i = 0; $i < 5; $i++)
                                <i class="glyphicon glyphicon-star" style="color: #ffc800;"></i>
                            @endfor
                        </div>
                        <div class="review-name">
                            <span>{{ $review['review_name'] }} {{ $review['review_surname'] }}</span>
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
