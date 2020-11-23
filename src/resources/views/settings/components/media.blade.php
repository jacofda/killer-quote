<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Carica File</h3>
            </div>
            <div class="card-body">
                <div class="dropzone" id="dropzoneForm">
                    <div class="row">
                        <div class="fallback">
                            <input name="file" type="file"  />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{asset('js/dropzone5-7-0.min.js')}}"></script>
    <script>
        (function(){
            let imageInput = "<input type='hidden' name='logo' id='logo-input' />";
            let text = "<strong>Clicca per caricare immagini e documenti. (jpg, png, pdf, doc, xls, ...)</strong><br> Max upload size 8MB";

            Dropzone.options.dropzoneForm = {
                url: "{{ url('killerquotes/settings/upload_logo') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                addRemoveLinks: true,
                paramName: "file",
                maxFilesize: 200,
                dictDefaultMessage: text,
                success: function(_, response) {
                    // The response represents the ID of the media
                    appendImageIdInput(response);
                },
                sending: function(file, xhr, formData) {
                    formData.append("mediable_id", "{{$model->id}}");
                    formData.append("mediable_type", "{{str_replace("\\","\\\\", $model->full_class)}}");
                    formData.append("directory", "{{$model->directory}}");
                },
                init: function() {
                    // Already existing file
                    @if($media = \KillerQuote\Src\App\Models\KillerQuoteSetting::getMediaById($model->value))
                        let mockFile = {
                            name: "{{$media->filename}}",
                            size: {{$media->size*1024}}
                        };
                        this.displayExistingFile(mockFile, "{{$media->getDisplayAttribute()}}");
                        this.files.push(mockFile);
                    @endif

                    // Allow only one file
                    this.on('addedfile', function(file) {
                        if (this.files.length > 1) {
                            this.removeFile(this.files[0]);
                        }
                    });
                }
            };

            function appendImageIdInput(id) {
                $imageEl = $('#logo-input')
                if(!$imageEl)
                    $imageEl = $(imageInput)

                $imageEl.val(id);
                $('#killerquotes-settings-form').prepend($imageEl);
            }
        })(jQuery);
    </script>
@endpush

