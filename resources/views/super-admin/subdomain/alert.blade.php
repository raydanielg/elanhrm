@if(module_enabled('Subdomain'))
    <div class="col-12">
        <x-alert type="danger" icon="exclamation-triangle">
            @lang('superadmin.subDomainNotUpdated')
        </x-alert>
    </div>
@endif
