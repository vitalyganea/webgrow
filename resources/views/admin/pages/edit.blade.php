@php use Illuminate\Support\Facades\File; @endphp
<x-auth-layout title="Edit Page">
    <x-slot:header>
        Edit Page
    </x-slot:header>

    <x-card>
        <x-slot:header>
            <x-slot:title>Page Information</x-slot:title>
            <x-slot:description>Edit the HTML content for each language tab.</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            @if ($languages->isEmpty())
                <p class="text-red-500">No languages available. Please configure languages to edit pages.</p>
            @else
                @php
                    $filePath = public_path('custom/index.html');

                    if (!File::exists($filePath)) {
                        File::put($filePath, '<p><!-- Start editing your page here --></p>');
                    }

                    $htmlContent = File::get($filePath);

                    // Get all .css and .scss files from public/custom and subdirectories
                    $cssFiles = [];
                    $allFiles = File::allFiles(public_path('custom'));

                    foreach ($allFiles as $file) {
                        if (in_array($file->getExtension(), ['css', 'scss'])) {
                            $relativePath = str_replace(public_path(), '', $file->getPathname());
                            $cssFiles[] = asset(ltrim($relativePath, '/'));
                        }
                    }
                @endphp

                <div x-data="{ tab: '{{ $languages->first()->code ?? '' }}' }" x-cloak class="space-y-6">
                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200">
                        @foreach ($languages as $language)
                            <button
                                @click="tab = '{{ $language->code }}'"
                                :class="tab === '{{ $language->code }}' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                                class="px-4 py-2 focus:outline-none"
                                type="button"
                            >
                                {{ strtoupper($language->code) }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Tab contents -->
                    <form method="POST">
                        @csrf

                        @foreach ($languages as $language)
                            @php $langCode = $language->code; @endphp

                            <div x-show="tab === '{{ $langCode }}'" x-cloak class="space-y-6">
                                <h3 class="text-lg font-semibold">{{ strtoupper($langCode) }} HTML Content</h3>

                                <input type="hidden" name="pages[{{ $langCode }}][language_code]" value="{{ $langCode }}">

                                <div>
                                    <x-label for="editor_{{ $langCode }}" value="HTML Content" />
                                    <textarea
                                        id="editor_{{ $langCode }}"
                                        name="pages[{{ $langCode }}][content]"
                                        rows="10"
                                    >{!! $htmlContent !!}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('pages.' . $langCode . '.content')" />
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-6 flex items-center gap-4">
                            <x-button type="submit">Save Changes</x-button>
                        </div>
                    </form>
                </div>
            @endif
        </x-slot:content>
    </x-card>

    <!-- TinyMCE CDN -->
    <script src="https://cdn.tiny.cloud/1/l5lkut73f99i5hjnces3j6nsovs11psgxagl1qoxewq3m4td/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @foreach ($languages as $language)
            tinymce.init({
                selector: '#editor_{{ $language->code }}',
                height: 500,
                menubar: true,
                plugins: 'fullscreen lists link image table code',
                toolbar: 'fullscreen | undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
                content_css: @json($cssFiles),
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
            @endforeach
        });
    </script>
</x-auth-layout>
