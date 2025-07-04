<x-admin.layouts.auth title="Content Backups">
    <x-slot:header>
        All Content Backups
    </x-slot:header>
    <x-admin.card>
        <x-slot:content>

            <div class="text-right">
                <button id="createContentBackup"
                        class="mb-4 mt-4 inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 h-9 px-4 py-2 bg-primary text-primary-foreground shadow hover:bg-primary/80 focus-visible:bg-primary/90">
                    + Create a content backup
                </button>
            </div>

            <div class="overflow-x-auto bg-card rounded shadow border border-border">
                <table class="w-full table-auto">
                    <thead class="bg-accent text-left text-sm font-semibold text-accent-foreground">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Created AT</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="text-sm text-foreground">
                    @forelse ($contentBackups as $contentBackup)
                        <tr class="border-t border-border hover:bg-accent/50 transition-colors">
                            <td class="px-4 py-2">{{ $contentBackup->id }}</td>
                            <td class="px-4 py-2">{{ $contentBackup->created_at }}</td>
                            <td class="px-4 py-2 space-x-2">
                                <button type="button" class="text-primary hover:text-primary/80 hover:underline transition-colors restore-btn" data-id="{{ $contentBackup->id }}">
                                    Restore
                                </button>
                                <form action="{{ route('admin.delete.content-backup', $contentBackup) }}" method="POST" class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="password" class="delete-password">
                                    <button type="button" class="text-destructive hover:text-destructive/80 hover:underline transition-colors delete-btn" data-id="{{ $contentBackup->id }}">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-muted-foreground">No backups found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $contentBackups->links() }}
            </div>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Create Backup AJAX
                    const createBackupButton = document.getElementById('createContentBackup');
                    createBackupButton.addEventListener('click', function () {
                        Swal.fire({
                            title: 'Confirm Create Backup',
                            text: 'Please enter your password to create a backup.',
                            icon: 'info',
                            input: 'password',
                            inputLabel: 'Your Password',
                            inputPlaceholder: 'Enter your password',
                            inputAttributes: {
                                autocapitalize: 'off',
                                autocorrect: 'off'
                            },
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Verify and Create',
                            cancelButtonText: 'Cancel',
                            showLoaderOnConfirm: true,
                            preConfirm: (password) => {
                                if (!password) {
                                    Swal.showValidationMessage('Password is required');
                                }
                                return password;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Creating Backup',
                                    text: 'Please wait while the backup is being created...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                $.ajax({
                                    url: '{{ route("admin.create.content-backup") }}',
                                    type: 'POST',
                                    data: { password: result.value },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function (response) {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: response.message || 'Backup created successfully!',
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    },
                                    error: function (xhr) {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: xhr.responseJSON?.message || 'An error occurred while creating the backup.',
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                });
                            }
                        });
                    });

                    // Restore Backup AJAX
                    const restoreButtons = document.querySelectorAll('.restore-btn');
                    restoreButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            const backupId = this.getAttribute('data-id');

                            Swal.fire({
                                title: 'Confirm Restore',
                                text: "This will overwrite existing data with the selected backup. Please enter your password to confirm.",
                                icon: 'warning',
                                input: 'password',
                                inputLabel: 'Your Password',
                                inputPlaceholder: 'Enter your password',
                                inputAttributes: {
                                    autocapitalize: 'off',
                                    autocorrect: 'off'
                                },
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Verify and Restore',
                                cancelButtonText: 'Cancel',
                                showLoaderOnConfirm: true,
                                preConfirm: (password) => {
                                    if (!password) {
                                        Swal.showValidationMessage('Password is required');
                                    }
                                    return password;
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    Swal.fire({
                                        title: 'Restoring Backup',
                                        text: 'Please wait while the backup is being restored...',
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });

                                    $.ajax({
                                        url: '{{ route("admin.restore.content-backup") }}',
                                        type: 'POST',
                                        data: {
                                            backup_id: backupId,
                                            password: result.value
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function (response) {
                                            Swal.fire({
                                                title: 'Success!',
                                                text: response.message || 'Backup restored successfully!',
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                            }).then(() => {
                                                window.location.reload();
                                            });
                                        },
                                        error: function (xhr) {
                                            Swal.fire({
                                                title: 'Error!',
                                                text: xhr.responseJSON?.message || 'An error occurred while restoring the backup.',
                                                icon: 'error',
                                                confirmButtonText: 'OK'
                                            });
                                        }
                                    });
                                }
                            });
                        });
                    });

                    // Delete Backup AJAX
                    const deleteButtons = document.querySelectorAll('.delete-btn');
                    deleteButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            const form = this.closest('form');
                            const backupId = this.getAttribute('data-id');

                            Swal.fire({
                                title: 'Confirm Delete',
                                text: "You won't be able to revert this! Please enter your password to confirm.",
                                icon: 'warning',
                                input: 'password',
                                inputLabel: 'Your Password',
                                inputPlaceholder: 'Enter your password',
                                inputAttributes: {
                                    autocapitalize: 'off',
                                    autocorrect: 'off'
                                },
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Verify and Delete',
                                cancelButtonText: 'Cancel',
                                showLoaderOnConfirm: true,
                                preConfirm: (password) => {
                                    if (!password) {
                                        Swal.showValidationMessage('Password is required');
                                    }
                                    return password;
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Set password in hidden input and submit form
                                    form.querySelector('.delete-password').value = result.value;
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
