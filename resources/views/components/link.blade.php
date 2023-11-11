<a {{ $attributes->class([
    'text-white px-3 py-2 cursor-pointer flex items-center text-xs uppercase font-bold rounded bg-slate-700 hover:bg-slate-500',
    'aria-disabled:opacity-50 disabled:opacity-50 aria-disabled:cursor-not-allowed disabled:cursor-not-allowed aria-disabled:pointer-events-none disabled:pointer-events-none',
]) }}>
    {{ $slot }}
</a>
