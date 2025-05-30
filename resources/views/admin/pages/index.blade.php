<x-auth-layout title="Pages">
    <x-slot:header>
        All Pages
    </x-slot:header>

    <div class="mb-4 text-right">
        <a href="{{ route('admin.create.page') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + New Page
        </a>
    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="w-full table-auto">
            <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
            <tr>
                <th class="px-4 py-2">Title</th>
                <th class="px-4 py-2">Created At</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
            @forelse ($pages as $page)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $page->title }}</td>
                    <td class="px-4 py-2">{{ $page->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-2 space-x-2">
                        {{-- Pass group_id for editing all translations --}}
                        <a href="{{ route('admin.edit.page', ['group_id' => $page->group_id]) }}" class="text-blue-600 hover:underline">Edit</a>

                        <form action="{{ route('admin.delete.page', ['group_id' => $page->group_id]) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete all translations of this page?')" class="text-red-600 hover:underline">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">No pages found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $pages->links() }}
    </div>
</x-auth-layout>
