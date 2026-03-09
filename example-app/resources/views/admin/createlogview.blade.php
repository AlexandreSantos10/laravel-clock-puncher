<x-app-layout>
    @if (!empty($message))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <span class="font-medium">Danger alert!</span> {{ $message }}
        </div>
    @endif
    <div class="py-9">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            


            <form action="{{ route('createlog') }}" method="post">
                @csrf
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label for="user" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">User
                            Name</label>
                        <select id="" name ="user_id" value="{{ request('user_id')}}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-yellow-500 dark:focus:border-yellow-500">

                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date</label>
                        <x-text-input type="date" name="data" id="date" value="{{ request('data')}}"
                            required />
                    </div>
                    <div>
                        <label for="entry"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Entry</label>
                        <x-text-input type="time" id="entrada" min="08:00" value="{{ request('entrada')}}" name="entrada"
                           required />
                    </div>

                    <div>
                        <label for="left" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Left
                            At</label>
                        <x-text-input type="time" id="left" min="08:01" value="{{ request('saida')}}" name="saida" required />
                    </div>

                    <div>
                        <label for="obs" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Obs</label>
                        <x-text-input id="obs" type="text" name="obs" value="{{ request('obs')}}" required />
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
                    <input type="hidden" value="{{ $user->inicio_almoco }}" name="inicio">
                </div>
                <x-primary-app-button type="submit" style="cursor: pointer"
                    >SUBMIT</x-primary-app-button>
            </form>






        </div>
    </div>


</x-app-layout>
