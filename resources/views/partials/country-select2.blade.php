{{--
  Shared country Select2: matched input UI + AR display with EN/AR search.
  Optional props:
    $selector (default '#country_select2')
    $oldCountry
    $variant: 'academy' | 'classic' | 'pink'
--}}
@php
    $selector = $selector ?? '#country_select2';
    $oldCountry = (string) ($oldCountry ?? old('country', ''));
    $variant = $variant ?? 'academy';
    $isAr = app()->getLocale() === 'ar';
    $placeholder = $isAr ? 'اختر دولتك' : 'Select your country';
    $failLabel = $isAr ? 'تعذر تحميل الدول' : 'Failed to load countries';
@endphp

@once
<style>
    .country-select2-host {
        --cs2-radius: .85rem;
        --cs2-border: #cfd9e6;
        --cs2-bg: #fff;
        --cs2-text: #0f172a;
        --cs2-muted: #64748b;
        --cs2-focus: #0b8f7f;
        --cs2-focus-ring: rgba(11, 143, 127, .18);
        --cs2-min-h: 2.9rem;
    }
    .country-select2-host.is-pink {
        --cs2-focus: #ff3d7a;
        --cs2-focus-ring: rgba(255, 61, 122, .15);
    }
    .country-select2-host.is-classic {
        --cs2-radius: .5rem;
        --cs2-border: #d1d5db;
        --cs2-focus: #1f2937;
        --cs2-focus-ring: rgba(31, 41, 55, .15);
        --cs2-min-h: 2.65rem;
    }
    .country-select2-host .select2-container {
        width: 100% !important;
        font-family: inherit;
    }
    .country-select2-host .select2-container--default .select2-selection--single {
        height: auto !important;
        min-height: var(--cs2-min-h) !important;
        border: 1px solid var(--cs2-border) !important;
        border-radius: var(--cs2-radius) !important;
        background: var(--cs2-bg) !important;
        padding: .55rem .9rem !important;
        box-shadow: none !important;
        display: flex !important;
        align-items: center !important;
        transition: border-color .2s ease, box-shadow .2s ease;
    }
    .country-select2-host .select2-container--default.select2-container--focus .select2-selection--single,
    .country-select2-host .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--cs2-focus) !important;
        box-shadow: 0 0 0 3px var(--cs2-focus-ring) !important;
        outline: none !important;
    }
    .country-select2-host .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--cs2-text) !important;
        line-height: 1.4 !important;
        padding: 0 !important;
        padding-inline-end: 1.5rem !important;
        position: static !important;
        top: auto !important;
        font-size: .95rem;
    }
    .country-select2-host .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: var(--cs2-muted) !important;
        opacity: .75;
        position: static !important;
        top: auto !important;
        line-height: 1.4 !important;
    }
    .country-select2-host .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        top: 0 !important;
        inset-inline-end: .55rem;
        inset-inline-start: auto;
        width: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .country-select2-host .select2-container--default .select2-selection--single .select2-selection__arrow b {
        margin: 0 !important;
        position: static !important;
        border-color: var(--cs2-muted) transparent transparent transparent;
    }
    .country-select2-host .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none !important;
    }
    .country-select2-host .select2-dropdown {
        border: 1px solid var(--cs2-border) !important;
        border-radius: .75rem !important;
        overflow: hidden;
        box-shadow: 0 16px 36px rgba(6, 21, 37, .12);
    }
    .country-select2-host .select2-search--dropdown .select2-search__field {
        border: 1px solid var(--cs2-border) !important;
        border-radius: .65rem !important;
        padding: .65rem .85rem !important;
        outline: none !important;
    }
    .country-select2-host .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--cs2-focus) !important;
        box-shadow: 0 0 0 3px var(--cs2-focus-ring);
    }
    .country-select2-host .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background: var(--cs2-focus) !important;
    }
    .country-select2-host .select2-results__option {
        padding: .65rem .9rem;
        font-size: .92rem;
    }
    .country-select2-option-en {
        display: block;
        margin-top: .15rem;
        font-size: .75rem;
        opacity: .72;
        font-weight: 500;
    }
</style>
@endonce

@once
<script>
window.EvorqCountrySelect = window.EvorqCountrySelect || {
    cache: null,
    loading: null,
    load() {
        if (this.cache) return Promise.resolve(this.cache);
        if (this.loading) return this.loading;
        this.loading = fetch('https://raw.githubusercontent.com/mledoze/countries/master/countries.json')
            .then((r) => r.json())
            .then((data) => {
                this.cache = Array.isArray(data) ? data : [];
                return this.cache;
            })
            .finally(() => { this.loading = null; });
        return this.loading;
    },
    matcher(params, data) {
        if ($.trim(params.term) === '') return data;
        if (data.children && data.children.length) {
            const children = [];
            data.children.forEach((child) => {
                const match = this.matcher(params, child);
                if (match) children.push(match);
            });
            if (children.length) {
                const modified = $.extend({}, data, true);
                modified.children = children;
                return modified;
            }
            return null;
        }
        if (typeof data.text === 'undefined') return null;
        const term = String(params.term).toLowerCase().trim();
        const el = data.element;
        const hay = [
            data.text || '',
            el ? el.getAttribute('data-en') : '',
            el ? el.getAttribute('data-ar') : '',
            el ? el.getAttribute('data-search') : '',
        ].join(' ').toLowerCase();
        return hay.indexOf(term) > -1 ? data : null;
    },
    init(options) {
        const opts = Object.assign({
            selector: '#country_select2',
            oldCountry: '',
            isAr: true,
            placeholder: 'اختر دولتك',
            failLabel: 'تعذر تحميل الدول',
            width: '100%',
        }, options || {});

        const $select = $(opts.selector);
        if (!$select.length || typeof $select.select2 !== 'function') return;

        const host = $select.closest('.country-select2-host');
        const dropdownParent = host.length ? host : $(document.body);
        const self = this;

        this.load()
            .then((data) => {
                $select.empty();
                $select.append(new Option(opts.placeholder, '', true, true));

                data.forEach((country) => {
                    const en = country?.name?.common || '';
                    const ar = country?.translations?.ara?.common || en;
                    const code = country?.cca2 || '';
                    if (!code) return;
                    const label = opts.isAr ? (ar || en) : (en || ar);
                    const option = new Option(label, code, false, code === opts.oldCountry);
                    option.setAttribute('data-en', en);
                    option.setAttribute('data-ar', ar);
                    option.setAttribute('data-search', `${en} ${ar} ${code}`.trim());
                    $select.append(option);
                });

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    placeholder: opts.placeholder,
                    allowClear: true,
                    dir: opts.isAr ? 'rtl' : 'ltr',
                    width: opts.width,
                    dropdownParent: dropdownParent,
                    matcher: function (params, data) {
                        return self.matcher(params, data);
                    },
                    templateResult: function (item) {
                        if (!item.id) return item.text;
                        const en = item.element ? item.element.getAttribute('data-en') : '';
                        const ar = item.element ? item.element.getAttribute('data-ar') : '';
                        const secondary = opts.isAr ? en : ar;
                        if (!secondary || secondary === item.text) {
                            return item.text;
                        }
                        const wrap = document.createElement('span');
                        wrap.appendChild(document.createTextNode(item.text));
                        const sub = document.createElement('span');
                        sub.className = 'country-select2-option-en';
                        sub.textContent = secondary;
                        wrap.appendChild(sub);
                        return wrap;
                    },
                });
            })
            .catch(() => {
                $select.empty().append(new Option(opts.failLabel, '', true, true));
            });
    }
};
</script>
@endonce

<script>
(function () {
    function bootCountrySelect() {
        if (!window.jQuery || !jQuery.fn.select2 || !window.EvorqCountrySelect) {
            setTimeout(bootCountrySelect, 40);
            return;
        }
        window.EvorqCountrySelect.init({
            selector: @json($selector),
            oldCountry: @json($oldCountry),
            isAr: @json($isAr),
            placeholder: @json($placeholder),
            failLabel: @json($failLabel),
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootCountrySelect);
    } else {
        bootCountrySelect();
    }
})();
</script>
