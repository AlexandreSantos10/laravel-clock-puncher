<x-app-layout>


    <div class="">

        <div class=" max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-row-reverse">
                <a href ="/admin/createuserview">
                    <x-primary-app-button>ADD</x-primary-app-button>
                </a>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm place-items-center">
                <div class=" w-full p-6 text-gray-900 dark:text-gray-100 flex justify-between sm:flex flex-wrap">
                    <div class=" flex justify-between">
                        <div>
                            <form action="/admin/users" method="get">
                                @csrf
                                <x-text-input type="text" name="name" id="table-search"
                                    value="{{ request('name') }}" placeholder="Search name" />
                        </div>
                        <div class="pl-3">

                            <x-secondary-app-button>SEARCH</x-secondary-app-button>
                            </form>
                        </div>
                        <div class="py-2">
                            <a href="/admin/users">
                                <x-refresh-icon />
                            </a>
                        </div>

                    </div>
                    <form action="{{ route('exportusers') }}" method="get">
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
                                <x-secondary-app-button>
                                    <x-export-icon />
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
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Finger
                                </th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Lunch Start
                                </th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Manage
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="bg-neutral-primary border-b border-default">
                                    <th class="px-6 py-4 font-medium text-heading whitespace-nowrap text-gray-100">
                                        {{ $user->name }}
                                    </th>

                                    <td class="px-6 py-4 text-gray-100">
                                        {{ $user->email }}
                                    </td>
                                    <td>
                                        @if (!$user->finger)
                                            <form action="{{ route('users.enroll', $user->id) }}" method="POST">
                                                @csrf
                                                <button style="cursor:pointer" type="submit" class="text-red-400">
                                                    Enroll Finger
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-success text-yellow-400">✔ Active</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <button
                                            class="{{ $user->tipo == 'admin' ? 'text-yellow-400' : 'text-gray-100' }}">
                                            {{ $user->tipo }}
                                        </button>
                                    </td>

                                    <td class="px-6 py-4 text-gray-100">
                                        {{ $print_time = date('H:i', strtotime($user->inicio_almoco)) }}
                                    </td>
                                    @if (Auth::user()->id != $user->id)
                                        <td class="px-10 py-4 text-gray-100">
                                            <button command="show-modal" commandfor="dialog{{ $user->id }}"
                                                style="cursor:pointer">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </button>
                                        </td>
                                    @endif
                                    <el-dialog>

                                        <dialog id="dialog{{ $user->id }}"
                                            aria-labelledby="dialog-title{{ $user->id }}"
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
                                                                    class="text-base font-semibold text-white">
                                                                    Switch User Role</h3>
                                                                <div class="mt-2">
                                                                    <p class="text-sm text-gray-400">Are u sure u
                                                                        want to switch
                                                                        {{ $user->name }}'s role?</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="bg-gray-700/25 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 justify-center items-center">
                                                        <form action="{{ route('changeusertype', $user->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <x-primary-app-button type="submit"
                                                                commandfor="dialog">Switch</x-primary-app-button>

                                                            <button type="button" style="cursor: pointer"
                                                                class="text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium text-sm px-5 py-2 text-center me-2 dark:border-#ebab13-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900"
                                                                command="close"
                                                                commandfor="dialog{{ $user->id }}">Cancel</button>
                                                        </form>
                                                    </div>
                                                </el-dialog-panel>
                                            </div>
                                        </dialog>
                                    </el-dialog>


                                </tr>
                            @endforeach

                        </tbody>
                    </table>


                    @if (!request('name'))
                        {{ $users->links('pagination::tailwind') }}
                    @endif
                </div>



            </div>
        </div>
    </div>


</x-app-layout>
