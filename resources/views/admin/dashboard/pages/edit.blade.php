@php
    use Illuminate\Support\Facades\File;

    $blockFolders = collect(File::directories(public_path('custom')))
        ->map(fn($dir) => basename($dir));

    $languagesArray = $languages->map(fn($l) => ['code' => $l->code])->toArray();

    $defaultLang = $languages->first()->code ?? '';
    $pagesJson = json_encode($pages ?? []);
@endphp

<x-admin.layouts.auth title="Edit Page">
    <x-slot:header>Edit Page</x-slot:header>

    <x-admin.card>
        <x-slot:header>
            <x-slot:title>Page Information</x-slot:title>
            <x-slot:description>Edit HTML content for each language and block.</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            @if ($languages->isEmpty())
                <p class="text-red-500">No languages available. Please configure languages to edit pages.</p>
            @else
                <!-- Language Tabs -->
                <div class="flex border-b border-gray-200 mb-4" id="language-tabs">
                    @foreach ($languages as $index => $language)
                        <button
                            type="button"
                            class="px-4 py-2 focus:outline-none text-gray-600 {{ $loop->first ? 'text-blue-600 border-b-2 border-blue-600' : '' }}"
                            data-lang="{{ $language->code }}"
                        >
                            {{ strtoupper($language->code) }}
                        </button>
                    @endforeach
                </div>

                <!-- Folder selection -->
                <div>
                    <label class="block font-semibold mb-1">Select Block Folder</label>
                    <select id="folder-select" class="border border-gray-300 rounded-md p-2 w-full max-w-xs">
                        <option value="">-- Select Folder --</option>
                        @foreach ($blockFolders as $folder)
                            <option value="{{ $folder }}">{{ $folder }}</option>
                        @endforeach
                    </select>
                </div>

                <form method="POST" action="{{ route('admin.update.page', $group_id) }}" id="page-form">
                    @csrf
                    @method('PUT')

                    @foreach ($languages as $language)
                        <div class="lang-section mt-6 space-y-6" data-lang="{{ $language->code }}" style="display: none;">
                            <input type="hidden" name="pages[{{ $language->code }}][language_code]" value="{{ $language->code }}" />

                            <div>
                                <x-admin.label for="title_{{ $language->code }}" value="Title" />
                                <x-admin.input
                                    id="title_{{ $language->code }}"
                                    name="pages[{{ $language->code }}][title]"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('pages.' . $language->code . '.title', $pages[$language->code]['title'] ?? '') }}"
                                    required
                                />
                            </div>

                            <div>
                                <x-admin.label for="slug_{{ $language->code }}" value="Slug (URL)" />
                                <x-admin.input
                                    id="slug_{{ $language->code }}"
                                    name="pages[{{ $language->code }}][slug]"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('pages.' . $language->code . '.slug', $pages[$language->code]['slug'] ?? '') }}"
                                    required
                                />
                            </div>

                            @if(isset($blockContents[$language->code]) && is_array($blockContents[$language->code]))
                                @foreach ($blockContents[$language->code] as $index => $blockContent)
                                    <div class="mt-6 border border-gray-300 rounded-md p-4">
                                        <h4 class="font-semibold mb-2">{{ $index }}</h4>
                                        <textarea id="editor-{{ $language->code }}-{{ $index }}" class="tinymce-editor" name="pages[{{ $language->code }}][blocks][{{ $index }}]">
                                            {{ $blockContent }}
                                        </textarea>
                                    </div>
                                @endforeach
                            @endif
                            <!-- Blocks will be inserted here -->
                            <div class="blocks-container" data-lang="{{ $language->code }}"></div>
                        </div>
                    @endforeach

                    <div class="mt-6">
                        <x-admin.button>Save Changes</x-admin.button>
                    </div>
                </form>
            @endif
        </x-slot:content>
    </x-admin.card>

    <script src="https://cdn.tiny.cloud/1/l5lkut73f99i5hjnces3j6nsovs11psgxagl1qoxewq3m4td/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        const languages = @json($languagesArray);
        const oldPages = {!! $pagesJson !!};
        const defaultLang = @json($defaultLang);

        let currentLang = defaultLang;
        let globalCssFiles = '/custom/home/assets/css/main.css';

        function showLangSection(langCode) {
            document.querySelectorAll('.lang-section').forEach(section => {
                section.style.display = section.dataset.lang === langCode ? 'block' : 'none';
            });
        }

        function loadBlocks(folder) {
            const blocksUrl = `/admin/blocks/${folder}`;
            fetch(blocksUrl)
                .then(response => response.json())
                .then(data => {

                    document.querySelectorAll('.blocks-container').forEach(container => container.innerHTML = '');

                    languages.forEach((lang) => {
                        const container = document.querySelector(`.blocks-container[data-lang="${lang.code}"]`);
                        data.blocks.forEach((block, index) => {
                            const content = oldPages[lang.code]?.blocks?.[block.name] || block.content;

                            const wrapper = document.createElement('div');
                            wrapper.classList.add('mt-6', 'border', 'border-gray-300', 'rounded-md', 'p-4');
                            wrapper.innerHTML = `
                            <h4 class="font-semibold mb-2">${block.name}</h4>
                            <textarea id="editor_${lang.code}_${index}" name="pages[${lang.code}][blocks][${block.name}]" rows="10">${content}</textarea>
                        `;
                            container.appendChild(wrapper);
                        });
                    });

                    setTimeout(() => {
                        tinymce.remove();
                        initStaticEditors(); // Re-initialize static ones with new css_files

                        languages.forEach((lang) => {
                            data.blocks.forEach((block, i) => {
                                tinymce.init({
                                    selector: `#editor_${lang.code}_${i}`,
                                    plugins: 'fullscreen link image code lists autoresize',
                                    toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code | fullscreen',
                                    // Remove fixed height
                                    content_css: globalCssFiles,
                                    autoresize_bottom_margin: 10,
                                    setup: editor => {
                                        editor.on('change', () => editor.save());
                                    }
                                });
                            });
                        });
                    }, 200);
                })
                .catch(err => {
                    console.error('Failed to load blocks:', err);
                });
        }

        function initStaticEditors() {
            tinymce.init({
                selector: 'textarea.tinymce-editor',
                plugins: 'fullscreen link image code lists autoresize',
                toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code | fullscreen',
                // Remove fixed height
                content_css: globalCssFiles,
                autoresize_bottom_margin: 10
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Initialize static editors first
            initStaticEditors();

            // Language tab switching
            const tabButtons = document.querySelectorAll('#language-tabs button');
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    currentLang = button.dataset.lang;
                    tabButtons.forEach(btn => btn.classList.remove('border-b-2', 'border-blue-600', 'text-blue-600'));
                    button.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
                    showLangSection(currentLang);
                });
            });

            showLangSection(defaultLang);

            // Folder select handler
            document.getElementById('folder-select').addEventListener('change', e => {
                const folder = e.target.value;
                if (folder) {
                    loadBlocks(folder);
                } else {
                    document.querySelectorAll('.blocks-container').forEach(container => container.innerHTML = '');
                    tinymce.remove();
                    initStaticEditors(); // Fallback re-init
                }
            });
        });
    </script>

</x-admin.layouts.auth>
