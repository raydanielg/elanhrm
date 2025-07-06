<button class="btn-primary rounded f-14 p-2 commonPaymentButton" type="button" id="card-button"
        data-secret="{{ $intent->client_secret }}">
    <i class="fab fa-cc-stripe mr-1"></i>
    @lang('superadmin.pay')
</button>
