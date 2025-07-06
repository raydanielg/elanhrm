@extends('vendor.installer.layouts.master')

@section('title', trans('installer_messages.requirements.title'))
@section('container')
    <ul class="list">
        <li class="list__item {{ $phpSupportInfo['supported'] ? 'success' : 'error' }}">PHP Version >=
            {{ $phpSupportInfo['minimum'] }} <i
                class="fa fa-fw fa-{{ $phpSupportInfo['supported'] ? 'check-circle-o' : 'exclamation-circle' }} row-icon"
                aria-hidden="true"></i>
            @if(!$phpSupportInfo['supported'])
                <div class="error-message">
                    Your PHP version ({{ $phpSupportInfo['current'] ?? 'Unknown' }}) is not supported. Please upgrade to PHP {{ $phpSupportInfo['minimum'] }} or higher.
                </div>
            @endif
        </li>

        @foreach ($requirements['requirements'] as $extention => $enabled)
            <li class="list__item {{ $enabled ? 'success' : 'error' }}">{{ $extention }} <i
                    class="fa fa-fw fa-{{ $enabled ? 'check-circle-o' : 'exclamation-circle' }} row-icon"
                    aria-hidden="true"></i>
                @if(!$enabled)
                    <div class="error-message">
                        The {{ $extention }} PHP extension is required but not installed. Please install it before continuing.
                    </div>
                @endif
            </li>
        @endforeach
    </ul>

    @php
        $hasErrors = !$phpSupportInfo['supported'] || isset($requirements['errors']);
    @endphp

    <div class="status-message">
        @if ($hasErrors)
            <div class="error-summary">
                <i class="fa fa-exclamation-triangle"></i>
                Some requirements failed to meet the minimum specifications. Please fix the issues before proceeding.
            </div>
        @else
            <div class="success-summary">
                <i class="fa fa-check-circle"></i>
                All system requirements are met! You can proceed to the next step.
            </div>
        @endif
    </div>

    <div class="buttons">
        @if (!$hasErrors)
            <a class="button" href="{{ route('LaravelInstaller::permissions') }}" id="next-button">
                {{ trans('installer_messages.next') }}
            </a>
        @else
            <button class="button disabled" disabled>
                {{ trans('installer_messages.next') }}
            </button>
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
                    // Add loading state
                    this.classList.add('loading');

                    // Change button text to show loading state
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner"></span> Loading...';

                    // Prevent default action temporarily
                    e.preventDefault();

                    // Navigate after a short delay to show loading state
                    setTimeout(function() {
                        window.location.href = nextButton.getAttribute('href');
                    }, 500);
                });
            }
        });
    </script>
    <style>
        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            margin-left: 25px;
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

        .button.disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .button.loading {
            position: relative;
            pointer-events: none;
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
