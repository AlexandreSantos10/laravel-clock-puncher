<x-app-layout>




    <div class="py-9">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
               

                    <div class="w-full p-6 text-gray-900 dark:text-gray-100 flex justify-between sm:flex flex-wrap">

                        <div>

                            <form action="/user/logs" method="get">
                                @csrf
                                <div class="flex justify-between sm:flex flex-wrap">

                                    <label for="table-search" class="sr-only">Search</label>

                                    <div class ="ml-2">
                                        <x-text-input type="month" name="month"
                                            value="{{ request('month') }}"/>

                                            
                                    </div>
                                    <div class ="ml-2">

                                        <x-text-input type="number" min="1" max="30" name="time"
                                            placeholder="DAY" value="{{ request('time') }}"
                                            />
                                    </div>


                                    <div class="lg:pl-2">
                                        <x-secondary-app-button> SEARCH </x-secondary-app-button>
                                    </div>
                                    <div class="py-2">
                                        <a href="/user/logs">
                                             <x-refresh-icon/>
                                        </a>
                                    </div>


                            </form>

                        </div>


                    </div>
                    <form action="{{ route('exportuserlog') }}" method="get">
                        <input type="hidden" name ="month" value="{{ request('month') }}">
                        <input type="hidden" name ="time" value="{{ request('time') }}">
                        @csrf
                        <div class="ml-100 w-max flex space-between">

                            <div>
                                <select id="" name ="format"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-yellow-400 focus:border-yellow-400 block w-25 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-yellow-400 dark:focus:border-yellow-400">
                                    <option value="xlsx">XLSX</option>
                                    <option value="csv">CSV</option>

                                </select>
                            </div>
                            <div>
                                <x-secondary-app-button><x-export-icon/>
                                </x-secondary-app-button>
                            </div>
                        </div>
                    </form>

                </div>

                <div
                    class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
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
                            @foreach ($logs as $log)
                                <tr class="bg-neutral-primary border-b border-default">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-heading whitespace-nowrap text-gray-100">
                                        {{ $log->user->name }}
                                    </th>
                                    <td class="px-6 py-4 text-gray-100">
                                        {{ $log->data }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-100">
                                        {{ $print_time = date('H:i', strtotime($log->entrada)) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-100">
                                        {{ $print_time = date('H:i', strtotime($log->user->inicio_almoco)) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-100">
                                        {{ $print_time = date('H:i', strtotime($log->final_almoço)) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-100">
                                        {{ $print_time = date('H:i', strtotime($log->saida)) }}
                                    </td>

                                    <td class="px-6 py-4 text-gray-100">
                                        {{ $print_time = date('H:i', strtotime($log->total_horas)) }}
                                    </td>



                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $logs->links('pagination::tailwind') }}
                </div>


            </div>

        </div>

    </div>

    </div>
</x-app-layout>
