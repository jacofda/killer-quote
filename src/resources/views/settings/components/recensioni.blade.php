@php
    $reviews = $model->value;
    if(!$reviews)
        $reviews = [
            ['review_name' => '', 'review_surname' => '', 'review_txt' => '']
        ];
@endphp
<div class="row">
    <div class="col-12 mb-3" id="reviews-txt">
        @foreach($reviews as $review)
            <div class="review_txt_div mb-4">
                <div class="row mb-2">
                    <div class="col-12 col-sm-6">
                        <input type="text" class="form-control" name="review[review_name][]" placeholder="Nome" value="{{ !empty($review['review_name']) ? $review['review_name'] : 'Carlo' }}" />
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="text" class="form-control" name="review[review_surname][]" placeholder="Surname" value="{{ !empty($review['review_surname']) ? $review['review_surname'] : 'Rossi' }}" />
                    </div>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" name="review[review_txt][]" placeholder="Recensione (max. 120 caratteri)" value="{{ !empty($review['review_txt']) ? $review['review_txt'] : 'Congraturazioni' }}" />
                    <div class="input-group-append">
                        <button class="btn btn-danger" data-action="delete"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="col-12">
        <button class="btn btn-md btn-block btn-success" id="recensione-add">Aggiungi</button>
    </div>
</div>
@push('scripts')
    <script>
        (function(){
            let reviewInput = `
                <div class="review_txt_div mb-4">
                    <div class="row mb-2">
                        <div class="col-12 col-sm-6">
                            <input type="text" class="form-control" name="review[review_name][]" placeholder="Nome" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <input type="text" class="form-control" name="review[review_surname][]" placeholder="Surname" />
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="text" name="review[review_txt][]" placeholder="Recensione (max. 120 caratteri)" class="form-control" />
                        <div class="input-group-append">
                            <button class="btn btn-danger" data-action="delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            `;

            function appendReviewInput(e) {
                e.preventDefault();
                $reviewInput = $(reviewInput);
                $reviewInput.find('button[data-action="delete"]').on('click', removeReviewInput);
                $('#reviews-txt').append($reviewInput);
            }

            function removeReviewInput(e) {
                if($('.review_txt_div').length <= 1)
                    return false;
                e.preventDefault();
                $(e.target).closest('.review_txt_div').remove();
            }

            $('#recensione-add').on('click', appendReviewInput);
            $('.review_txt_div button[data-action="delete"]').on('click', removeReviewInput);
        })(jQuery);
    </script>
@endpush
