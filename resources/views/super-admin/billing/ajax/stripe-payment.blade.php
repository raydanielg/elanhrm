@csrf
<input type="hidden" id="name" name="name" value="{{ user()->name }}">
<input type="hidden" id="stripeEmail" name="stripeEmail" value="{{ user()->email }}">
<input type="hidden" name="plan_id" value="{{ $package->id }}">
<input type="hidden" name="type" value="{{ $type }}">
<div class="col-lg-12 col-md-12">
    <div id="card-error" class="text-red text-bold mt-2 text-sm font-medium text-center mb-2"></div>
</div>

<div class="col-lg-12 col-md-12">
    <label for="card-element" class="font-bold"> @lang('modules.invoices.cardInfo') </label>
</div>
<div class="col-lg-12 col-md-12">
    <div id="card-element"
         class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></div>
</div>

@if($stripeSettings->stripe_status == 'active')
    <script>
        var clientDetails = {!! json_encode($customerDetail) !!};
        var stripe = Stripe('{{ config("cashier.key") }}');
        var elements = stripe.elements();

        var cardButton = document.getElementById('card-button');

        function isDarkTheme() {
            return document.body.classList.contains('dark-theme');
        }

        if (isDarkTheme()) {
            var color = "#99A5B5";
            var placeholderColor = "#99A5B5";
        } else {
            var color = "#32325d";
            var placeholderColor = "#32325d";
        }

        var style = {
            base: {
                color: color,
                fontFamily: 'Arial, sans-serif',
                fontSmoothing: "antialiased",
                fontSize: "16px",
                "::placeholder": {
                    color: placeholderColor
                }
            },
            invalid: {
                fontFamily: 'Arial, sans-serif',
                color: "#fa755a",
                iconColor: "#fa755a"
            }
        };

        var cardElement = elements.create("card", {style: style});
        // Stripe injects an iframe into the DOM
        cardElement.mount("#card-element");


        // console.log(cardButton);
        var clientSecret = cardButton.dataset.secret;
        console.log(clientSecret);
        var validCard = false;
        var cardError = document.getElementById('card-error');

        cardElement.addEventListener('change', function (event) {

            // Disable the Pay button if there are no card details in the Element

            if (event.error) {
                validCard = false;
                cardButton.disabled = true;
                cardError.textContent = event.error.message;
            } else {
                validCard = true;
                cardButton.disabled = false;
                cardError.textContent = '';
            }
        });
        var form = document.getElementById('stripe-payment-form');
        const button = $('#card-button');

        cardButton.addEventListener('click', async (e) => {
            e.preventDefault();
            cardButton.disabled = true;


            var {setupIntent, error} = await stripe.confirmCardSetup(
                clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: clientDetails.name,
                            email: clientDetails.email,
                            address: {
                                line1: clientDetails.line1,
                                city: clientDetails.city,
                                state: clientDetails.state,
                                country: clientDetails.country
                            }
                        }
                    }
                }
            );

            if (error) {

                cardButton.disabled = false;
                // console.log('error'+error);
                // Display "error.message" to the user...
                $('#card-error').text(error.message);

            } else {

                const button = $('#card-button');
                const text = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{__('app.loading')}}';
                button.html(text);

                // The card has been verified successfully...
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'payment_method');
                hiddenInput.setAttribute('value', setupIntent.payment_method);
                form.appendChild(hiddenInput);

                document.getElementById('stripe-payment-form').submit();
            }
        });

    </script>
@endif
