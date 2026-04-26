<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white disabled:opacity-50 disabled:pointer-events-none transition ease-in-out duration-150 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:hover:bg-gray-800 dark:focus:ring-offset-gray-950']) }}>
    {{ $slot }}
</button>
