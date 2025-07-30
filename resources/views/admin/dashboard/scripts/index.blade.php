<x-admin.layouts.auth title="Scripts">
    <x-slot:header>
        All Scripts
    </x-slot:header>
    <x-admin.card>
        <x-slot:content>

            <div class="text-right">
                <a href="{{ route('admin.create.script') }}"
                   class="mb-4 mt-4 inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 h-9 px-4 py-2 bg-primary text-primary-foreground shadow hover:bg-primary/80 focus-visible:bg-primary/90">
                    + New Script
                </a>
            </div>

            <div class="overflow-x-auto bg-card rounded shadow border border-border">
                <table class="w-full table-auto">
                    <thead class="bg-accent text-left text-sm font-semibold text-accent-foreground">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Position</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="text-sm text-foreground">
                    @forelse ($scripts as $script)
                        <tr class="border-t border-border hover:bg-accent/50 transition-colors">
                            <td class="px-4 py-2">{{ $script->id }}</td>
                            <td class="px-4 py-2">{{ $script->name }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                    {{ $script->type === 'inline' ? 'bg-blue-50 text-blue-700 ring-blue-600/20' : 'bg-green-50 text-green-700 ring-green-600/20' }}">
                                    {{ ucfirst($script->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                    {{ $script->position === 'head' ? 'bg-purple-50 text-purple-700 ring-purple-600/20' :
                                       ($script->position === 'body_top' ? 'bg-orange-50 text-orange-700 ring-orange-600/20' : 'bg-gray-50 text-gray-700 ring-gray-600/20') }}">
                                    {{ str_replace('_', ' ', ucfirst($script->position)) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('admin.edit.script', $script) }}" class="text-primary hover:text-primary/80 hover:underline transition-colors">Edit</a>
                                <form action="{{ route('admin.delete.script', $script) }}" method="POST" class="inline delete-form">
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
                            <td colspan="6" class="px-4 py-4 text-center text-muted-foreground">No scripts found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $scripts->links() }}
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
