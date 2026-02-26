<x-app-layout>
    
    
<div class="bg-gray-800 min-h-screen">
<div class="py-50">
        <div class="max-w-md mx-auto sm:px-4 lg:px-12">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
<div class="max-w-lg p-6 bg-white border border-yellow-400 shadow-sm dark:bg-gray-800 dark:border-yellow-400 text-center">
    <a href="#">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Your Day has started!</h5>
    </a>
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">You started at: {{ $logs->entrada }}</p>
    
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">You can click here to finish your day.</p>
    
    
    
    <form action="{{route('clockfinishupdate',['logs' => $logs])}}" method="post">
    @csrf
    @method("put")
    <button type="submit" style="cursor: pointer" class="text-white hover:text-red-700 border border-red-700 hover:bg-inherit focus:ring-4 focus:outline-none focus:ring-red-300 font-medium text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-red-500 dark:text-white dark:hover:text-red-500 dark:hover:ring-red-600 dark:focus: bg-red-500">END DAY</button>
    </form>
    
</div>
</div>
</div>
    
</x-app-layout>

