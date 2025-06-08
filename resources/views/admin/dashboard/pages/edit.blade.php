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
                                    <div class="accordion border border-gray-300 rounded-md overflow-hidden" data-block-name="{{ $index }}">
                                        <h4 class="accordion-header flex justify-between items-center font-semibold cursor-pointer px-4 py-2 bg-gray-100 hover:bg-gray-200" data-toggle>
                                            <i class="fas fa-grip-vertical mr-2 cursor-move drag-handle"></i>
                                            {{ $index }}
                                            <i class="fas fa-chevron-right transition-transform duration-300 transform arrow"></i>
                                        </h4>
                                        <div class="accordion-body px-4 py-2 hidden">
                                            <textarea id="editor-{{ $language->code }}-{{ $index }}" class="tinymce-editor" name="pages[{{ $language->code }}][blocks][{{ $index }}]">{{ $blockContent }}</textarea>
                                        </div>
                                        <input type="hidden" name="pages[{{ $language->code }}][blocks_order][{{ $index }}]" class="block-order-input" value="{{ $loop->index }}" />
                                    </div>
                                @endforeach
                            @endif

                            <div class="blocks-container space-y-4" data-lang="{{ $language->code }}"></div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <script>
        const languages = @json($languagesArray);
        const oldPages = {!! $pagesJson !!};
        const defaultLang = @json($defaultLang);
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
                            wrapper.classList.add('accordion', 'border', 'border-gray-300', 'rounded-md', 'overflow-hidden', 'mb-2');
                            wrapper.setAttribute('data-block-name', block.name);
                            wrapper.innerHTML = `
                                <h4 class="accordion-header flex justify-between items-center font-semibold cursor-pointer px-4 py-2 bg-gray-100 hover:bg-gray-200" data-toggle>
                                    <i class="fas fa-grip-vertical mr-2 cursor-move drag-handle"></i>
                                    ${block.name}
                                    <i class="fas fa-chevron-right transition-transform duration-300 transform arrow"></i>
                                </h4>
                                <div class="accordion-body px-4 py-2 hidden">
                                    <textarea id="editor_${lang.code}_${index}" name="pages[${lang.code}][blocks][${block.name}]" rows="10">${content}</textarea>
                                </div>
                                <input type="hidden" name="pages[${lang.code}][blocks_order][${block.name}]" class="block-order-input" value="${index}" />
                            `;
                            container.appendChild(wrapper);
                        });
                    });

                    setTimeout(() => {
                        tinymce.remove();
                        initStaticEditors();
                        languages.forEach((lang) => {
                            data.blocks.forEach((block, i) => {
                                tinymce.init({
                                    selector: `#editor_${lang.code}_${i}`,
                                    plugins: 'fullscreen link image code lists autoresize',
                                    toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code | fullscreen',
                                    content_css: globalCssFiles,
                                    autoresize_bottom_margin: 10,
                                    setup: editor => {
                                        editor.on('change', () => editor.save());
                                    }
                                });
                            });
                        });
                        initAccordionToggle();
                        initSortable();
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
                content_css: globalCssFiles,
                autoresize_bottom_margin: 10
            });
        }

        function initAccordionToggle() {
            document.querySelectorAll('.accordion-header').forEach(header => {
                header.addEventListener('click', () => {
                    const body = header.nextElementSibling;
                    const arrow = header.querySelector('.arrow');
                    const isOpen = !body.classList.contains('hidden');

                    body.classList.toggle('hidden');
                    if (arrow) {
                        arrow.classList.toggle('rotate-90', !isOpen);
                    }
                });
            });
        }

        function initSortable() {
            document.querySelectorAll('.lang-section').forEach(langSection => {
                const container = langSection;
                new Sortable(container, {
                    handle: '.drag-handle',
                    animation: 150,
                    draggable: '.accordion',
                    onEnd: () => {
                        // Update hidden inputs order
                        const accordions = container.querySelectorAll('.accordion');
                        accordions.forEach((accordion, index) => {
                            const input = accordion.querySelector('.block-order-input');
                            if(input) input.value = index;
                        });
                    },
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initStaticEditors();
            initAccordionToggle();
            initSortable();

            const tabButtons = document.querySelectorAll('#language-tabs button');
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const lang = button.dataset.lang;
                    tabButtons.forEach(btn => btn.classList.remove('border-b-2', 'border-blue-600', 'text-blue-600'));
                    button.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
                    showLangSection(lang);
                });
            });

            showLangSection(defaultLang);

            document.getElementById('folder-select').addEventListener('change', e => {
                const folder = e.target.value;
                if (folder) {
                    loadBlocks(folder);
                } else {
                    document.querySelectorAll('.blocks-container').forEach(container => container.innerHTML = '');
                    tinymce.remove();
                    initStaticEditors();
                    initAccordionToggle();
                }
            });
        });
    </script>
</x-admin.layouts.auth>
