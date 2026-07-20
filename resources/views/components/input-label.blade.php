@props(['value'])

<label
    {{ $attributes->class([
        'block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-200 tracking-wide'
    ]) }}
>
    {{ $value ?? $slot }}
</label>