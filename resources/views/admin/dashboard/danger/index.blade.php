<x-admin.layouts.auth title="Delete Account">
    <x-slot:header>
        Account Deletion
    </x-slot:header>
    <x-admin.settings-tabs class="max-w-xl">
        <x-admin.card>
            <x-slot:header>
                <x-slot:title>
                    Delete Account
                </x-slot:title>
                <x-slot:description>
                    Once your account is deleted, all of its resources and data will be permanently deleted. Before
                    deleting your account, please download any data or information that you wish to retain.
                </x-slot:description>
            </x-slot:header>
            <x-slot:content>
                <x-admin.button id="delete-account-btn" variant="destructive">
                    {{ __('Delete Account') }}
                </x-admin.button>

                <!-- Hidden Deletion Form -->
                <form method="POST" action="{{ route('admin.settings.danger') }}" id="hidden-delete-form" class="hidden">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="password" id="hidden-delete-password" />
                </form>
            </x-slot:content>
        </x-admin.card>
    </x-admin.settings-tabs>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const deleteAccountBtn = document.getElementById('delete-account-btn');

            deleteAccountBtn.addEventListener('click', () => {
                Swal.fire({
                    title: '{{ __('Are you sure you want to delete your account?') }}',
                    html: `
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                    <div>
                        <label for="password" class="sr-only">{{ __('Password') }}</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="border border-gray-300 rounded-md p-2 w-full"
                                placeholder="{{ __('Password') }}"
                            />
                            <p id="password-error" class="text-red-500 text-sm mt-2 hidden"></p>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '{{ __('Delete Account') }}',
                    cancelButtonText: '{{ __('Cancel') }}',
                    focusConfirm: false,
                    buttonsStyling: false,
                    customClass: {
                        actions: 'flex justify-end gap-4 mt-6',
                        confirmButton: 'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 ml-3',
                        cancelButton: 'inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150'
                    },
                    didOpen: () => {
                        const passwordInput = Swal.getPopup().querySelector('#password');
                        const confirmButton = Swal.getConfirmButton();
                        confirmButton.disabled = true;

                        passwordInput.addEventListener('input', () => {
                            confirmButton.disabled = !passwordInput.value.trim();
                        });
                    },
                    preConfirm: () => {
                        const password = Swal.getPopup().querySelector('#password').value;
                        if (!password) {
                            Swal.showValidationMessage('Please enter your password');
                            return false;
                        }
                        return { password };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const hiddenForm = document.getElementById('hidden-delete-form');
                        const hiddenPassword = document.getElementById('hidden-delete-password');
                        hiddenPassword.value = result.value.password;
                        hiddenForm.submit();
                    }
                });
            });
        });
    </script>
</x-admin.layouts.auth>
