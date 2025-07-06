@extends('layouts.app')

@section('content')

    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        <x-super-admin.setting-sidebar :activeMenu="$activeSettingMenu"/>

        <x-setting-card>
            <x-slot name="header">
                <div class="s-b-n-header" id="tabs">
                    <h2 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                        @lang($pageTitle)</h2>
                </div>
            </x-slot>
            @include($view)
        </x-setting-card>

    </div>
    <!-- SETTINGS END -->
@endsection


