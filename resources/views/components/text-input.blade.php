@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->class([
        'w-full rounded-2xl border border-slate-300 bg-white px-5 py-3 text-slate-700',
        'shadow-sm transition-all duration-300',
        'placeholder:text-slate-400',
        'focus:border-blue-500 focus:ring-4 focus:ring-blue-100',
        'dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:ring-blue-900/40'
    ]) }}
>