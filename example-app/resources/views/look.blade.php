<x-app-layout>
    
    
<div class="bg-gray-800 min-h-screen">
<div class="py-55">
        <div class="max-w-md mx-auto sm:px-4 lg:px-12">
            
<div class="max-w-lg p-6 bg-white  rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 text-center">
    
    <p class="mb-3 font-normal text-gray-700 dark:text-white">Started at: {{ $logs->entrada }}</p>
    <p class="mb-3 font-normal text-gray-700 dark:text-white">Left at: {{ $logs->saida }}</p>
    <p class="mb-3 font-normal text-gray-700 dark:text-white"> Created by {{ $logs->created_by }}</p>
     <p class="mb-3 font-normal text-gray-700 dark:text-white">{{ $logs->total_horas }} hours were done</p>

    <a href="/dashboard" class="mb-3 font-normal text-gray-700 dark:text-red-500">Back</a>
    
    
      
</div>
</div>
    
</x-app-layout>

