<script defer src="{{ asset('saas/js/cookieconsent.js') }}"></script>
<script>
    window.addEventListener('load', function () {

        // obtain plugin
        var cc = initCookieConsent();

        // run plugin with your configuration
        cc.run({
            current_lang: 'en',
            autoclear_cookies: true,

            // mode: 'opt-in'                          // default: 'opt-in'; value: 'opt-in' or 'opt-out'
            // delay: 0,                               // default: 0
            // auto_language: '',                      // default: null; could also be 'browser' or 'document'
            // autorun: true,                          // default: true
            // force_consent: false,                   // default: false
            // hide_from_bots: true,                   // default: true
            remove_cookie_tables: true,             // default: false
            cookie_name: "{{ addslashes(strtolower(str()->slug(global_setting()->global_app_name)).'_cc_cookie')}}",               // default: 'cc_cookie'
            // cookie_expiration: 182,                 // default: 182 (days)
            // cookie_necessary_only_expiration: 182   // default: disabled
            // cookie_domain: location.hostname,       // default: current domain
            // cookie_path: '/',                       // default: root
            // cookie_same_site: 'Lax',                // default: 'Lax'
            // use_rfc_cookie: false,                  // default: false
            // revision: 0,                            // default: 0// default: false
            page_scripts: true,                        // default: false
            gui_options: {
                consent_modal: {
                    layout: 'box',               // box/cloud/bar
                    position: 'bottom right',     // bottom/middle/top + left/right/center
                    transition: 'slide',           // zoom/slide
                    swap_buttons: true            // enable to invert buttons
                },
                settings_modal: {
                    layout: 'box',                 // box/bar
                    position: 'right',           // left/right
                    transition: 'zoom'            // zoom/slide
                }
            },
            languages: {
                'en': {
                    consent_modal: {
                        title: "@lang('cookie.title')",
                        description: '@lang("cookie.description")<button type="button" data-cc="c-settings" class="cc-link">@lang("cookie.letMeChoose")</button>',
                        primary_btn: {
                            text: "@lang('cookie.acceptAll')",
                            role: 'accept_all'              // 'accept_selected' or 'accept_all'
                        },
                        secondary_btn: {
                            text: "@lang('cookie.acceptNecessary')",
                            role: 'accept_necessary'        // 'settings' or 'accept_necessary'
                        }
                    },
                    settings_modal: {
                        title: "@lang('cookie.cookiePreferences')",
                        save_settings_btn: "@lang('cookie.saveSettings')",
                        accept_all_btn: "@lang('cookie.acceptAll')",
                        reject_all_btn: "@lang('cookie.acceptNecessary')",
                        close_btn_label: "@lang('app.close')",
                        cookie_table_headers: [
                            {col1: 'Name'},
                            {col2: 'Domain'},
                            {col3: 'Expiration'},
                            {col4: 'Description'}
                        ],
                        blocks: [
                            {
                                title: '{{__('cookie.cookieUsage')}} 📢',
                                description: '{{__('cookie.cookieUsageDescription')}}'
                            }, {
                                title: '{{__('cookie.strictlyNecessaryCookies')}}',
                                description: '{{__('cookie.strictlyNecessaryCookiesDescription')}}',
                                toggle: {
                                    value: 'necessary',
                                    enabled: true,
                                    readonly: true          // cookie categories with readonly=true are all treated as "necessary cookies"
                                }
                            }, {
                                title: '{{__('cookie.performanceAnalyticsCookies')}}',
                                description: '{{__('cookie.performanceAnalyticsCookiesDescription')}}',
                                toggle: {
                                    value: 'analytics',     // your cookie category
                                    enabled: false,
                                    readonly: false
                                },
                                cookie_table: [             // list of all expected cookies
                                    {
                                        col1: '^_ga',       // match all cookies starting with "_ga"
                                        col2: 'google.com',
                                        col3: '2 years',
                                        col4: 'description ...',
                                        is_regex: true
                                    },
                                    {
                                        col1: '_gid',
                                        col2: 'google.com',
                                        col3: '1 day',
                                        col4: 'description ...',
                                    }
                                ]
                            }, {
                                title: '{{__('cookie.advertisementTitle')}}',
                                description: '{{__('cookie.advertisementTitleDescription')}}',
                                toggle: {
                                    value: 'targeting',
                                    enabled: false,
                                    readonly: false
                                }
                            }, {
                                title: '{{__('cookie.moreInformation')}}',
                                description: '{{__('cookie.moreInformationDescription')}} <a class="cc-link" href="{{ route('front.contact')}}">{{ $frontMenu->contact }}</a>.',
                            }
                        ]
                    }
                }
            }
        });
    });
</script>
