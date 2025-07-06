<x-setting-card>

    <x-slot name="header">
        <div class="s-b-n-header" id="tabs">
            <nav class="tabs border-bottom-grey">
                <ul class="nav -primary" id="nav-tab" role="tablist">
                    @foreach ($languageSettings->sortBy('language_code') as $language)
                        <li>
                            <a class="nav-item nav-link f-15 @if ($loop->first) active @endif lang-{{$language->language_code}}"
                               data-toggle="tab" href="{{ route($route, [$language->language_code]) }}" role="tab"
                               aria-controls="nav-{{ $language->language_code }}" aria-selected="true">
                                <span
                                    class='flag-icon flag-icon-{{ ($language->language_code == 'en') ? 'gb' : strtolower($language->flag_code) }} flag-icon-squared'></span>
                                {{ $language->language_name }}
                                @if( isset($allLangTranslation) && in_array($language->id, array_column($allLangTranslation,'language_setting_id')))
                                    <i class='fa fa-circle ml-1 text-light-green'></i>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </x-slot>
    {{-- include tabs here --}}
    @include($view)

</x-setting-card>

@push('scripts')
    <script>
        /*******************************************************
         More btn in lang menu Start
         *******************************************************/

        const container = document.querySelector('.tabs');
        const primary = container.querySelector('.-primary');
        const primaryItems = container.querySelectorAll('.-primary > li:not(.-more)');
        container.classList.add('--jsfied'); // insert "more" button and duplicate the list

        primary.insertAdjacentHTML('beforeend', `
        <li class="-more bg-grey">
            <button type="button" class="px-4 h-100 d-lg-flex d-md-flex align-items-center justify-content-center" aria-haspopup="true" aria-expanded="false">
                    More <span>&darr;</span>
            </button>
            <ul class="-secondary" id="hide-project-menues">
                ${primary.innerHTML}
            </ul>
        </li>
        `);
        const secondary = container.querySelector('.-secondary');
        const secondaryItems = secondary.querySelectorAll('li');
        const allItems = container.querySelectorAll('li');
        const moreLi = primary.querySelector('.-more');
        const moreBtn = moreLi.querySelector('button');
        moreBtn.addEventListener('click', e => {
            e.preventDefault();
            container.classList.toggle('--show-secondary');
            moreBtn.setAttribute('aria-expanded', container.classList.contains('--show-secondary'));
        }); // adapt tabs

        const doAdapt = () => {
            // reveal all items for the calculation
            allItems.forEach(item => {
                item.classList.remove('--hidden');
            }); // hide items that won't fit in the Primary

            let stopWidth = moreBtn.offsetWidth;
            let hiddenItems = [];
            const primaryWidth = primary.offsetWidth;
            primaryItems.forEach((item, i) => {
                if (primaryWidth >= stopWidth + item.offsetWidth) {
                    stopWidth += item.offsetWidth;
                } else {
                    item.classList.add('--hidden');
                    hiddenItems.push(i);
                }
            }); // toggle the visibility of More button and items in Secondary

            if (!hiddenItems.length) {
                moreLi.classList.add('--hidden');
                container.classList.remove('--show-secondary');
                moreBtn.setAttribute('aria-expanded', false);
            } else {
                secondaryItems.forEach((item, i) => {
                    if (!hiddenItems.includes(i)) {
                        item.classList.add('--hidden');
                    }
                });
            }
        };

        doAdapt(); // adapt immediately on load

        window.addEventListener('resize', doAdapt); // adapt on window resize
        // hide Secondary on the outside click

        document.addEventListener('click', e => {
            let el = e.target;

            while (el) {
                if (el === secondary || el === moreBtn) {
                    return;
                }

                el = el.parentNode;
            }

            container.classList.remove('--show-secondary');
            moreBtn.setAttribute('aria-expanded', false);
        });
        /*******************************************************
         More btn in projects menu End
         *******************************************************/
    </script>
    <script>
        /* manage menu active class */
        $('.nav-item').removeClass('active');
        var activeTab = "lang-{{ $activeTab }}";
        $('.' + activeTab).addClass('active');

        $("body").on("click", "#editSettings .nav a", function (event) {
            event.preventDefault();

            $('.nav-item').removeClass('active');
            $(this).addClass('active');

            const requestUrl = this.href;

            $.easyAjax({
                url: requestUrl,
                blockUI: true,
                container: "#nav-tabContent",
                historyPush: true,
                success: function (response) {
                    if (response.status === "success") {
                        $('#nav-tabContent').html(response.html);
                        init('.settings-box');
                        init('#F');
                    }
                }
            });
        });


        function updateLang(url, file = false) {
            $.easyAjax({
                url: url,
                container: '#editSettings',
                type: "POST",
                blockUI: true,
                file: file,
                data: $('#editSettings').serialize(),
                success: function (response) {
                    // This will add green-circle icon
                    addBadge(response);
                }
            })
        }


        function addBadge(response) {
            $(`.lang-${response.lang} .fa-circle`).remove()
            if (response.lang) {
                $(`.lang-${response.lang}`).append("<i class='fa fa-circle ml-1 text-light-green'></i>")
            }
        }
    </script>
@endpush
