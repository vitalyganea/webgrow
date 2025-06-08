<x-admin.layouts.auth title="Pages">
    <x-slot:header>
        All Pages
    </x-slot:header>
    <x-admin.card>
        <x-slot:content>

        <div class="text-right">
        <a href="{{ route('admin.create.page') }}"
           class="mb-4 mt-4 inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 h-9 px-4 py-2 bg-primary text-primary-foreground shadow hover:bg-primary/80 focus-visible:bg-primary/90">
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

                        <form action="{{ route('admin.delete.page', ['group_id' => $page->group_id]) }}" method="POST" class="inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="text-red-600 hover:underline delete-btn">
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


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to delete all translations of this page!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete all!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
        </x-slot:content>
    </x-admin.card>
</x-admin.layouts.auth>
