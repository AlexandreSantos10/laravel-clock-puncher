<x-app-layout>
    <div class="py-9">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">



            <form method="post" action="{{ route('createuser') }}">
                @csrf


                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label for="name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                        <x-text-input type="text" id="name" name="name"
                           placeholder="John Doe" required />
                    </div>
                    <div>
                        <label for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                        <x-text-input type="text" id="email" name="email"
                             placeholder="John@gmail.com" required />
                    </div>
                    <div>
                        <label for="password"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <x-text-input type="password" id="password" name="password"
                             placeholder="Password" required />
                    </div>


                    <div>
                        <label for="lunch" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lunch
                            Start</label>
                        <x-text-input type="time" id="lunch" name="lunch"
                            placeholder="13:00:00" required />
                    </div>
                    <div class="flex items-center mb-4">
                        <input checked id="default-radio-1" type="radio" value="user" name="type"
                            class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="default-radio-1"
                            class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">User</label>
                    </div>
                    <div class="flex items-center">
                        <input id="default-radio-2" type="radio" value="admin" name="type"
                            class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="default-radio-2"
                            class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Admin</label>
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
                <button type="submit" style="cursor: pointer"
                    class="text-white hover:text-yellow-400 border border-yellow-400 hover:bg-inherit focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium text-sm px-5 py-2 text-center  dark:border-yellow-300 dark:text-white dark:hover:text-yellow-300 dark:hover: ring-yellow-900 dark:focus: bg-yellow-400">SUBMIT</button>
            </form>






        </div>
    </div>


</x-app-layout>
