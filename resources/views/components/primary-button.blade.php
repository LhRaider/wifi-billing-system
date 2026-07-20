<button
    {{ $attributes->class([
        'inline-flex items-center justify-center',
        'px-8 py-3',
        'rounded-2xl',
        'font-semibold',
        'text-white',
        'bg-gradient-to-r from-blue-600 to-indigo-700',
        'shadow-lg',
        'transition-all duration-300',
        'hover:shadow-2xl hover:-translate-y-0.5',
        'hover:from-blue-700 hover:to-indigo-800',
        'focus:outline-none focus:ring-4 focus:ring-blue-200',
        'disabled:opacity-50 disabled:cursor-not-allowed'
    ]) }}
>
    {{ $slot }}
</button>