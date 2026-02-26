<x-app-layout>
    
    
    
    
    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                
               
                <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                    <table class="w-full text-sm text-left rtl:text-right text-body">
                        <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    User 
                                </th>
                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    Entry
                                </th>
                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    Lunch Start
                                </th>

                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    Lunch End
                                </th>
                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    Exit
                                </th>
                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    Total Time
                                </th>
                                 
                            </tr>
                        </thead>
                        
                        <tbody>
                            @foreach($logs as $log)
                            <tr class="bg-neutral-primary border-b border-default">
                                <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap text-gray-100">
                                    {{ $log->user->name }}
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
                                    {{$print_time = date("H:i", strtotime($log->user->inicio_almoco)) }}
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
                                

                            
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                </div>

                
            </div>
            
        </div>
        
    </div>

</div>
</x-app-layout>

