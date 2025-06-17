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
            <x-slot:description>Edit content for each language and block.</x-slot:description>
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

                <!-- Add Content Button -->
                <div class="mb-4">
                    <x-admin.button id="add-content-btn">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </x-admin.button>
                </div>

                <!-- Modal for Content Selection -->
                <div id="content-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4">Select Content Type</h3>
                        <div id="content-types" class="space-y-2">
                            <button class="content-type-btn w-full text-left px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md" data-type="html_template">HTML Template</button>
                            <!-- Future content types can be added here -->
                        </div>
                        <div id="content-block-selection" class="mt-4 hidden">
                            <label class="block font-semibold mb-1">Select HTML Template</label>
                            <select id="html-file-select" class="border border-gray-300 rounded-md p-2 w-full">
                                @foreach ($blockFolders as $folder)
                                    @php
                                        $htmlFiles = collect(File::files(public_path("custom/{$folder}")))
                                            ->filter(fn($file) => $file->getExtension() === 'html')
                                            ->map(fn($file) => $file->getBasename());
                                    @endphp
                                    @foreach ($htmlFiles as $file)
                                        <option value="{{ $folder }}/{{ $file }}">{{ $folder }}/{{ $file }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-4 flex justify-end space-x-2">
                            <button id="cancel-modal" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Cancel</button>
                            <button id="confirm-content" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600" disabled>Confirm</button>
                        </div>
                    </div>
                </div>

                @foreach ($languages as $language)
                    <form method="POST" action="{{ route('admin.update.page', $group_id) }}" class="lang-section mt-6 space-y-6" data-lang="{{ $language->code }}" style="display: none;" id="page-form-{{ $language->code }}">
                        @csrf
                        @method('PUT')

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
                            @foreach ($blockContents[$language->code] as $blockId => $blockData)
                                <div class="accordion border border-gray-300 overflow-hidden shadow-sm mb-4 transition-shadow duration-200" data-block-id="{{ $blockId }}" data-block-type="{{ $blockData['type'] }}">
                                    <h4 class="drag-handle accordion-header flex justify-between items-center font-semibold cursor-grab px-4 py-3 bg-gray-50 hover:bg-gray-100 transition">
                                        <i class="fas fa-grip-vertical text-gray-400 mr-2"></i>
                                        <span class="flex-grow">{{ config('admin.admin-static-text.' . $blockData['type']) }}</span>
                                        <i class="fas fa-chevron-right transition-transform duration-300 transform arrow text-gray-400"></i>
                                    </h4>
                                    <div class="accordion-body px-4 py-3 hidden bg-white transition-all duration-300">
                                        @if($blockData['type'] === 'html_template')
                                            <textarea id="editor-{{ $language->code }}-{{ $blockId }}" class="tinymce-editor w-full rounded border border-gray-300 p-2" name="pages[{{ $language->code }}][blocks][{{ $blockId }}][content]">{{ $blockData['content'] }}</textarea>
                                        @else
                                            <textarea id="editor-{{ $language->code }}-{{ $blockId }}" class="w-full rounded border border-gray-300 p-2" name="pages[{{ $language->code }}][blocks][{{ $blockId }}][content]">{{ $blockData['content'] }}</textarea>
                                        @endif
                                        <input type="hidden" name="pages[{{ $language->code }}][blocks][{{ $blockId }}][type]" value="{{$blockData['type']}}" />
                                    </div>
                                    <input type="hidden" name="pages[{{ $language->code }}][blocks_order][{{ $blockId }}]" class="block-order-input" value="{{ $loop->index }}" />
                                </div>
                            @endforeach
                        @endif

                        <div class="blocks-container space-y-4" data-lang="{{ $language->code }}"></div>

                        <div class="mt-6" id="save-button-container-{{ $language->code }}">
                            <x-admin.button>Save Changes</x-admin.button>
                        </div>
                    </form>
                @endforeach
            @endif
        </x-slot:content>
    </x-admin.card>

    <script src="https://cdn.tiny.cloud/1/{{env('TINY_EDITOR_API_KEY')}}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
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

        function loadHtmlContent(folder, file, langCode) {
            const url = `/custom/${folder}/${file}`;
            fetch(url)
                .then(response => response.text())
                .then(content => {
                    const pageForm = document.querySelector(`#page-form-${langCode}`);
                    if (!pageForm) {
                        console.error(`No element found with ID page-form-${langCode}`);
                        return;
                    }

                    const blockId = Date.now().toString();
                    const index   = pageForm.querySelectorAll('.accordion').length;

                    const wrapper = document.createElement('div');

                    wrapper.classList.add(
                        'accordion',
                        'border', 'border-gray-300',
                        'overflow-hidden',
                        'shadow-sm',
                        'mb-4',
                        'transition-shadow', 'duration-200'
                    );
                    wrapper.setAttribute('data-block-id', blockId);
                    wrapper.setAttribute('data-block-type', 'html_template');

                    wrapper.innerHTML = `
                      <h4 class="drag-handle accordion-header flex justify-between items-center font-semibold cursor-grab px-4 py-3 bg-gray-50 hover:bg-gray-100 transition" data-toggle>
                          <i class="fas fa-grip-vertical text-gray-400 mr-2"></i>
                          <span class="flex-grow">{{ config('admin.admin-static-text.html_template') }}</span>
                        <i class="fas fa-chevron-right transition-transform duration-300 transform arrow text-gray-400"></i>
                      </h4>
                      <div class="accordion-body px-4 py-3 hidden bg-white transition-all duration-300">
                        <textarea
                          id="editor_${langCode}_${blockId}"
                          class="tinymce-editor w-full rounded border border-gray-300 p-2"
                          name="pages[${langCode}][blocks][${blockId}][content]"
                          rows="10"
                        >${content}</textarea>
                        <input
                          type="hidden"
                          name="pages[${langCode}][blocks][${blockId}][type]"
                          value="html_template"
                        />
                      </div>
                      <input
                        type="hidden"
                        name="pages[${langCode}][blocks_order][${blockId}]"
                        class="block-order-input"
                        value="${index}"
                      />
                    `;

                    const accordions = pageForm.getElementsByClassName('accordion');
                    const lastAccordion = accordions.length > 0 ? accordions[accordions.length - 1] : null;
                    if (lastAccordion) {
                        lastAccordion.insertAdjacentElement('afterend', wrapper);
                    } else {
                        pageForm.appendChild(wrapper);
                    }

                    setTimeout(() => {
                        tinymce.init({
                            selector: `#editor_${langCode}_${blockId}`,
                            plugins: 'fullscreen link image code lists autoresize advcolor',
                            toolbar: 'undo redo | styles | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code | fullscreen',
                            content_css: globalCssFiles,
                            autoresize_bottom_margin: 10,
                            setup: editor => {
                                editor.on('change', () => editor.save());
                            }
                        });
                        initAccordionToggle();
                        initStaticEditors();
                        initSortableForSection(langCode);
                    }, 200);
                })
                .catch(err => {
                    console.error('Failed to load HTML content:', err);
                });
        }

        function initStaticEditors() {
            document.querySelectorAll('textarea.tinymce-editor').forEach(textarea => {
                const accordion = textarea.closest('.accordion');
                if (accordion && accordion.dataset.blockType === 'html_template') {
                    const editorId = textarea.id;
                    const editor = tinymce.get(editorId);
                    if (!editor) {
                        tinymce.init({
                            selector: `#${editorId}`,
                            plugins: 'fullscreen link image code lists autoresize advcolor',
                            toolbar: 'undo redo | styles | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code | fullscreen',
                            content_css: globalCssFiles,
                            autoresize_bottom_margin: 10,
                            setup: editor => {
                                editor.on('change', () => editor.save());
                            }
                        });
                    }
                }
            });
        }

        function initAccordionToggle() {
            document.querySelectorAll('.accordion-header').forEach(header => {
                header.removeEventListener('click', toggleAccordion);
                header.addEventListener('click', toggleAccordion);
            });
        }

        function toggleAccordion(event) {
            const header = event.currentTarget;
            const body = header.nextElementSibling;
            const arrow = header.querySelector('.arrow');
            const isOpen = !body.classList.contains('hidden');

            body.classList.toggle('hidden');
            if (arrow) {
                arrow.classList.toggle('rotate-90', !isOpen);
            }
        }

        function initSortableForSection(langCode) {
            const langSection = document.querySelector(`.lang-section[data-lang="${langCode}"]`);
            if (langSection) {
                new Sortable(langSection, {
                    handle: '.drag-handle',
                    animation: 150,
                    draggable: '.accordion',
                    onStart: () => {
                        tinymce.get().forEach(editor => editor.save());
                    },
                    onEnd: () => {
                        const accordions = langSection.querySelectorAll('.accordion');
                        accordions.forEach((accordion, index) => {
                            const input = accordion.querySelector('.block-order-input');
                            if (input) input.value = index;

                            const textarea = accordion.querySelector('textarea');
                            if (textarea && accordion.dataset.blockType === 'html_template') {
                                const editor = tinymce.get(textarea.id);
                                if (editor) {
                                    editor.remove();
                                }
                            }

                        });

                        initStaticEditors();
                    },
                });

            }
        }

        function initSortable() {
            languages.forEach(lang => {
                initSortableForSection(lang.code);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initStaticEditors();
            initAccordionToggle();
            initSortable();

            let currentLang = defaultLang;

            const tabButtons = document.querySelectorAll('#language-tabs button');
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const lang = button.dataset.lang;
                    currentLang = lang;
                    tabButtons.forEach(btn => btn.classList.remove('border-b-2', 'border-blue-600', 'text-blue-600'));
                    button.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
                    showLangSection(lang);
                });
            });

            showLangSection(defaultLang);

            const modal = document.getElementById('content-modal');
            const addContentBtn = document.getElementById('add-content-btn');
            const cancelModalBtn = document.getElementById('cancel-modal');
            const confirmContentBtn = document.getElementById('confirm-content');
            const contentTypeButtons = document.querySelectorAll('.content-type-btn');
            const htmlFileSelect = document.getElementById('html-file-select');
            const contentBlockSelection = document.getElementById('content-block-selection');

            addContentBtn.addEventListener('click', () => {
                modal.classList.remove('hidden');
            });

            cancelModalBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
                contentBlockSelection.classList.add('hidden');
                htmlFileSelect.value = '';
                confirmContentBtn.disabled = true;
            });

            contentTypeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const type = button.dataset.type;
                    contentTypeButtons.forEach(btn => btn.classList.remove('bg-blue-100'));
                    button.classList.add('bg-blue-100');

                    if (type === 'html_template') {
                        contentBlockSelection.classList.remove('hidden');
                        htmlFileSelect.focus();
                    } else {
                        contentBlockSelection.classList.add('hidden');
                    }
                });
            });

            htmlFileSelect.addEventListener('change', () => {
                confirmContentBtn.disabled = !htmlFileSelect.value;
            });

            confirmContentBtn.addEventListener('click', () => {
                const selectedFile = htmlFileSelect.value;
                if (selectedFile) {
                    const [folder, file] = selectedFile.split('/');
                    loadHtmlContent(folder, file, currentLang);
                    modal.classList.add('hidden');
                    contentBlockSelection.classList.add('hidden');
                    htmlFileSelect.value = '';
                    confirmContentBtn.disabled = true;
                    initStaticEditors();
                }
            });
        });
    </script>
</x-admin.layouts.auth>
