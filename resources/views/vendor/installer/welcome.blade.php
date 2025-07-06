@extends('vendor.installer.layouts.master')

@section('title', trans('installer_messages.welcome.title'))
@section('container')
    <p class="paragraph" style="text-align: center;">{{ trans('installer_messages.welcome.message') }}</p>
    <div class="buttons">
        <a href="{{ route('LaravelInstaller::environment') }}" class="button" id="next-button">{{ trans('installer_messages.next') }}</a>
    </div>
@stop

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nextButton = document.getElementById('next-button');

        nextButton.addEventListener('click', function(e) {
            // Add loading class to button
            this.classList.add('loading');

            // Change button text to show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner"></span> Submitting...';

            // Prevent default action temporarily to show loading state
            e.preventDefault();

            // Navigate after a short delay to show the loading state
            setTimeout(function() {
                window.location.href = nextButton.getAttribute('href');
            }, 500);
        });
    });
</script>

<style>
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
