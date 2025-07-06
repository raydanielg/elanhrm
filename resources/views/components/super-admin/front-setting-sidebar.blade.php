<!-- SETTINGS SIDEBAR START -->
<div class="mobile-close-overlay w-100 h-100" id="close-settings-overlay"></div>
<div class="settings-sidebar bg-white py-3" id="mob-settings-sidebar">
    <a class="d-block d-lg-none close-it" id="close-settings"><i class="fa fa-times"></i></a>

    <!-- SETTINGS SEARCH START -->
    <form class="border-bottom-grey px-4 pb-3 d-flex">
        <div class="input-group rounded py-1 border-grey">
            <div class="input-group-prepend">
                <span class="input-group-text border-0 bg-white">
                    <i class="fa fa-search f-12 text-lightest"></i>
                </span>
            </div>
            <input type="text" id="search-setting-menu" class="form-control border-0 f-14 pl-0"
                   placeholder="@lang('app.search')"/>
        </div>
    </form>
    <!-- SETTINGS SEARCH END -->

    <!-- SETTINGS MENU START -->
    <ul class="settings-menu" id="settingsMenu">

        <x-setting-menu-item :active="$activeMenu" menu="front_theme_settings"
                             :href="route('superadmin.front-settings.front_theme_settings')"
                             :text="__('superadmin.menu.frontThemeSettings')">
        </x-setting-menu-item>

        @if (global_setting()->front_design)
            <x-setting-menu-item :active="$activeMenu" menu="testimonial_settings"
                                 :href="route('superadmin.front-settings.testimonial-settings.index')"
                                 :text="__('superadmin.menu.testimonialSetting')">
            </x-setting-menu-item>

            <x-setting-menu-item :active="$activeMenu" menu="client_settings"
                                 :href="route('superadmin.front-settings.client-settings.index')"
                                 :text="__('superadmin.menu.clientSetting')">
            </x-setting-menu-item>

            <x-setting-menu-item :active="$activeMenu" menu="faq_settings"
                                 :href="route('superadmin.front-settings.faq-settings.index')"
                                 :text="__('superadmin.menu.faqSetting')">
            </x-setting-menu-item>

            <x-setting-menu-item :active="$activeMenu" menu="price_settings_translation"
                                 :href="route('superadmin.front-settings.price-settings.lang')"
                                 :text="__('superadmin.menu.priceSetting')">
            </x-setting-menu-item>
        @endif

        <x-setting-menu-item :active="$activeMenu" menu="front_settings"
                             :href="route('superadmin.front-settings.front-settings.index')"
                             :text="__('superadmin.menu.frontSettings')">
        </x-setting-menu-item>

        @if (global_setting()->front_design)
            <x-setting-menu-item :active="$activeMenu" menu="feature_translation"
                                :href="route('superadmin.front-settings.features-translation.lang')"
                                :text="__('superadmin.menu.featureTranslation')">
            </x-setting-menu-item>
        @endif

        <x-setting-menu-item :active="$activeMenu" menu="feature"
                             :href="route('superadmin.front-settings.features-settings.index', ['tab' => 'image'])"
                             :text="__('superadmin.menu.features')">
        </x-setting-menu-item>

        <x-setting-menu-item :active="$activeMenu" menu="footer_setting"
                                :href="route('superadmin.front-settings.footer-settings.index')"
                                :text="__('superadmin.menu.footerSetting')">
        </x-setting-menu-item>

        @if (global_setting()->front_design)
            <x-setting-menu-item :active="$activeMenu" menu="cta_settings"
                                 :href="route('superadmin.front-settings.cta-settings.lang')"
                                 :text="__('superadmin.menu.ctaSetting')">
            </x-setting-menu-item>
        @endif

        <x-setting-menu-item :active="$activeMenu" menu="front_widgets"
                             :href="route('superadmin.front-settings.front-widgets.index')"
                             :text="__('superadmin.menu.frontWidgets')">
        </x-setting-menu-item>

        <x-setting-menu-item :active="$activeMenu" menu="seo_details"
                             :href="route('superadmin.front-settings.seo-detail.index')"
                             :text="__('superadmin.menu.seoDetails')">
        </x-setting-menu-item>


        <x-setting-menu-item :active="$activeMenu" menu="sign_up_settings"
                             :href="route('superadmin.front-settings.sign-up-setting.index')"
                             :text="__('superadmin.menu.signUpSetting')">
        </x-setting-menu-item>

        <x-setting-menu-item :active="$activeMenu" menu="front_menu_settings"
                             :href="route('superadmin.front-settings.front_menu_settings.lang')"
                             :text="__('superadmin.menu.frontMenuSettings')">
        </x-setting-menu-item>

        <x-setting-menu-item :active="$activeMenu" menu="contact_settings"
                             :href="route('superadmin.front-settings.contact_settings')"
                             :text="__('superadmin.menu.contactSetting')">
        </x-setting-menu-item>

        <x-setting-menu-item :active="$activeMenu" menu="social_link"
                            :href="route('superadmin.front-settings.social_link')"
                            :text="__('superadmin.frontCms.socialLinks')">
        </x-setting-menu-item>
    </ul>
    <!-- SETTINGS MENU END -->

</div>
<!-- SETTINGS SIDEBAR END -->

<script>
    $("body").on("click", ".ajax-tab", function (event) {
        event.preventDefault();

        $('.project-menu .p-sub-menu').removeClass('active');
        $(this).addClass('active');

        const requestUrl = this.href;

        $.easyAjax({
            url: requestUrl,
            blockUI: true,
            container: ".content-wrapper",
            historyPush: true,
            success: function (response) {
                if (response.status === "success") {
                    $('.content-wrapper').html(response.html);
                    init('.content-wrapper');
                }
            }
        });
    });

    $("#search-setting-menu").on("keyup", function () {
        const value = this.value.toLowerCase().trim();
        $("#settingsMenu li").show().filter(function () {
            return $(this).text().toLowerCase().trim().indexOf(value) == -1;
        }).hide();
    });
</script>
