<x-admin.layouts.auth title="SEO Tags">
    <x-slot:header>
        All SEO Tags
    </x-slot:header>

    <div class="mb-4 text-right">
        <a href="{{ route('admin.create.seo-tag') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + New SEO Tag
        </a>
    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="w-full table-auto">
            <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">SEO Tag</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
            @forelse ($seoTags as $tag)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $tag->id }}</td>
                    <td class="px-4 py-2">{{ Str::limit($tag->seo_tag, 80) }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="{{ route('admin.edit.seo-tag', $tag) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.delete.seo-tag', $tag) }}" method="POST" class="inline">
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
                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">No SEO tags found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $seoTags->links() }}
    </div>
</x-admin.layouts.auth>
