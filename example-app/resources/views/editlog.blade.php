<x-app-layout>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-between">
                    {{ __("Welcome to the Edit a Log Page! ") }}{{ Auth::user()->name }} 
                    
                </div>
            
</div>


<form action="{{ route('update',['logs' => $logs]) }}" method="post">
    @csrf
    @method("put")
    <div class="grid gap-6 mb-6 md:grid-cols-2">
        
        <div>
            <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date</label>
            <input type="date" name ="data" id="date" value="{{$logs->data}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
        </div>
        <div>
            <label for="entry" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Entry</label>
            <input type="time" id="entry" min="08:00" value="{{$logs->entrada}}" name ="entrada" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
        </div>  
        
        <div>
            <label for="left" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Left At</label>
            <input type="time" id="left" min="16:00" value="{{$logs->saida}}" name = "saida" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
        </div>
        
        <div>
            <label for="obs" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Obs</label>
            <input type="text" id="obs" name ="obs" value="{{$logs->obs}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder=""required />
        </div>
    </div>
    
    <div class="flex items-start mb-6">
        <div class="flex items-center h-5">
        <input id="remember" type="checkbox" value="" class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800" required />
        </div>
        <label for="remember" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">I agree with the <a href="#" class="text-yellow-300 hover:underline dark:text-yellow-300">terms and conditions</a>.</label>
    </div>
    <div class="invisible">
            <input type="hidden" value="{{ $logs ->user_id }}" name="user_id">
        </div>
    <button type="submit" class="text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-yellow-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900">Submit</button>
</form>

   
                

                
            
        </div>
    </div>

    
</x-app-layout>
