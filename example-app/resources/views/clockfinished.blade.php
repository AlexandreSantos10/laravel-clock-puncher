<x-app-layout>
    
    
<div class="bg-gray-800">
<div class="py-55">
        <div class="max-w-md mx-auto sm:px-4 lg:px-12">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
<div class="max-w-lg p-6 bg-white border border-white rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 text-center">
    <a href="#">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Your Day has ended!</h5>
    </a>
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">You started at: {{ $logs->entrada }}</p>
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">You left at: {{ $logs->saida }}</p>
     <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">You did {{ $logs->total_horas }} hours </p>

    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Thank you for logging your day.</p>
    
    
    
    
    
</div>  
</div>
</div>
    
</x-app-layout>

