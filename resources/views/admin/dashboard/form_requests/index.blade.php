<x-admin.layouts.auth title="Form Requests">
    <x-slot:header>
        All Form Requests
    </x-slot:header>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="w-full table-auto">
            <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">URL</th>
                <th class="px-4 py-2">Created at</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
            @forelse ($formRequests as $request)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $request->id }}</td>
                    <td class="px-4 py-2">{{ Str::limit($request->url, 50) }}</td>
                    <td class="px-4 py-2">{{ $request->created_at }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <button type="button" class="text-blue-600 hover:underline show-btn" data-request-body="{{ $request->requestBody }}" data-request-id="{{ $request->id }}">
                            Show
                        </button>
                        <form action="{{ route('admin.delete.form-request', $request) }}" method="POST" class="inline delete-form">
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
                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">No form requests found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $formRequests->links() }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle Delete Buttons
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

            // Handle Show Buttons
            const showButtons = document.querySelectorAll('.show-btn');
            showButtons.forEach(button => {
                button.addEventListener('click', function () {
                    let rawRequestBody = this.getAttribute('data-request-body');
                    const requestId = this.getAttribute('data-request-id');
                    try {
                        // Unescape HTML entities if present
                        rawRequestBody = rawRequestBody
                            .replace(/"/g, '"')
                            .replace(/'/g, "'");
                        const requestBody = JSON.parse(rawRequestBody);
                        let readableHtml = '';
                        for (const [key, value] of Object.entries(requestBody)) {
                            readableHtml += `<strong>${key}:</strong> ${value}<br>`;
                        }

                        Swal.fire({
                            title: 'Form Request Details',
                            html: readableHtml,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Mark as Seen',
                            cancelButtonText: 'Close',
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Send request to mark as seen
                                fetch("{{ route('admin.mark-seen.form-request', ':id') }}".replace(':id', requestId), {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        seen: 1
                                    })
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire({
                                                title: 'Success',
                                                text: 'Form request marked as seen!',
                                                icon: 'success',
                                                confirmButtonText: 'OK',
                                                confirmButtonColor: '#3085d6'
                                            });
                                        } else {
                                            throw new Error(data.message || 'Failed to mark as seen');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error marking as seen:', error);
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'Failed to mark form request as seen.',
                                            icon: 'error',
                                            confirmButtonText: 'OK',
                                            confirmButtonColor: '#3085d6'
                                        });
                                    });
                            }
                        });
                    } catch (e) {
                        console.error('Invalid JSON:', rawRequestBody, e);
                        Swal.fire({
                            title: 'Error',
                            text: 'Unable to parse form request details. Check console for details.',
                            icon: 'error',
                            confirmButtonText: 'Close',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            });
        });
    </script>
</x-admin.layouts.auth>
