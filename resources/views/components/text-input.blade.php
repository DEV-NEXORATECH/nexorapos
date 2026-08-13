@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-blue-100 bg-blue-50/40 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500']) }}>
