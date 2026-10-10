@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        default => 'sm:max-w-2xl',
    };
@endphp

<div
    x-data="{
        show: @js((bool) $show),

        focusables() {
            const selector = 'a[href], button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])';

            return [...$el.querySelectorAll(selector)]
                .filter((element) => {
                    return !element.hasAttribute('disabled')
                        && element.getAttribute('aria-hidden') !== 'true';
                });
        },

        firstFocusable() {
            return this.focusables()[0] ?? null;
        },

        lastFocusable() {
            return this.focusables().slice(-1)[0] ?? null;
        },

        focusFirst() {
            this.firstFocusable()?.focus();
        },

        focusNext() {
            const elements = this.focusables();

            if (elements.length === 0) {
                return;
            }

            const currentIndex = elements.indexOf(document.activeElement);
            const nextIndex = currentIndex === -1
                ? 0
                : (currentIndex + 1) % elements.length;

            elements[nextIndex]?.focus();
        },

        focusPrevious() {
            const elements = this.focusables();

            if (elements.length === 0) {
                return;
            }

            const currentIndex = elements.indexOf(document.activeElement);
            const previousIndex = currentIndex <= 0
                ? elements.length - 1
                : currentIndex - 1;

            elements[previousIndex]?.focus();
        },

        closeModal() {
            this.show = false;
        }
    }"
    x-init="$watch('show', (value) => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');

            @if($attributes->has('focusable'))
                setTimeout(() => focusFirst(), 100);
            @endif
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="
        if ($event.detail === @js($name)) {
            show = true;
        }
    "
    x-on:close-modal.window="
        if ($event.detail === @js($name)) {
            show = false;
        }
    "
    x-on:close.stop="closeModal()"
    x-on:keydown.escape.window="closeModal()"
    x-on:keydown.tab.prevent="focusNext()"
    x-on:keydown.shift.tab.prevent="focusPrevious()"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    style="display: {{ $show ? 'block' : 'none' }};"
    role="dialog"
    aria-modal="true"
    aria-label="{{ str_replace(['-', '_'], ' ', ucfirst($name)) }}"
>
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="closeModal()"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
    >
        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
    </div>

    <div
        x-show="show"
        class="relative mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidthClass }} sm:mx-auto"
        x-on:click.stop
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        {{ $slot }}
    </div>
</div>