@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border border-gray-200/80 bg-white/70 px-3 py-2 shadow-sm backdrop-blur placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-800/80 dark:bg-gray-950/30 dark:placeholder:text-gray-500']) }}>
