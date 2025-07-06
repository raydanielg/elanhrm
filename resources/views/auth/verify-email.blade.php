@push('styles')
    @foreach ($frontWidgets as $item)
    @if(!is_null($item->header_script))
        {!! $item->header_script !!}
    @endif

    @endforeach
@endpush
<x-auth>

    @push('styles')
    <style>
        .otc {
            position: relative;
            width: 320px;
            margin: 0 auto;
        }

        .otc fieldset {
            border: 0;
            padding: 0;
            margin: 0;
        }

        .otc fieldset div {
            display: flex;
            align-items: center;
        }

        .otc legend {
            margin: 0 auto 1em;
            color: #5555FF;
        }

        input[type="number"] {
            width: 1.2em;
            line-height: 1;
            margin: .1em;
            padding: 8px 0 4px;
            font-size: 2.65em;
            text-align: center;
            appearance: textfield;
            -webkit-appearance: textfield;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* 2 group of 3 items */
        input[type="number"]:nth-child(n+4) {
            order: 2;
        }

        .otc div::before {
            content: '';
            height: 2px;
            width: 15px;
            margin: 0 .25em;
            order: 1;
            background: #cccccc;
        }

        .otc label {
            border: 0 !important;
            clip: rect(1px, 1px, 1px, 1px) !important;
            -webkit-clip-path: inset(50%) !important;
            clip-path: inset(50%) !important;
            height: 1px !important;
            margin: -1px !important;
            overflow: hidden !important;
            padding: 0 !important;
            position: absolute !important;
            width: 1px !important;
            white-space: nowrap !important;
        }
        #verifyEmailLink {
            border: 1px solid #E8EEF3;
            width: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 0px;
            color: #fff;
            display: inline-block;
            padding: 8px 12px;
            background: #28313c;
            border-radius: 5px;
            text-transform: capitalize;
            margin-top: 19px;
        }
    </style>

    @endpush

    <div class="alert alert-success" id="sessionCondition" >
        @php
            $showbtn = true;
            $isClient = session('isClient');
            $companyNeedApprovel = \App\Models\GlobalSetting::value('company_need_approval');
        @endphp

        <h6>
            {{-- employee login using invite || company reg and login --}}
            @if(user()->roles->first()->name == 'employee')
                @lang('superadmin.clientSignUpApprovalMailVerificationPending')
            @elseif(!$isClient && session('user')->admin_approval === 0 && session('company')->approved === 0)
                @lang('superadmin.approvalPending')
            @elseif (!$isClient && session('user')->admin_approval === 1 && session('company')->approved === 1)
                @if($companyNeedApprovel == 0)
                    @lang('superadmin.clientSignUpApprovalMailVerificationPending')
                @else
                    @lang('superadmin.approval')
                @endif
            @endif

            {{-- client login --}}
            @if ($isClient && session('admin_approval') == 0 && session('admin_client_signup_approval') == 1)
                @php $showbtn = false; @endphp
                @lang('superadmin.clientSignUpApprovalPending')
            @elseif (
                $isClient && 
                    (
                        session('admin_approval') == 1 && session('admin_client_signup_approval') == 0 ||
                        session('admin_approval') == 1 && session('admin_client_signup_approval') == 1 ||
                        session('admin_approval') == 0 && session('admin_client_signup_approval') == 0
                    )
                )
                @php $showbtn = true; @endphp
                @lang('superadmin.clientSignUpApprovedMailVerificationPending')
            @endif
        </h6>

        @if($showbtn)
            <button type="submit"
            class="btn-primary f-w-500 rounded w-50 f-18"
                href="verify"
            id="verifyEmailLink"
            >@lang('superadmin.superadmin.verifyMail')</button>
        @endif
    </div>

    <div class="container"id="verificationContainer" style="display: none;">
        <div class="d-flex justify-content-between mb-4">
            <h5 class="heading-h5">Hi  {{ user()->name }} !!</h5>
            <button type="button" class="btn-light btn btn-sm rounded f-12" onclick="event.preventDefault();
            document.getElementById('logout-form').submit();"><i class="fa fa-power-off f-16 mr-1"></i>
            {{__('app.logout')}}</button>
        </div>

        <div class="card border-0">
            <div class="card-header bg-white border-0">
                <h3 class="heading-h3 mb-0">@lang('superadmin.emailVerificationCode.enterVerificationCode')</h3>
            </div>

            <div class="card-body">
                <div class="mb-4 font-medium text-sm text-success d-none" id="email-code-sent-message">
                    @lang('superadmin.emailVerificationCode.newEmailCodeSent')
                </div>

                <form class="otc my-3 d-inline ajax-form" id="email-verification-form" name="one-time-code" action="#">
                    @csrf
                    <fieldset class="form-group">
                        <label for="otc-1">Number 1</label>
                        <label for="otc-2">Number 2</label>
                        <label for="otc-3">Number 3</label>
                        <label for="otc-4">Number 4</label>
                        <label for="otc-5">Number 5</label>
                        <label for="otc-6">Number 6</label>

                        <span class="text-dark-grey">@lang('superadmin.emailVerificationCode.enterVerificationCodeEmail')</span>

                        <div>

                            <input type="number" name="email_otp[]" pattern="[0-9]*" min="0" max="9" maxlength="1" value="" inputtype="numeric" autocomplete="one-time-code"
                                id="otc-1" required class="rounded border">

                            <!-- Autocomplete not to put on other input -->
                            <input type="number" name="email_otp[]" pattern="[0-9]*" min="0" max="9" maxlength="1" value="" inputtype="numeric"
                                id="otc-2" required class="rounded border">
                            <input type="number" name="email_otp[]" pattern="[0-9]*" min="0" max="9" maxlength="1" value="" inputtype="numeric"
                                id="otc-3" required class="rounded border">
                            <input type="number" name="email_otp[]" pattern="[0-9]*" min="0" max="9" maxlength="1" value="" inputtype="numeric"
                                id="otc-4" required class="rounded border">
                            <input type="number" name="email_otp[]" pattern="[0-9]*" min="0" max="9" maxlength="1" value="" inputtype="numeric"
                                id="otc-5" required class="rounded border">
                            <input type="number" name="email_otp[]" pattern="[0-9]*" min="0" max="9" maxlength="1" value="" inputtype="numeric"
                                id="otc-6" required class="rounded border">
                        </div>

                        <button type="button" onclick="return verifyCode()" id="verify-code" class="btn-primary rounded f-14 p-2 mt-3 align-baseline">
                            @lang('superadmin.emailVerificationCode.verifyCode')
                        </button>

                        {{-- SAAS --}}
                        {{-- @if(session('impersonate') && isWorksuiteSaas())
                        <x-forms.link-primary icon="stop" data-toggle="tooltip"
                            data-original-title="{{ __('superadmin.stopImpersonationTooltip') }}" data-placement="left"
                            :link="route('superadmin.superadmin.stop_impersonate')"
                            class="btn-primary rounded f-14 p-2 mt-3 align-baseline mr-5">
                            @lang('superadmin.stopImpersonation')
                        </x-forms.link-primary>
                        @endif --}}


                    </fieldset>
                </form>

                <form method="POST" action="#" id="send-email-verification-code">
                    @csrf
                    <button type="button" onclick="return sendVerifyCode()" id="send-verify-code" class="btn-light rounded f-11 p-2 mt-3 align-baseline">
                        @lang('superadmin.emailVerificationCode.resendVerifyCode')
                    </button>
                </form>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

            </div>
        </div>
    </div>


    <x-slot name="scripts">
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const verifyEmailLink = document.getElementById('verifyEmailLink');
                const verificationContainer = document.getElementById('verificationContainer');
                const sessionCondition = document.getElementById('sessionCondition');

                verifyEmailLink.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent the default link behavior

                    // Make the container visible
                    verificationContainer.style.display = 'block';

                    // Hide session condition
                    if (sessionCondition) {
                        sessionCondition.style.display = 'none';
                    }

                    // Send a verification code email (assuming you have a backend API for this)
                    sendVerificationEmail();
                });

                // Function to send verification email (replace this with your backend implementation)
                function sendVerificationEmail() {
                    // Make an AJAX request to your backend server to send the email
                    // Example using Fetch API
                    fetch('/send-verification-email', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            // Include any necessary data for sending the email
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to send verification email');
                        }
                        console.log('Verification email sent successfully');
                    })
                    .catch(error => {
                        console.error('Error sending verification email:', error.message);
                    });
                }
            });
        </script>
       <script>
            let in1 = document.getElementById('otc-1'),
            ins = document.querySelectorAll('input[type="number"]'),
        	 splitNumber = function(e) {
        		let data = e.data || e.target.value; // Chrome doesn't get the e.data, it's always empty, fallback to value then.
        		if ( ! data ) return; // Shouldn't happen, just in case.
        		if ( data.length === 1 ) return; // Here is a normal behavior, not a paste action.

        		popuNext(e.target, data);

                verifyCode();
        		//for (i = 0; i < data.length; i++ ) { ins[i].value = data[i]; }
        	},
        	popuNext = function(el, data) {
        		el.value = data[0]; // Apply first item to first input
        		data = data.substring(1); // remove the first char.
        		if ( el.nextElementSibling && data.length ) {
        			// Do the same with the next element and next data
        			popuNext(el.nextElementSibling, data);
        		}
        	};

            ins.forEach(function(input) {
                /**
                 * Control on keyup to catch what the user intent to do.
                 * I could have check for numeric key only here, but I didn't.
                 */
                input.addEventListener('keyup', function(e){
                    // Break if Shift, Tab, CMD, Option, Control.
                    if (e.keyCode === 16 || e.keyCode == 9 || e.keyCode == 224 || e.keyCode == 18 || e.keyCode == 17) {
                        return;
                    }

                    // On Backspace or left arrow, go to the previous field.
                    if ( (e.keyCode === 8 || e.keyCode === 37) && this.previousElementSibling && this.previousElementSibling.tagName === "INPUT" ) {
                        this.previousElementSibling.select();
                    } else if (e.keyCode !== 8 && this.nextElementSibling) {
                        this.nextElementSibling.select();
                    }

                    // If the target is populated to quickly, value length can be > 1
                    if ( e.target.value.length > 1 ) {
                        splitNumber(e);
                    }

                    if (event.target.id == 'otc-6') {
                        verifyCode();
                    }
                });

                /**
                 * Better control on Focus
                 * - don't allow focus on other field if the first one is empty
                 * - don't allow focus on field if the previous one if empty (debatable)
                 * - get the focus on the first empty field
                 */
                input.addEventListener('focus', function(e) {
                    // If the focus element is the first one, do nothing
                    if ( this === in1 ) return;

                    // If value of input 1 is empty, focus it.
                    if ( in1.value == '' ) {
                        in1.focus();
                    }

                    // If value of a previous input is empty, focus it.
                    // To remove if you don't wanna force user respecting the fields order.
                    if ( this.previousElementSibling.value == '' ) {
                        this.previousElementSibling.focus();
                    }
                });
            });

            /**
             * Handle copy/paste of a big number.
             * It catches the value pasted on the first field and spread it into the inputs.
             */
            in1.addEventListener('input', splitNumber);
        </script>

        <script>

            function handleFormSubmit(e) {
                e.preventDefault();
            }

            function sendVerifyCode() {
                event.preventDefault();
                document.addEventListener('click', handleFormSubmit, false);

                const url = "{{ route('verification.send') }}";
                $.easyAjax({
                    url: url,
                    container: 'body',
                    disableButton: true,
                    buttonSelector: "#send-verify-code",
                    type: "POST",
                    messagePosition: "inline",
                    blockUI: true,
                    data: $('#send-email-verification-code').serialize(),
                    success: function (response) {
                        $('#email-code-sent-message').removeClass('d-none');
                    }
                })
            }

            function verifyCode() {
                event.preventDefault();
                document.addEventListener('click', handleFormSubmit, false);

                const url = "{{ route('superadmin.signup.verifyEmail') }}";
                $.easyAjax({
                    url: url,
                    container: '#email-verification-form',
                    disableButton: true,
                    buttonSelector: "#verify-code",
                    type: "POST",
                    messagePosition: "inline",
                    data: $('#email-verification-form').serialize(),
                    success: function (response) {
                        if (response.status === 'success') {
                            document.removeEventListener('click', handleFormSubmit);
                        } else if (response.status == 'fail') {
                            document.removeEventListener('click', handleFormSubmit);
                            $('#email-verification-form')[0].reset();
                            $('#otc-1').focus();
                        }
                    }
                })
            }
        </script>

        @foreach ($frontWidgets as $item)
        @if(!is_null($item->footer_script))
            {!! $item->footer_script !!}
        @endif

        @endforeach
    </x-slot>

</x-auth>
