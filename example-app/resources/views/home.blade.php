<x-app-layout>
    
<div class="bg-gray-800">
<div class="py-50">
        <div class="max-w-md mx-auto sm:px-4 lg:px-12 ">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
<div class="max-w p-6 bg-white border border-white rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 text-center ">
    <a href="#">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Welcome to the Clock Puncher</h5>
    </a>
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Here you can keep track of how many hours you do each day.</p>
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Local time: {{ now() }}</p>
    <form action="{{route('logcreate')}}" method="post">
    @csrf
    <button type="submit" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-base text-sm px-4 py-2.5 text-center leading-5">Start Day</button>
    </form>
   
    
    
</div>
</div>
    </div>
    </div>
</x-app-layout>

