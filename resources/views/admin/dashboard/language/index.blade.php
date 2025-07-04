<x-admin.layouts.auth title="Languages">
    <x-slot:header>
        All Languages
    </x-slot:header>
    <x-admin.card>
        <x-slot:content>

            <div class="text-right">
                <a href="{{ route('admin.create.language') }}"
                   class="mb-4 mt-4 inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 h-9 px-4 py-2 bg-primary text-primary-foreground shadow hover:bg-primary/80 focus-visible:bg-primary/90">
                    + New Language
                </a>
            </div>

            <div class="overflow-x-auto bg-card rounded shadow border border-border">
                <table class="w-full table-auto">
                    <thead class="bg-accent text-left text-sm font-semibold text-accent-foreground">
                    <tr>
                        <th class="px-4 py-2">Title</th>
                        <th class="px-4 py-2">Code</th>
                        <th class="px-4 py-2">Main</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="text-sm text-foreground">
                    @forelse ($languages as $language)
                        <tr class="border-t border-border hover:bg-accent/50 transition-colors">
                            <td class="px-4 py-2">{{ $language->name }}</td>
                            <td class="px-4 py-2">{{ $language->code }}</td>
                            <td class="px-4 py-2">{{ $language->main ? 'Yes' : '' }}</td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('admin.edit.language', $language) }}" class="text-primary hover:text-primary/80 hover:underline transition-colors">Edit</a>
                                <form action="{{ route('admin.delete.language', $language) }}" method="POST" class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-destructive hover:text-destructive/80 hover:underline transition-colors delete-btn">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-muted-foreground">No languages found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $languages->links() }}
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const deleteButtons = document.querySelectorAll('.delete-btn');

                    deleteButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            const form = this.closest('form');

                            Swal.fire({
                                title: 'Are you sure?',
                                text: "You won't be able to revert this!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Yes, delete it!',
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
