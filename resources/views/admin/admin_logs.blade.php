<x-app-layout>
    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-row-reverse h-10"></div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm place-items-center">
                <div class="w-full p-6 text-gray-900 dark:text-gray-100 flex justify-between sm:flex flex-wrap">

                    <div>
                        <form action="{{ route('admin.adminlogs') }}" method="get">
                            @csrf
                            <div class="flex justify-between sm:flex flex-wrap">
                                <div>
                                    <select name="name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-yellow-400 focus:border-yellow-400 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-yellow-400 dark:focus:border-yellow-400">
                                        <option value="">SEARCH BY LOG OWNER</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->name }}"
                                                {{ request('name') == $user->name ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class ="ml-2">
                                    <x-text-input type="month" name="month" value="{{ request('month') }}" />
                                </div>

                                <div class="lg:pl-2">
                                    <x-secondary-app-button>SEARCH</x-secondary-app-button>
                                </div>

                                <div class="py-2 ml-2">
                                    <a href="{{ route('admin.adminlogs') }}">
                                        <x-refresh-icon />
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                   
                </div>

                <div
                    class="w-full relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                    <table class="w-full text-sm text-left rtl:text-right text-body">
                        <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">Author</th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">Log Owner</th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">Log Date</th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">Action</th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">Old</th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">Changes</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($admin_logs as $log)
                                <tr class="bg-neutral-primary border-b border-default">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium whitespace-nowrap {{ $log->autor->tipo == 'admin' ? 'text-yellow-400' : 'text-gray-100' }}">
                                        {{ $log->autor->name ?? 'System' }}
                                    </th>

                                    <td class="px-6 py-4 text-gray-100">
                                        {{ $log->owner_name }}
                                    </td>

                                    <td class="px-6 py-4 text-gray-100 font-bold">
                                        {{ $log->original_date }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            class="{{ $log->acao == 'DELETE' ? 'text-red-400' : 'text-green-400' }} font-bold">
                                            {{ $log->acao }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-gray-100">
                                        <button type="button"
                                            onclick="document.getElementById('dialog-old-{{ $log->id }}').showModal()"
                                            style="cursor: pointer">
                                            <x-eye-icon />
                                        </button>

                                        <el-dialog>
                                            <dialog id="dialog-old-{{ $log->id }}"
                                                aria-labelledby="dialog-title-old-{{ $log->id }}"
                                                class="fixed inset-0 m-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent p-0 backdrop:bg-transparent">
                                                <el-dialog-backdrop
                                                    class="fixed inset-0 bg-gray-900/70 transition-opacity"></el-dialog-backdrop>

                                                <div tabindex="0"
                                                    class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                    <el-dialog-panel
                                                        class="relative transform overflow-hidden bg-gray-800 text-left shadow-xl outline outline-1 -outline-offset-1 outline-white/10 transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                                        <div class="bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                                            <div class="sm:flex sm:items-start">
                                                                <div
                                                                    class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                                                    <h3 id="dialog-title-old-{{ $log->id }}"
                                                                        class="text-lg font-semibold text-white mb-4 border-b border-gray-600 pb-2">
                                                                        Original Data (Before)
                                                                    </h3>

                                                                    <div class="mt-2 text-left space-y-2">
                                                                        @if (is_array($log->dados_antigos))
                                                                            @foreach ($log->dados_antigos as $key => $value)
                                                                                @if (!in_array($key, ['id', 'created_at', 'updated_at', 'user_id']))
                                                                                    <div
                                                                                        class="flex border-b border-gray-700 pb-1">
                                                                                        <span
                                                                                            class="w-1/3 font-semibold text-gray-400 capitalize">{{ str_replace('_', ' ', $key) }}:</span>
                                                                                        <span
                                                                                            class="text-gray-100">{{ $value ?: '---' }}</span>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach
                                                                        @else
                                                                            <p class="text-gray-400">No data available.
                                                                            </p>
                                                                        @endif
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="bg-gray-700/25 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 justify-center items-center">
                                                            <span
                                                                onclick="document.getElementById('dialog-old-{{ $log->id }}').close()"
                                                                class="cursor-pointer inline-block">
                                                                <x-secondary-app-button type="button"
                                                                    class="pointer-events-none">
                                                                    Close
                                                                </x-secondary-app-button>
                                                            </span>
                                                        </div>
                                                    </el-dialog-panel>
                                                </div>
                                            </dialog>
                                        </el-dialog>
                                    </td>

                                    <td class="px-6 py-4 text-gray-100">
                                        @if ($log->dados_novos)
                                            <button type="button"
                                                onclick="document.getElementById('dialog-new-{{ $log->id }}').showModal()"
                                                style="cursor: pointer">
                                                <x-eye-icon />
                                            </button>

                                            <el-dialog>
                                                <dialog id="dialog-new-{{ $log->id }}"
                                                    aria-labelledby="dialog-title-new-{{ $log->id }}"
                                                    class="fixed inset-0 m-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent p-0 backdrop:bg-transparent">
                                                    <el-dialog-backdrop
                                                        class="fixed inset-0 bg-gray-900/70 transition-opacity"></el-dialog-backdrop>

                                                    <div tabindex="0"
                                                        class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                        <el-dialog-panel
                                                            class="relative transform overflow-hidden bg-gray-800 text-left shadow-xl outline outline-1 -outline-offset-1 outline-white/10 transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                                            <div class="bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                                                <div class="sm:flex sm:items-start">
                                                                    <div
                                                                        class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                                                        <h3 id="dialog-title-new-{{ $log->id }}"
                                                                            class="text-lg font-semibold text-white mb-4 border-b border-gray-600 pb-2">
                                                                            New Data (After)
                                                                        </h3>

                                                                        <div class="mt-2 text-left space-y-2">
                                                                            @if (is_array($log->dados_novos))
                                                                                @foreach ($log->dados_novos as $key => $value)
                                                                                    @if (!in_array($key, ['id', 'created_at', 'updated_at', 'user_id']))
                                                                                        <div
                                                                                            class="flex border-b border-gray-700 pb-1">
                                                                                            <span
                                                                                                class="w-1/3 font-semibold text-gray-400 capitalize">{{ str_replace('_', ' ', $key) }}:</span>
                                                                                            <span
                                                                                                class="text-yellow-400 font-medium">{{ $value ?: '---' }}</span>
                                                                                        </div>
                                                                                    @endif
                                                                                @endforeach
                                                                            @else
                                                                                <p class="text-gray-400">No data
                                                                                    available.</p>
                                                                            @endif
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="bg-gray-700/25 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 justify-center items-center">
                                                                <span
                                                                    onclick="document.getElementById('dialog-new-{{ $log->id }}').close()"
                                                                    class="cursor-pointer inline-block">
                                                                    <x-secondary-app-button type="button"
                                                                        class="pointer-events-none">
                                                                        Close
                                                                    </x-secondary-app-button>
                                                                </span>
                                                            </div>
                                                        </el-dialog-panel>
                                                    </div>
                                                </dialog>
                                            </el-dialog>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-4">
                        {{ $admin_logs->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
