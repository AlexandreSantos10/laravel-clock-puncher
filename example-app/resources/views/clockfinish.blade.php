<x-app-layout>
    
    
    
<div class="py-10">
        <div class="max-w-md mx-auto sm:px-4 lg:px-12">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
<div class="max-w-lg p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 ">
    <a href="#">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Your Day has started!</h5>
    </a>
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">You started at: {{ $logs->entrada }}</p>
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Lunch is going to end around: {{ $logs->final_almoço }}</p>
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">You can click here to finish your day.</p>
    
    
    <form action="{{route('clockfinishupdate',['logs' => $logs])}}" method="post">
    @csrf
    @method("put")
    <button type="submit" class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-base text-sm px-4 py-2.5 text-center leading-5">End Day</button>
    </form>
    
    
</div>
</div>
    
</x-app-layout>

