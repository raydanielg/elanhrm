<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-20">
    <input type="hidden" name="language_setting_id" value="{{ $lang->id }}">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group my-3">
                <x-forms.label fieldId="message" :fieldLabel="__('app.message').$lang->label">
                </x-forms.label>
                <div id="message">{!!  $signUpSetting->message ?? '' !!}</div>
                <textarea name="message" id="message_text" class="d-none"></textarea>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        quillImageLoad('#message');

    });
</script>
