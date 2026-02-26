<x-app-layout>
   

    <div class="py-12">
        <div class=" max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg place-items-center">
                <div class=" w-full p-6 text-gray-900 dark:text-gray-100 flex justify-between sm:flex flex-wrap">
                    <div class=" flex justify-between">
                        <div >
                            <form action="userlist" method="get">
                            @csrf
                                <input type="text" name = "name" id="table-search" class="block p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 w-50 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search name">
                        </div>
                        <div>
                                <button type="submit" style="cursor: pointer" class="text-yellow-400 hover:text-white border border-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-#ebab13-300 dark:text-yellow-300 dark:hover:text-white dark:hover:bg-yellow-400 dark:focus:ring-yellow-900">SEARCH 
                                </button>
                            </form>  
                        </div>
                        <div class="py-2">
                        <a href="userlist">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        </a>
                        </div>

                    </div>
                <div class="py-2">
                    <a href ="/createuser" type="button" class="text-white hover:text-yellow-400 border border-yellow-400 hover:bg-inherit focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium text-sm px-5 py-2 text-center  dark:border-yellow-300 dark:text-white dark:hover:text-yellow-300 dark:hover: ring-yellow-900 dark:focus: bg-yellow-400">ADD</a>
            </div>
        </div>
              <div class="w-full relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                    
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
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 font-large text-gray-100">
                                    Lunch Start
                                </th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="bg-neutral-primary border-b border-default">
                                <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap text-gray-100">
                                    {{ $user->name }}
                                </th>
                                <td class="px-6 py-4 text-gray-100">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 text-gray-100">
                                    {{ $user->tipo }}
                                </td>
                                <td class="px-6 py-4 text-gray-100">
                                    {{ $user->inicio_almoco }}
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
