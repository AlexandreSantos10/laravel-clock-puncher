<x-app-layout>

    <div class="">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-row-reverse">
                <a href ="/admin/createlogview">
                <x-primary-app-button>ADD</x-primary-app-button>
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm place-items-center">

                <div class="w-full p-6 text-gray-900 dark:text-gray-100 flex justify-between sm:flex flex-wrap">

                    <div>

                        <form action="/admin/logs" method="get">
                            @csrf
                            <div class="flex justify-between sm:flex flex-wrap">

                                <label for="table-search" class="sr-only">Search</label>

                                <div>
                                    <div class="">
                                        <select id="" name ="name"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-yellow-400 focus:border-yellow-400 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-yellow-400 dark:focus:border-yellow-400">
                                            <option value="">ALL USERS</option>
                                            @foreach ($users as $user)
                                                <option
                                                    value="{{ $user->name }}"{{ request('name') == $user->name ? 'selected' : '' }}>
                                                    {{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
                                    <x-secondary-app-button>SEARCH</x-secondary-app-button>
                                </div>
                                <div class="py-2">
                                    <a href="/admin/logs">
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
                <form action="{{ route('export') }}" method="get">
                    <input type="hidden" name ="name" value="{{ request('name') }}">
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
                class="w-full relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">

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
                                <td class="flex items-center px-4 py-4 text-gray-100">
                                    <a href ="/admin/looklog/{{ $log->id }}" class="mr-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>

                                    </a>

                                    <a href ="/admin/editlog/{{ $log->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>
                                    <button command="show-modal" style="cursor: pointer" commandfor="dialog{{ $log->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </td>
                                <el-dialog>
                                
                                        <dialog id="dialog{{ $log->id }}" aria-labelledby="dialog-title{{ $log->id }}"
                                            class="fixed inset-0 m-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent p-0 backdrop:bg-transparent">
                                            <el-dialog-backdrop
                                                class="fixed inset-0 bg-gray-900/50 transition-opacity data-[closed]:opacity-0 data-[enter]:duration-300 data-[leave]:duration-200 data-[enter]:ease-out data-[leave]:ease-in"></el-dialog-backdrop>

                                            <div tabindex="0"
                                                class="flex min-h-full items-end justify-center p-4 text-center focus:outline focus:outline-0 sm:items-center sm:p-0">
                                                <el-dialog-panel
                                                    class="relative transform overflow-hidden bg-gray-800 text-left shadow-xl outline outline-1 -outline-offset-1 outline-white/10 transition-all data-[closed]:translate-y-4 data-[closed]:opacity-0 data-[enter]:duration-300 data-[leave]:duration-200 data-[enter]:ease-out data-[leave]:ease-in sm:my-8 sm:max-w-lg data-[closed]:sm:translate-y-0 data-[closed]:sm:scale-95">
                                                    <div class="bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                                        <div class="sm:flex sm:items-start">

                                                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                            <h3 id="dialog-title"
                                                                class="text-base font-semibold text-white">Delete Log
                                                            </h3>
                                                            <div class="mt-2">
                                                                <p class="text-sm text-gray-400">Are you sure you want
                                                                    to delete this Log?</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="bg-gray-700/25 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 justify-center items-center">

                                                    <form action ="/admin/delete/{{ $log->id }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <x-primary-red-button
                                                            command="close" commandfor="dialog"
                                                            >Delete</x-primary-red-button>


                                                    </form>
                                                    <x-secondary-red-button type="button"  command="close"
                                                        commandfor="dialog{{ $log->id }}"
                                                        class="mt-3 inline-flex w-full justify-center bg-white/10 px-3 py-2 text-sm font-semibold text-white ring-1 ring-inset ring-white/5 hover:bg-white/20 sm:mt-0 sm:w-auto">Cancel</x-secondary-red-button>

                                                </div>
                                            </el-dialog-panel>
                                        </div>
                                    </dialog>
                                </el-dialog>



                            </tr>
                        @endforeach
                    </tbody>

                </table>
                {{ $logs->links('pagination::tailwind') }}
            </div>


        </div>
    </div>

</x-app-layout>
