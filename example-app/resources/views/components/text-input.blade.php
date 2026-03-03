@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-yellow-400 focus:ring-yellow-400 dark:focus:ring-yellow-400 shadow-sm']) }}>
