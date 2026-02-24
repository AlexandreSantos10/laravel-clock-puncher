<x-app-layout>
   

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg place-items-center">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-between">
                    <a href ="/createuser" type="button" class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-base text-sm px-4 py-2.5 text-center leading-5">Add User</a>
                
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
