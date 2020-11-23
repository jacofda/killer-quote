@php
    $reviews = [];
    $v = $model->value;
    foreach($v as $review) {
        $media = \KillerQuote\Src\App\Models\KillerQuoteSetting::getMediaById($review['review_img']);
        if(!$media) continue;
        $review['media'] = $media;
        $reviews[] = $review;
    }
@endphp
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Carica File</h3>
            </div>
            <div class="card-body">
                <div class="dropzone" id="reviewsDropzoneForm">
                    <div class="row">
                        <div class="fallback">
                            <input name="file" type="file"  />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div id="reviews-img-inputs">
    </div>
    <div class="col-12" id="reviews-txt">
        @foreach($reviews as $review)
            <div data-image-id="{{ $review['review_img'] }}" class="review_txt_div mb-3">
                <input type="text" class="form-control" name="recensione_txt[]" placeholder="Recensione (max. 120 caratteri)" value="{{ $review['review_txt'] }}" />
            </div>
        @endforeach
    </div>
</div>
@push('scripts')
    <script>
        (function(){
            let reviewInput = `
                <div class="review_txt_div mb-3">
                    <input type="text" name="recensione_txt[]" placeholder="Recensione (max. 120 caratteri)" class="form-control" />
                </div>
            `;
            let text = "<strong>Clicca per caricare immagini e documenti. (jpg, png, pdf, doc, xls, ...)</strong><br> Max upload size 8MB";

            Dropzone.options.reviewsDropzoneForm = {
                url: "{{ url('killerquotes/settings/upload_review_image') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                addRemoveLinks: true,
                paramName: "file",
                maxFilesize: 200,
                dictDefaultMessage: text,
                success: function(file, response) {
                    // The response represents the ID of the media
                    file.serverId = Number(response);
                    appendReviewInput(response);
                },
                sending: function(file, xhr, formData) {
                    formData.append("mediable_id", "{{$model->id}}");
                    formData.append("mediable_type", "{{str_replace("\\","\\\\", $model->full_class)}}");
                    formData.append("directory", "{{$model->directory}}");
                },
                init: function() {
                    // Load existing files
                    let mockFile = {};
                    @foreach($reviews as $review)
                        mockFile = {
                            name: "{{$review['media']->filename}}",
                            size: {{$review['media']->size*1024}},
                            serverId: {{ $review['media']->id }}
                        };
                        this.displayExistingFile(mockFile, "{{$review['media']->getDisplayAttribute()}}");
                        this.files.push(mockFile);
                    @endforeach

                    this.on('removedfile', function(file) {
                        let url = "{{ url('killerquotes/settings/delete_review_image') }}"+"/"+file.serverId;
                        $.post({
                            url: url,
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                        })
                            .always(function() {
                                removeReviewInput(file.serverId);
                            });
                    });
                }
            };

            function appendReviewInput(imageId) {
                $review = $(reviewInput);
                $review.attr('data-image-id', imageId);
                $('#reviews-txt').append($review);
            }

            function removeReviewInput(imageId) {
                $('#reviews-txt [data-image-id="'+imageId+'"]').remove();
            }
        })(jQuery);
    </script>
@endpush

