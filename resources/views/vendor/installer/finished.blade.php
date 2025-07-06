@extends('vendor.installer.layouts.master')

@section('style')
    <style>
        .finished-container {
            text-align: center;
            padding: 30px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
        }

        .error-icon {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .message-text {
            font-size: 18px;
            margin-bottom: 25px;
            padding: 15px;
            border-radius: 4px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }

        .button {
            display: inline-block;
            transition: all 0.3s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

@section('title', trans('installer_messages.final.title'))
@section('container')
    <div class="finished-container">
        @if(session()->has('message') && session('message')['status'] !== 'success')
            <i class="fa fa-times-circle error-icon"></i>
            <div class="message-text error-message">
                {{ session('message')['message'] }}
            </div>
        @else
            <i class="fa fa-check-circle success-icon"></i>
            <div class="message-text success-message">
                {{ session()->has('message') ? session('message')['message'] : trans('installer_messages.final.finished') }}
            </div>
        @endif

        <div class="buttons">
            <a href="{{ url('/') }}" class="button" id="exit-button">
                {{ trans('installer_messages.final.exit') }}
            </a>
        </div>
    </div>
@stop

@section('scripts')
    <script src="{{ asset('installer/js/jQuery-2.2.0.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const exitButton = document.getElementById('exit-button');

            if (exitButton) {
                exitButton.addEventListener('click', function(e) {
                    // Add loading state
                    this.classList.add('loading');

                    // Change button text to show loading state
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner"></span> Redirecting...';

                    // Prevent default action temporarily
                    e.preventDefault();

                    // Navigate after a short delay to show loading state
                    setTimeout(function() {
                        window.location.href = exitButton.getAttribute('href');
                    }, 500);
                });
            }
        });
    </script>
@endsection
