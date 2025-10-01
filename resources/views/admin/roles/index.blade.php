<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Usuário</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Perfis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Atribuir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Remover</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                <div class="text-gray-500 dark:text-gray-400 text-sm">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @foreach($user->getRoleNames() as $role)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded mr-1 dark:bg-blue-900 dark:text-blue-100">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('roles.assign') }}" method="POST" class="flex gap-2 items-center">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <select name="role" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                        @foreach($roles as $role)
                                            @if($role->name === 'admin' && $user->id !== 1)
                                                @continue  {{-- pula o admin se não for usuário id 1 --}}
                                            @endif
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-sm">
                                        Atribuir
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('roles.remove') }}" method="POST" class="flex gap-2 items-center">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <select name="role" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                        @foreach($user->getRoleNames() as $role)
                                            <option value="{{ $role }}">{{ $role }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm">
                                        Remover
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

