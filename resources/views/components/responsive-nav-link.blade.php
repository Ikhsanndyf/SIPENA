@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-lg bg-indigo-50 px-3 py-2.5 text-start text-sm font-semibold text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500'
            : 'block w-full rounded-lg px-3 py-2.5 text-start text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
