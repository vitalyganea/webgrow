<x-admin.layouts.auth title="Create Script">
    <x-slot:header>
        Create a New Script
    </x-slot:header>

    <x-admin.card>
        <x-slot:header>
            <x-slot:title>Script Information</x-slot:title>
            <x-slot:description>Add custom scripts to your website (JavaScript, analytics, tracking codes, etc.).</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            <form method="POST" action="{{ route('admin.store.script') }}">
                @csrf

                <div>
                    <x-admin.label for="name" value="Script Name" />
                    <x-admin.input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        value="{{ old('name') }}"
                        placeholder="e.g., Google Analytics, Custom JS"
                        required
                    />
                    <x-admin.input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div class="mt-4">
                    <x-admin.label for="description" value="Description (Optional)" />
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Brief description of what this script does"
                    >{{ old('description') }}</textarea>
                    <x-admin.input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <div class="mt-4">
                    <x-admin.label for="type" value="Script Type" />
                    <select
                        id="type"
                        name="type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required
                        onchange="toggleContentLabel()"
                    >
                        <option value="">Select Type</option>
                        <option value="inline" {{ old('type') == 'inline' ? 'selected' : '' }}>Inline Script</option>
                        <option value="external" {{ old('type') == 'external' ? 'selected' : '' }}>External Script URL</option>
                    </select>
                    <x-admin.input-error class="mt-2" :messages="$errors->get('type')" />
                </div>

                <div class="mt-4">
                    <x-admin.label for="position" value="Script Position" />
                    <select
                        id="position"
                        name="position"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required
                    >
                        <option value="">Select Position</option>
                        <option value="head" {{ old('position') == 'head' ? 'selected' : '' }}>Head (loads first, before content)</option>
                        <option value="body_top" {{ old('position') == 'body_top' ? 'selected' : '' }}>Body Top (after body opens)</option>
                        <option value="body_bottom" {{ old('position') == 'body_bottom' ? 'selected' : '' }}>Body Bottom (before body closes - recommended)</option>
                    </select>
                    <x-admin.input-error class="mt-2" :messages="$errors->get('position')" />
                </div>

                <div class="mt-4">
                    <x-admin.label for="content" value="" id="content-label">Script Content</x-admin.label>
                    <textarea
                        id="content"
                        name="content"
                        rows="8"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"
                        placeholder="Enter your JavaScript code or external script URL"
                        required
                    >{{ old('content') }}</textarea>
                    <x-admin.input-error class="mt-2" :messages="$errors->get('content')" />
                    <p class="mt-1 text-sm text-gray-500" id="content-help">
                        For inline scripts, enter JavaScript code. For external scripts, enter the full URL (e.g., https://example.com/script.js)
                    </p>
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <x-admin.button>Create Script</x-admin.button>
                    <a href="{{ route('admin.get.scripts') }}" class="text-gray-600 hover:text-gray-800 transition-colors">Cancel</a>
                    <x-admin.action-message status="script-created" />
                </div>
            </form>
        </x-slot:content>
    </x-admin.card>

    <script>
        function toggleContentLabel() {
            const type = document.getElementById('type').value;
            const label = document.getElementById('content-label');
            const help = document.getElementById('content-help');
            const content = document.getElementById('content');

            if (type === 'inline') {
                label.textContent = 'JavaScript Code';
                content.placeholder = 'Enter your JavaScript code here...\n\n// Example:\nconsole.log("Hello World!");';
                help.textContent = 'Enter your JavaScript code. Do not include <script> tags.';
            } else if (type === 'external') {
                label.textContent = 'Script URL';
                content.placeholder = 'https://example.com/script.js';
                help.textContent = 'Enter the full URL to the external JavaScript file.';
            } else {
                label.textContent = 'Script Content';
                content.placeholder = 'Enter your JavaScript code or external script URL';
                help.textContent = 'For inline scripts, enter JavaScript code. For external scripts, enter the full URL.';
            }
        }
    </script>
</x-admin.layouts.auth>
