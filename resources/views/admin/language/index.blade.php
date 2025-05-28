<x-auth-layout title="Languages">
    <x-slot:header>
        All Languages
    </x-slot:header>

    <div class="mb-4 text-right">
        <a href="{{ route('admin.create.language') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + New Language
        </a>
    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="w-full table-auto">
            <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
            <tr>
                <th class="px-4 py-2">Title</th>
                <th class="px-4 py-2">Code</th>
                <th class="px-4 py-2">Main</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
            @forelse ($languages as $language)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $language->name }}</td>
                    <td class="px-4 py-2">{{ $language->code }}</td>
                    <td class="px-4 py-2">{{ $language->main ? 'Yes' : '' }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="{{ route('admin.edit.language', $language) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.delete.language', $language) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure?')" class="text-red-600 hover:underline">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">No languages found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $languages->links() }}
    </div>
</x-auth-layout>
