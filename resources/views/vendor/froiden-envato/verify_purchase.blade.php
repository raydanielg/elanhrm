<!DOCTYPE html>
<html>
<head>
    <title>Worksuite SAAS - Verify Purchase</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-6 max-w-2xl">
        <div class="flex flex-col items-center mb-8">
            <div class="flex items-center">
                <img src="{{ asset('img/worksuite-logo.png') }}" class="h-12 w-auto" alt="Tabletrack Logo"/>
                <h1 class="text-3xl font-bold ml-3 text-gray-800">Worksuite SAAS</h1>
            </div>

        </div>

        <div class="bg-white rounded-lg shadow-md">
            <div class="px-4 py-3 bg-gradient-to-r from-amber-50 to-yellow-50 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L2 7v10l10 5 10-5V7L12 2zm0 2.236L19.09 8 12 11.764 4.91 8 12 4.236zM4 9.618v7.764L11 21v-7.764L4 9.618zm16 0L13 13.382V21l7-3.618V9.618z"/>
                    </svg>
                    Verify Your Purchase

                </h2>
            </div>

            <div class="p-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-amber-50 rounded p-3 flex items-start text-sm">
                        <svg class="w-4 h-4 text-amber-500 mt-1 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                        </svg>
                        <div>
                            <h3 class="font-medium text-amber-900">Current Domain</h3>
                            <p class="text-amber-700">{{ \request()->getHost() }}</p>
                        </div>
                    </div>

                    <div class="bg-amber-50 rounded p-3 flex items-start text-sm">
                        <svg class="w-4 h-4 text-amber-500 mt-1 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/>
                        </svg>
                        <div>
                            <h3 class="font-medium text-amber-900">Product Link</h3>
                            <a href="{{config('froiden_envato.envato_product_url')}}" target="_blank"
                               class="text-amber-600 hover:text-amber-800 underline break-all">
                                {{ config('froiden_envato.envato_product_url') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex items-start space-x-3 bg-amber-50 border border-amber-200 rounded p-3 text-sm">
                    <svg class="w-4 h-4 text-amber-600 mt-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <div>
                        <p class="text-amber-700">
                            <span class="font-medium">Need help finding your purchase code?</span>
                            <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"
                               class="underline ml-1" target="_blank">Click here</a>
                        </p>
                        <p class="text-amber-600 mt-1 text-xs">
                            <svg class="w-3 h-3 inline mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                            </svg>
                            Contact your admin if you're not authorized to verify the purchase
                        </p>
                    </div>
                </div>

                <div id="alert" class="hidden"></div>

                <form id="verify-form" onsubmit="validateCode();return false;" class="space-y-4">
                    {{ csrf_field() }}

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <svg class="w-4 h-4 inline mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12.65 10C11.83 7.67 9.61 6 7 6c-3.31 0-6 2.69-6 6s2.69 6 6 6c2.61 0 4.83-1.67 5.65-4H17v4h4v-4h2v-4H12.65zM7 14c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
                                </svg>
                                Purchase Code / License Code
                            </label>
                            <input type="text" id="purchase_code" name="purchase_code"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                                   placeholder="088abxxx-2831-47e7-xxxx-9d8dd3c7xxx8"/>
                            <span class="text-red-500 text-xs mt-1 hidden" id="purchase_code_error">Purchase code is required</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <svg class="w-4 h-4 inline mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                                Email Address
                            </label>
                            <input type="email" id="email" name="email"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"/>
                            <span class="text-red-500 text-xs mt-1 hidden" id="email_error">Email is required</span>
                        </div>

                        <div class="bg-gray-50 rounded p-3">
                            <div class="flex items-start">
                                <input type="checkbox" id="consent" name="consent" checked
                                       class="mt-1 h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500"/>
                                <label for="consent" class="ml-2 text-xs text-gray-600">
                                    <svg class="w-3 h-3 inline mr-1 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                                    </svg>
                                    I agree to receive important emails related to updates, security patches, promotional offers,
                                    and new features
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" onclick="validateCode();return false;"
                                    class="px-4 py-2 bg-amber-500 text-white text-sm rounded hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 flex items-center">
                                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                </svg>
                                Verify Purchase
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="//envato.froid.works/plugins/froiden-helper/helper.js"></script>

    <script>
        function validateCode() {
            // Reset previous error states
            $('#purchase_code, #email').removeClass('border-red-500');
            $('#purchase_code_error, #email_error').addClass('hidden');

            let isValid = true;

            if (!$('#purchase_code').val()) {
                $('#purchase_code').addClass('border-red-500');
                $('#purchase_code_error').removeClass('hidden');
                isValid = false;
            }

            if (!$('#email').val()) {
                $('#email').addClass('border-red-500');
                $('#email_error').removeClass('hidden');
                isValid = false;
            }

            if (!isValid) {
                return false;
            }

            $.easyAjax({
                type: 'POST',
                url: "{{ route('purchase-verified') }}",
                data: $("#verify-form").serialize(),
                container: "#verify-form",
                messagePosition: "inline",
                success: function (response) {
                    if (response.status === 'success') {
                        showSuccess(response);
                    } else if (response.status === 'fail' && response.data?.server) {
                        showError({responseJSON: {message: response.data.server.message}});
                    }
                },
                error: function(xhr) {
                    showError(xhr);
                }
            });
            return false;
        }

        function showSuccess(response) {
            const successHtml = `
                <div class="bg-green-50 border-l-4 border-green-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm text-green-700">
                                <p class="font-medium mb-1">Purchase code successfully verified!</p>
                                <p>Your purchase code has been validated for ${window.location.hostname}.</p>
                                <p class="mt-2">
                                    <span class="italic">Redirecting to login page...</span>
                                    <br>
                                    <br>
                                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 underline">
                                        Click here if not redirected
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#alert').html(successHtml).removeClass('hidden');
            $('#verify-form').addClass('hidden');

            setTimeout(function() {
                window.location.href = "{{ route('login') }}";
            }, 3000);
        }

        function showError(xhr) {
            const message = xhr.responseJSON?.message || 'An error occurred. Please try again.';
            const errorHtml = `
                <div class="bg-red-50 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm text-red-700" id="error-message"></div>
                        </div>
                    </div>
                </div>
            `;
            $('#alert').html(errorHtml).removeClass('hidden');

            // Create a temporary div to parse the HTML message
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = message;

            // Find any links and add the appropriate classes
            const links = tempDiv.getElementsByTagName('a');
            for(let link of links) {
                link.className = 'text-blue-600 hover:text-blue-800 underline';
            }

            // Set the parsed HTML content
            $('#error-message').html(tempDiv.innerHTML);
        }
    </script>
</body>
</html>
