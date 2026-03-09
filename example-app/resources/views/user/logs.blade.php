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
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
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
                                <x-secondary-app-button><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" />
                                    </svg>
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
