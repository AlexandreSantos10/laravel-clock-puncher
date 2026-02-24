<x-app-layout>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg place-items-center">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-between">
                    
                    <a href ="/createpost"type="button" class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-base text-sm px-4 py-2.5 text-center leading-5">Add Log</a>
                
                </div>
                
                <div class="w-full relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                    <table class="w-full text-sm text-left rtl:text-right text-body">
                        <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    User 
                                </th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Entry
                                </th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Lunch Start
                                </th>

                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Lunch End
                                </th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Exit
                                </th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Total Time
                                </th>
                                 <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Manage
                                </th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            @foreach($logs as $log)
                            <tr class="bg-neutral-primary border-b border-default">
                                <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap text-gray-100">
                                    {{ $log->name }}
                                </th>
                                <td class="px-6 py-4 text-gray-100">
                                    {{ $log->data }}
                                </td>
                                <td class="px-6 py-4 text-gray-100">
                                    {{ 
                                        $print_time = date("H:i", strtotime($log->entrada))
                                    }}
                                </td>
                                <td class="px-6 py-4 text-gray-100">
                                    {{$print_time = date("H:i", strtotime($log->inicio_almoco)) }}
                                </td>
                                <td class="px-6 py-4 text-gray-100">
                                    {{$print_time = date("H:i", strtotime($log->final_almoço)) }}
                                </td>
                                <td class="px-6 py-4 text-gray-100">
                                    {{ $print_time = date("H:i", strtotime($log->saida)) }}
                                </td>
                                
                                <td class="px-6 py-4 text-gray-100">
                                    {{$print_time = date("H:i", strtotime($log->total_horas))}}
                                </td>
                                <td class="flex items-center px-6 py-4 text-gray-100">
                                    <a href ="/editlog/{{$log->id}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>

                                    <form action ="/delete/{{$log->id}}" method="post">
                                        @csrf
                                    @method("delete")
                                    <button type="submit" style="cursor: pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                    
                                </form>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                
            </div>
        </div>
    </div>

    
</x-app-layout>
