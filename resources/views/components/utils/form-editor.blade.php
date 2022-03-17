@props([
    'name' => '',
    'id' => null,
    'value' => '',
    'label'=>'',
    'placeholder' => '',
    'options'=>[],
])

<div class="form-group row mb-3">
    <label for="{{$name}}-editor" class="col-md-2 col-form-label">@lang($label)</label>

    <div class="col-md-10">
        <textarea id="{{$id ?? $name}}-editor"
                  name="{{$name}}"
                  rows="20"
                  placeholder="{{ !empty($placeholder) ? $placeholder : $label}}"
                      {{ $attributes->merge(['class' => 'form-control text-editor']) }}
        >{!! $value !!}</textarea>

        @error($name)
        <div class="invalid-feedback">
            {{$message}}
        </div>
        @enderror

    </div>
</div><!--form-group-->

@push('after-scripts')
    <script>
        tinymce.init({
            selector: '#{{$id ?? $name}}-editor',
            language: 'ru',
            fontsize_formats: "8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 20px 22px 24px 26px 28px 30px 32px 34px 36px 38px 40px 42px",
            plugins: [
                'advlist autolink link image lists charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                'table emoticons template paste help'
            ],
            toolbar: 'undo redo | styleselect| fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | link image | preview media fullpage | ' +
                'forecolor backcolor emoticons | help',
            menubar: 'favs file edit view insert format tools table help',
            images_upload_handler: function (blobInfo, success, failure) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;

                xhr.open('POST', '{{route('admin.editor.upload')}}');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.head.querySelector('meta[name="csrf-token"]').content);

                xhr.onload = function () {
                    var json;

                    if (xhr.status != 200) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }
                    json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location != 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    success(json.location);
                };
                formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            }
            //content_css: 'css/content.css'
        });
    </script>
@endpush
