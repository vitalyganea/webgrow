<x-admin.layouts.auth title="User Management">
    <x-slot:header>
        All Users
    </x-slot:header>
    <x-admin.card>
        <x-slot:content>
            <div class="text-right">
                <a href="{{ route('admin.users.create') }}"
                   class="mb-4 mt-4 inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 h-9 px-4 py-2 bg-primary text-primary-foreground shadow hover:bg-primary/80 focus-visible:bg-primary/90">
                    + Add New User
                </a>
            </div>

            <div class="overflow-x-auto bg-card rounded shadow border border-border">
                <table class="w-full table-auto">
                    <thead class="bg-accent text-left text-sm font-semibold text-accent-foreground">
                    <tr>
                        <th class="px-4 py-2">Avatar</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Role</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="text-sm text-foreground">
                    @forelse ($users as $user)
                        <tr class="border-t border-border hover:bg-accent/50 transition-colors">
                            <td class="px-4 py-2">
                                <img class="h-8 w-8 rounded-full" src="{{ asset($user->avatar()) }}" alt="{{ $user->name }}">
                            </td>
                            <td class="px-4 py-2">{{ $user->name }}</td>
                            <td class="px-4 py-2">{{ $user->email }}</td>
                            <td class="px-4 py-2">{{ $user->role->name ?? 'No Role' }}</td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-primary hover:text-primary/80 hover:underline transition-colors">Edit</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline delete-form">
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
                            <td colspan="5" class="px-4 py-4 text-center text-muted-foreground">No users found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
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
