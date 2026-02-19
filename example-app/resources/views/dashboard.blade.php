<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Logs') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-between">
                    {{ __("Welcome to the Logs Page! ") }}{{ Auth::user()->name }}
                    <a href ="/createpost"type="button" class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-base text-sm px-4 py-2.5 text-center leading-5">Add Log</a>
                
                </div>
                
                <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                    <table class="w-full text-sm text-left rtl:text-right text-body">
                        <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    User ID
                                </th>
                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    Entry
                                </th>
                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    Exit
                                </th>
                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    Lunch End
                                </th>
                                <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    Total Time
                                </th>
                                 <th scope="col" class="px-6 py-3 font-medium text-gray-100">
                                    Manage
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr class="bg-neutral-primary border-b border-default">
                                <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap text-gray-100">
                                    {{ $log->user_id }}
                                </th>
                                <td class="px-6 py-4 text-gray-100">
                                    {{ $log->entrada }}
                                </td>
                                <td class="px-6 py-4 text-gray-100">
                                    {{ $log->saida }}
                                </td>
                                <td class="px-6 py-4 text-gray-100">
                                    {{ $log->final_almoço }}
                                </td>
                                <td class="px-6 py-4 text-gray-100">
                                    {{ $log->total_horas}}
                                </td>
                                <td class="px-6 py-4 text-gray-100">
                                    <a href ="/editlog/{{$log->id}}"type="button" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-base text-sm px-4 py-1.5 text-center leading-5">Edit</a>
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
