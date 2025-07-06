@extends('layouts.app')

@section('content')
    <style>

        .rainbow {
            position: relative;
            z-index: 0;
            overflow: hidden;
            padding: 2rem;

        &
        ::before {
            content: '';
            position: absolute;
            z-index: -2;
            left: -50%;
            top: -50%;
            width: 200%;
            height: 200%;
            background-color: #399953;
            background-repeat: no-repeat;
            background-size: 50% 50%, 50% 50%;
            background-position: 0 0, 100% 0, 100% 100%, 0 100%;
            background-image: linear-gradient(#399953, #399953), linear-gradient(#fbb300, #fbb300), linear-gradient(#d53e33, #d53e33), linear-gradient(#377af5, #377af5);
            animation: rotate 8s linear infinite;
        }

        &
        ::after {
            content: '';
            position: absolute;
            z-index: -1;
            left: 3px;
            top: 3px;
            width: calc(100% - 6px);
            height: calc(100% - 6px);
            background: white;
            border-radius: 2px;
        }

        }
    </style>
    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        {{-- SAAS --}}
        @if(user()->is_superadmin)
            <x-super-admin.setting-sidebar :activeMenu="$activeSettingMenu"/>
        @else
            <x-setting-sidebar :activeMenu="$activeSettingMenu"/>
        @endif

        <x-setting-card>
            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <h2 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                        @lang($pageTitle)</h2>
                </div>
            </x-slot>

            <div class="col-lg-12 col-md-12 w-100 p-4 ">
                @php($updateVersionInfo = \Froiden\Envato\Functions\EnvatoUpdate::updateVersionInfo())
                @include('vendor.froiden-envato.update.update_blade')
                @include('vendor.froiden-envato.update.version_info')
                @include('vendor.froiden-envato.update.changelog')
                @include('vendor.froiden-envato.update.plugins')
            </div>

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection

@push('scripts')
    @include('vendor.froiden-envato.update.update_script')
@endpush
