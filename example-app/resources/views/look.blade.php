<x-app-layout>


    <div class="bg-gray-800 min-h-screen">
        <div class="py-55">
            <div class="max-w-md mx-auto sm:px-4 lg:px-12">

                <div
                    class="max-w p-6 bg-white border border-yellow-400 shadow-sm dark:bg-gray-800 dark:border-yellow-400 text-center">

                    <p class="mb-3 font-normal text-gray-700 dark:text-white">Started at: {{ $logs->entrada }}</p>
                    <p class="mb-3 font-normal text-gray-700 dark:text-white">Left at: {{ $logs->saida }}</p>
                    <p class="mb-3 font-normal text-gray-700 dark:text-white"> Created by {{ $logs->created_by }}</p>
                    <p class="mb-3 font-normal text-gray-700 dark:text-white">{{ $logs->total_horas }} hours were done
                    </p>

                    <a href="/dashboard" class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">Back</a>



                </div>
            </div>

</x-app-layout>
