@php
    use Illuminate\Support\Facades\File;

    $blockFolders = collect(File::directories(public_path('custom')))
        ->map(fn($dir) => basename($dir));
@endphp

<x-auth-layout title="Edit Page">
    <x-slot:header>Edit Page</x-slot:header>

    <x-card>
        <x-slot:header>
            <x-slot:title>Page Information</x-slot:title>
            <x-slot:description>Edit HTML content for each language and block.</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            @if ($languages->isEmpty())
                <p class="text-red-500">No languages available. Please configure languages to edit pages.</p>
            @else
                <div x-data="pageEditor()" x-init="init()" class="space-y-6" x-cloak>

                    <!-- Language Tabs -->
                    <div class="flex border-b border-gray-200 mb-4">
                        <template x-for="language in languages" :key="language.code">
                            <button
                                @click.prevent="currentLang = language.code"
                                :class="currentLang === language.code ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
                                class="px-4 py-2 focus:outline-none"
                                type="button"
                                x-text="language.code.toUpperCase()"
                            ></button>
                        </template>
                    </div>

                    <!-- Folder selection -->
                    <div>
                        <label class="block font-semibold mb-1">Select Block Folder</label>
                        <select x-model="selectedFolder" @change="loadBlocks()" class="border border-gray-300 rounded-md p-2 w-full max-w-xs">
                            <option value="">-- Select Folder --</option>
                            @foreach ($blockFolders as $folder)
                                <option value="{{ $folder }}">{{ $folder }}</option>
                            @endforeach
                        </select>
                    </div>

                    <form method="POST" action="{{ route('admin.update.page', $group_id) }}">
                        @csrf
                        @method('PUT')

                        <!-- For each language -->
                        <template x-for="language in languages" :key="language.code">
                            <div x-show="currentLang === language.code" class="mt-6 space-y-6">

                                <input type="hidden" :name="`pages[${language.code}][language_code]`" :value="language.code" />

                                <div>
                                    <label class="block font-semibold" :for="`title_${language.code}`">Title</label>
                                    <input
                                        :id="`title_${language.code}`"
                                        :name="`pages[${language.code}][title]`"
                                        type="text"
                                        class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                                        :value="oldPages[language.code]?.title || ''"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block font-semibold mt-4" :for="`slug_${language.code}`">Slug (URL)</label>
                                    <input
                                        :id="`slug_${language.code}`"
                                        :name="`pages[${language.code}][slug]`"
                                        type="text"
                                        class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                                        :value="oldPages[language.code]?.slug || ''"
                                        required
                                    />
                                </div>

                                <!-- Multiple editors for blocks -->
                                <template x-if="blocks.length > 0">
                                    <template x-for="(block, index) in blocks" :key="block.filename">
                                        <div class="mt-6 border border-gray-300 rounded-md p-4">
                                            <h4 class="font-semibold mb-2" x-text="block.filename"></h4>

                                            <textarea
                                                :id="`editor_${language.code}_${index}`"
                                                :name="`pages[${language.code}][blocks][${block.filename}]`"
                                                rows="10"
                                                x-text="block.contents[language.code] ?? block.defaultContent"
                                            ></textarea>
                                        </div>
                                    </template>
                                </template>

                                <template x-if="blocks.length === 0 && selectedFolder">
                                    <p>No block HTML files found in this folder.</p>
                                </template>
                            </div>
                        </template>

                        <div class="mt-6">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </x-slot:content>
    </x-card>

    <script src="https://cdn.tiny.cloud/1/l5lkut73f99i5hjnces3j6nsovs11psgxagl1qoxewq3m4td/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        function pageEditor() {
            return {
                languages: @json($languages->map(fn($l) => ['code' => $l->code])),
                currentLang: @json($languages->first()->code ?? ''),
                selectedFolder: '',
                blocks: [], // array of {filename, defaultContent, contents:{lang:content}}
                oldPages: @json($pages ?? []),

                init() {
                    // Optionally load default folder and blocks
                },

                async loadBlocks() {
                    if (!this.selectedFolder) {
                        this.blocks = [];
                        return;
                    }

                    const url = `/admin/blocks/${this.selectedFolder}`;
                    try {
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Network response was not ok');

                        const data = await response.json();

                        // blocks: [{name, content}], css_files: [...]
                        // We'll prepare blocks structure, allowing per-language override
                        // For now, all languages get same content

                        this.blocks = data.blocks.map(block => ({
                            filename: block.name,
                            defaultContent: block.content,
                            contents: {} // You can extend to load saved content per language here
                        }));

                        // Destroy any old editors
                        tinymce.remove();

                        // Init editors after DOM updates
                        this.$nextTick(() => {
                            const cssFiles = data.css_files || [];

                            this.blocks.forEach((block, i) => {
                                this.languages.forEach(lang => {
                                    const selector = `#editor_${lang.code}_${i}`;

                                    tinymce.init({
                                        selector,
                                        height: 300,
                                        menubar: true,
                                        plugins: 'fullscreen lists link image table code',
                                        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
                                        content_css: cssFiles,
                                        setup: editor => {
                                            editor.on('change', () => editor.save());
                                        }
                                    });
                                });
                            });
                        });

                    } catch (error) {
                        console.error('Failed to load blocks:', error);
                        this.blocks = [];
                    }
                }
            }
        }
    </script>
</x-auth-layout>
