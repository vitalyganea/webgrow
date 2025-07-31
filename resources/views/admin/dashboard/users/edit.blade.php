<x-admin.layouts.auth title="Edit User">
<x-slot:header>
        Edit User
    </x-slot:header>

    <x-admin.card>
        <x-slot:header>
            <x-slot:title>User Information</x-slot:title>
            <x-slot:description>Update the user details and profile information.</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div>
                    <x-admin.label for="name" value="Name" />
                    <x-admin.input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        value="{{ old('name', $user->name) }}"
                        required
                    />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div class="mt-4">
                    <x-admin.label for="email" value="Email" />
                    <x-admin.input
                        id="email"
                        name="email"
                        type="email"
                        class="mt-1 block w-full"
                        value="{{ old('email', $user->email) }}"
                        required
                    />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div class="mt-4">
                    <x-admin.label for="password" value="Password (leave blank to keep current)" />
                    <x-admin.input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full"
                    />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('password')" />
                </div>

                <div class="mt-4">
                    <x-admin.label for="password_confirmation" value="Confirm Password" />
                    <x-admin.input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                    />
                </div>

                <div class="mt-4">
                    <x-admin.label for="role_id" value="Role" />
                    <select
                        id="role_id"
                        name="role_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-admin.input-error class="mt-2" :messages="$errors->get('role_id')" />
                </div>

                <div class="mt-4">
                    <x-admin.label for="current_avatar" value="Current Avatar" />
                    <div class="mt-1">
                        <img src="{{ asset($user->avatar()) }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-full">
                    </div>
                </div>

                <div class="mt-4">
                    <x-admin.label for="avatar" value="New Avatar (optional)" />
                    <input
                        type="file"
                        name="avatar"
                        id="avatar"
                        accept="image/*"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('avatar')" />
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <x-admin.button>Update User</x-admin.button>
                    <x-admin.action-message status="user-updated" />
                </div>
            </form>
        </x-slot:content>
    </x-admin.card>
</x-admin.layouts.auth>
