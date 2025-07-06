@extends('vendor.installer.layouts.master')


@section('style')
    <style>
        .button.disabled {
            pointer-events: none;
            cursor: not-allowed;
            background: #c2c2c2;
        }
        .hide{
            display: none;
        }
        .status-message {
            margin: 20px 0;
            padding: 15px;
            border-radius: 4px;
        }
        .error-summary {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }
        .success-summary {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
            vertical-align: middle;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
@endsection

@section('title', trans('installer_messages.permissions.title'))
@section('container')
    <p class="text-center mb-4">The installer needs to check if the following folders have the correct permissions.</p>


    <ul class="list">
        @foreach ($permissions['permissions'] as $permission)
            <li class="list__item list__item--permissions {{ $permission['isSet'] ? 'success' : 'error' }}">
                {{ $permission['folder'] }}
                <span>
                    <i class="fa fa-fw fa-{{ $permission['isSet'] ? 'check-circle-o' : 'exclamation-circle' }}"></i>
                    {{ $permission['permission'] }}
                </span>
            </li>
        @endforeach
    </ul>

    <div class="status-message">
        @if (isset($permissions['errors']))
            <div class="error-summary">
                <i class="fa fa-exclamation-triangle"></i>
                Some folders don't have the required permissions. Please fix the issues before proceeding.
            </div>
        @else
            <div class="success-summary">
                <i class="fa fa-check-circle"></i>
                All folder permissions are correct! You can proceed to the next step.
            </div>
        @endif
    </div>

    @if (isset($permissions['errors']))
        <div class="terminal-command">
            <h5>Terminal Command</h5>
            <span>If you have terminal access, run the following command on terminal</span>
            <p style="background: #f7f7f9;padding: 10px; border-radius: 4px; font-family: monospace;">
                chmod -R 775 storage/app/ storage/framework/ storage/logs/ bootstrap/cache/
            </p>
        </div>
    @endif

    <div class="buttons">
        <ul class="hide" id="messageWait">
            <ol>Please wait a few moments as the application prepares for you. This may take a minute or two depending on your server configuration.</ol>
        </ul>
        @if (!isset($permissions['errors']))
            <a class="button" href="{{ route('LaravelInstaller::database') }}" id="next-button">
                {{ trans('installer_messages.next') }}
            </a>
        @else
            <a class="button" href="javascript:window.location.href='';">
                {{ trans('installer_messages.checkPermissionAgain') }}
            </a>
        @endif
    </div>

@stop

@section('scripts')
    <script src="{{ asset('installer/js/jQuery-2.2.0.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nextButton = document.getElementById('next-button');

            if (nextButton) {
                nextButton.addEventListener('click', function(e) {
                    // Add loading class to button
                    this.classList.add('disabled');

                    // Change button text to show loading state
                    this.innerHTML = '<span class="spinner"></span> Processing...';

                    // Show wait message
                    document.getElementById('messageWait').classList.remove('hide');

                    // Prevent default action temporarily to show loading state
                    e.preventDefault();

                    // Navigate after a short delay to show the loading state
                    setTimeout(function() {
                        window.location.href = nextButton.getAttribute('href');
                    }, 500);
                });
            }

            $('.button').not('#next-button').click(function () {
                const button = $(this);
                const text = '<span class="spinner"></span> Checking...';

                $(button).addClass('disabled');
                $('#messageWait').show();
                button.html(text);
            });
        });
    </script>
@endsection