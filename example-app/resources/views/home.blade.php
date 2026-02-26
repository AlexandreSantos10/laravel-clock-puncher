<x-app-layout>
    
<div class="bg-gray-800 min-h-screen">
<div class="py-50">
        <div class="max-w-md mx-auto sm:px-4 lg:px-12 ">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
<div class="max-w p-6 bg-white border border-yellow-400 shadow-sm dark:bg-gray-800 dark:border-yellow-400 text-center ">
    <a href="#">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Welcome to the Clock Puncher</h5>
    </a>
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Here you can keep track of how many hours you do each day.</p>
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Local time: {{ now() }}</p>
    <form action="{{route('logcreate')}}" method="post">
    @csrf
    <button type="submit" style="cursor: pointer" class="text-white hover:text-yellow-400 border border-yellow-400 hover:bg-inherit focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium text-sm px-5 py-2 text-center  dark:border-yellow-300 dark:text-white dark:hover:text-yellow-300 dark:hover: ring-yellow-900 dark:focus: bg-yellow-400">START DAY</button>
    </form>
   
    
    
</div>
</div>
    </div>
    </div>
</x-app-layout>

