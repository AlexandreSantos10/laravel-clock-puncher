<x-app-layout>
    @if (!empty($message))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <span class="font-medium">Danger alert!</span> {{ $message }}
        </div>
    @endif
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-between">
                    {{ __('Welcome to the Edit a Log Page! ') }}{{ Auth::user()->name }}

                </div>

            </div>


            <form action="{{ route('update', ['logs' => $logs]) }}" method="post">
                @csrf
                @method('put')
                <div class="grid gap-6 mb-6 md:grid-cols-2">

                    <div>
                        <label for="date"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date</label>
                        <input type="date" name ="data" id="date" value="{{ $logs->data }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-yellow-500 dark:focus:border-yellow-500"
                            required />
                    </div>
                    <div>
                        <label for="entry"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Entry</label>
                        <input type="time" id="entry" min="08:00" value="{{ $logs->entrada }}" name ="entrada"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-yellow-500 dark:focus:border-yellow-500"
                            required />
                    </div>

                    <div>
                        <label for="left" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Left
                            At</label>
                        <input type="time" id="left" min="{{ $logs->entrada }}" value="{{ $logs->saida }}"
                            name = "saida"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-yellow-500 dark:focus:border-yellow-500"
                            required />
                    </div>

                    <div>
                        <label for="obs"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Obs</label>
                        <input type="text" id="obs" name ="obs" value="{{ $logs->obs }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-yellow-500 dark:focus:border-yellow-500"
                            placeholder=""required />
                    </div>
                </div>

                <div class="flex items-start mb-6">
                    <div class="flex items-center h-5">
                        <input id="remember" type="checkbox" value=""
                            class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-yellow-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-yellow-600 dark:ring-offset-gray-800"
                            required />
                    </div>
                    <label for="remember" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">I agree with
                        the <a href="#" class="text-yellow-300 hover:underline dark:text-yellow-300">terms and
                            conditions</a>.</label>
                </div>
                <div class="invisible">
                    <input type="hidden" value="{{ $logs->user_id }}" name="user_id">
                </div>
                <button type="submit" style="cursor: pointer"
                    class="text-white hover:text-yellow-400 border border-yellow-400 hover:bg-inherit focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium text-sm px-5 py-2 text-center  dark:border-yellow-300 dark:text-white dark:hover:text-yellow-300 dark:hover: ring-yellow-900 dark:focus: bg-yellow-400">SUBMIT</button>
            </form>






        </div>
    </div>


</x-app-layout>
