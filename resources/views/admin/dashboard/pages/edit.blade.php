@php
    use Illuminate\Support\Facades\File;

    $blockFolders = collect(File::directories(public_path('custom')))
        ->map(fn($dir) => basename($dir));

    $languagesArray = $languages->map(fn($l) => ['code' => $l->code])->toArray();
    $defaultLang = $languages->first()->code ?? '';
    $pagesJson = json_encode($pages ?? []);
    $seoDataJson = json_encode($seoData ?? []);
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

                <!-- Buttons for Content and SEO -->
                <div class="mb-4 flex space-x-2">
                    <x-admin.button id="add-content-btn">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        <span class="ml-1">Add Content</span>
                    </x-admin.button>
                    <x-admin.button id="edit-seo-btn">
                        <i class="fa fa-tags" aria-hidden="true"></i>
                        <span class="ml-1">Edit SEO</span>
                    </x-admin.button>
                </div>

                <!-- Hidden SEO Form -->
                <form method="POST" action="{{ route('admin.update.seo', $group_id) }}" id="hidden-seo-form" class="hidden">
                    @csrf
                    <input type="hidden" name="language_code" id="hidden-seo-lang-code" />
                    <!-- SEO inputs will be dynamically added here -->
                </form>

                @foreach ($languages as $language)
                    <form method="POST" action="{{ route('admin.update.page', $group_id) }}" class="lang-section mt-6 space-y-6" data-lang="{{ $language->code }}" style="display: none;" id="page-form-{{ $language->code }}">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="pages[{{ $language->code }}][language_code]" value="{{ $language->code }}" />

                        <!-- Container for deleted blocks inputs -->
                        <div class="deleted-blocks-container" data-lang="{{ $language->code }}"></div>

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
                                        <span class="flex-grow">{{ config('admin.admin-static-text.' . $blockData['type'], ucfirst($blockData['type'])) }}</span>
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-chevron-right transition-transform duration-300 transform arrow text-gray-400"></i>
                                            <button type="button" class="delete-block-btn text-red-500 hover:text-red-700" data-block-id="{{ $blockId }}" data-lang="{{ $language->code }}">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </h4>
                                    <div class="accordion-body px-4 py-3 hidden bg-white transition-all duration-300">
                                        @if($blockData['type'] === 'html_template')
                                            <textarea id="editor-{{ $language->code }}-{{ $blockId }}" class="tinymce-editor w-full rounded border border-gray-300 p-2" name="pages[{{ $language->code }}][blocks][{{ $blockId }}][content]">{{ $blockData['content'] }}</textarea>
                                        @else
                                            <textarea id="editor-{{ $language->code }}-{{ $blockId }}" class="w-full rounded border border-gray-300 p-2" name="pages[{{ $language->code }}][blocks][{{ $blockId }}][content]">{{ $blockData['content'] }}</textarea>
                                        @endif
                                        <input type="hidden" name="pages[{{ $language->code }}][blocks][{{ $blockId }}][type]" value="{{ $blockData['type'] }}" />
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
        const seoData = {!! $seoDataJson !!};
        const defaultLang = @json($defaultLang);
        const groupId = @json($group_id);

        const globalCssFiles = @json(
            collect(File::files(public_path('assets/css')))
                ->filter(fn($file) => $file->getExtension() === 'css')
                ->map(fn($file) => '/assets/css/' . $file->getBasename())
                ->prepend('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css')
                ->values()
                ->toArray()
        );

        function showLangSection(langCode) {
            document.querySelectorAll('.lang-section').forEach(section => {
                section.style.display = section.dataset.lang === langCode ? 'block' : 'none';
            });
        }

        function addTextBlock(langCode, content = '') {
            const pageForm = document.querySelector(`#page-form-${langCode}`);
            if (!pageForm) {
                console.error(`No element found with ID page-form-${langCode}`);
                return;
            }

            const blockId = 'new_' + Date.now().toString();
            const index = pageForm.querySelectorAll('.accordion').length;

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
            wrapper.setAttribute('data-block-type', 'text');

            wrapper.innerHTML = `
                <h4 class="drag-handle accordion-header flex justify-between items-center font-semibold cursor-grab px-4 py-3 bg-gray-50 hover:bg-gray-100 transition">
                    <i class="fas fa-grip-vertical text-gray-400 mr-2"></i>
                    <span class="flex-grow">Text</span>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-chevron-right transition-transform duration-300 transform arrow text-gray-400"></i>
                        <button type="button" class="delete-block-btn text-red-500 hover:text-red-700" data-block-id="${blockId}" data-lang="${langCode}">
                            <i class="fas fa-trash" aria-hidden="true"></i>
                        </button>
                    </div>
                </h4>
                <div class="accordion-body px-4 py-3 hidden bg-white transition-all duration-300">
                    <textarea
                        id="editor_${langCode}_${blockId}"
                        class="w-full rounded border border-gray-300 p-2"
                        name="pages[${langCode}][blocks][${blockId}][content]"
                        rows="10"
                    >${content}</textarea>
                    <input
                        type="hidden"
                        name="pages[${langCode}][blocks][${blockId}][type]"
                        value="text"
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
                const saveButtonContainer = document.getElementById(`save-button-container-${langCode}`);
                pageForm.insertBefore(wrapper, saveButtonContainer);
            }

            initAccordionToggle();
            initDeleteButtons();
            initSortableForSection(langCode);
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

                    const blockId = 'new_' + Date.now().toString();
                    const index = pageForm.querySelectorAll('.accordion').length;

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
                        <h4 class="drag-handle accordion-header flex justify-between items-center font-semibold cursor-grab px-4 py-3 bg-gray-50 hover:bg-gray-100 transition">
                            <i class="fas fa-grip-vertical text-gray-400 mr-2"></i>
                            <span class="flex-grow">{{ config('admin.admin-static-text.html_template') }}</span>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-chevron-right transition-transform duration-300 transform arrow text-gray-400"></i>
                                <button type="button" class="delete-block-btn text-red-500 hover:text-red-700" data-block-id="${blockId}" data-lang="${langCode}">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
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
                        const saveButtonContainer = document.getElementById(`save-button-container-${langCode}`);
                        pageForm.insertBefore(wrapper, saveButtonContainer);
                    }

                    setTimeout(() => {
                        tinymce.init({
                            selector: `#editor_${langCode}_${blockId}`,
                            plugins: 'fullscreen link image code lists autoresize advcolor',
                            toolbar: 'undo redo | styles | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code | fullscreen',
                            content_css: globalCssFiles,
                            autoresize_bottom_margin: 10,
                            valid_elements: '*[*]',
                            setup: editor => {
                                editor.on('change', () => editor.save());
                            }
                        });
                        initAccordionToggle();
                        initStaticEditors();
                        initDeleteButtons();
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
                            valid_elements: '*[*]',
                            automatic_uploads: true,
                            images_file_types: 'jpg,jpeg,png,gif,webp',
                            images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                                const xhr = new XMLHttpRequest();
                                xhr.open('POST', '/admin/upload-image', true);
                                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

                                xhr.upload.onprogress = (e) => {
                                    if (e.lengthComputable) {
                                        progress((e.loaded / e.total) * 100);
                                    }
                                };

                                xhr.onload = () => {
                                    if (xhr.status === 200) {
                                        try {
                                            const json = JSON.parse(xhr.responseText);
                                            if (json && typeof json.location === 'string') {
                                                resolve(json.location);
                                            } else {
                                                reject('Invalid JSON response: ' + xhr.responseText);
                                            }
                                        } catch (e) {
                                            reject('Error parsing JSON: ' + e);
                                        }
                                    } else {
                                        reject('HTTP Error: ' + xhr.status);
                                    }
                                };

                                xhr.onerror = () => {
                                    reject('Image upload failed due to a network error.');
                                };

                                const formData = new FormData();
                                formData.append('file', blobInfo.blob(), blobInfo.filename());
                                xhr.send(formData);
                            }),
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
            if (event.target.classList.contains('delete-block-btn') || event.target.closest('.delete-block-btn')) {
                return;
            }
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

        function initDeleteButtons() {
            document.querySelectorAll('.delete-block-btn').forEach(button => {
                button.removeEventListener('click', handleDeleteBlock);
                button.addEventListener('click', handleDeleteBlock);
            });
        }

        function addDeletedBlockInput(langCode, blockId) {
            const form = document.querySelector(`#page-form-${langCode}`);
            const deletedBlocksContainer = form.querySelector('.deleted-blocks-container');

            // Create a new input for this deleted block
            const deletedBlockInput = document.createElement('input');
            deletedBlockInput.type = 'hidden';
            deletedBlockInput.name = `pages[${langCode}][deleted_blocks][]`;
            deletedBlockInput.value = blockId;
            deletedBlockInput.className = 'deleted-block-input';

            deletedBlocksContainer.appendChild(deletedBlockInput);
        }

        function handleDeleteBlock(event) {
            const button = event.currentTarget;
            const blockId = button.dataset.blockId;
            const langCode = button.dataset.lang;

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will delete the content block. This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    const accordion = button.closest('.accordion');
                    if (accordion) {
                        // Remove TinyMCE editor if it exists
                        const textarea = accordion.querySelector('textarea');
                        if (textarea && accordion.dataset.blockType === 'html_template') {
                            const editor = tinymce.get(textarea.id);
                            if (editor) {
                                editor.remove();
                            }
                        }

                        // Check if blockId is from database (doesn't start with 'new_')
                        if (!blockId.startsWith('new_')) {
                            addDeletedBlockInput(langCode, blockId);
                        }

                        accordion.remove();

                        // Update block order inputs
                        const langSection = document.querySelector(`.lang-section[data-lang="${langCode}"]`);
                        const accordions = langSection.querySelectorAll('.accordion');
                        accordions.forEach((acc, index) => {
                            const input = acc.querySelector('.block-order-input');
                            if (input) input.value = index;
                        });

                        Swal.fire({
                            title: 'Deleted!',
                            text: 'The content block has been removed.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    }
                }
            });
        }

        function loadSeoData(langCode) {
            return new Promise((resolve) => {
                const seoInputs = [];
                Object.keys(seoData[langCode] || {}).forEach(tag => {
                    seoInputs.push({
                        tag,
                        value: seoData[langCode][tag] || ''
                    });
                });
                resolve(seoInputs);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initStaticEditors();
            initAccordionToggle();
            initSortable();
            initDeleteButtons();

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

            const addContentBtn = document.getElementById('add-content-btn');
            const editSeoBtn = document.getElementById('edit-seo-btn');

            addContentBtn.addEventListener('click', () => {
                Swal.fire({
                    title: 'Select Content Type',
                    html: `
                        <div class="space-y-2">
                            <button class="content-type-btn w-full text-left px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md" data-type="html_template">HTML Template</button>
                            <button class="content-type-btn w-full text-left px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md" data-type="text">Text</button>
                        </div>
                        <div id="content-block-selection" class="mt-4 hidden">
                            <label class="block font-semibold mb-1">Select HTML Template</label>
                            <select id="html-file-select" class="border border-gray-300 rounded-md p-2 w-full">
                                <option value="" disabled selected>Select a template</option>
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
                <div id="text-block-selection" class="mt-4 hidden">
                    <label class="block font-semibold mb-1">Enter Text Content</label>
                    <textarea id="text-content" class="w-full rounded border border-gray-300 p-2" rows="5"></textarea>
                </div>
`,
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    focusConfirm: false,
                    didOpen: () => {
                        const contentTypeButtons = Swal.getPopup().querySelectorAll('.content-type-btn');
                        const htmlFileSelect = Swal.getPopup().querySelector('#html-file-select');
                        const textContent = Swal.getPopup().querySelector('#text-content');
                        const contentBlockSelection = Swal.getPopup().querySelector('#content-block-selection');
                        const textBlockSelection = Swal.getPopup().querySelector('#text-block-selection');
                        const confirmButton = Swal.getConfirmButton();
                        let selectedType = null;

                        contentTypeButtons.forEach(button => {
                            button.addEventListener('click', () => {
                                selectedType = button.dataset.type;
                                contentTypeButtons.forEach(btn => btn.classList.remove('bg-blue-100'));
                                button.classList.add('bg-blue-100');

                                if (selectedType === 'html_template') {
                                    contentBlockSelection.classList.remove('hidden');
                                    textBlockSelection.classList.add('hidden');
                                    htmlFileSelect.focus();
                                    confirmButton.disabled = !htmlFileSelect.value;
                                } else if (selectedType === 'text') {
                                    contentBlockSelection.classList.add('hidden');
                                    textBlockSelection.classList.remove('hidden');
                                    textContent.focus();
                                    confirmButton.disabled = !textContent.value.trim();
                                }
                            });
                        });

                        htmlFileSelect.addEventListener('change', () => {
                            confirmButton.disabled = !htmlFileSelect.value;
                        });

                        textContent.addEventListener('input', () => {
                            confirmButton.disabled = selectedType === 'text' && !textContent.value.trim();
                        });
                    },
                    preConfirm: () => {
                        const htmlFileSelect = Swal.getPopup().querySelector('#html-file-select');
                        const textContent = Swal.getPopup().querySelector('#text-content');
                        const selectedButton = Swal.getPopup().querySelector('.content-type-btn.bg-blue-100');
                        return {
                            type: selectedButton ? selectedButton.dataset.type : null,
                            value: selectedButton && selectedButton.dataset.type === 'html_template' ? htmlFileSelect.value : textContent.value
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value.type) {
                        if (result.value.type === 'html_template' && result.value.value) {
                            const [folder, file] = result.value.value.split('/');
                            loadHtmlContent(folder, file, currentLang);
                        } else if (result.value.type === 'text') {
                            addTextBlock(currentLang, result.value.value);
                        }
                    }
                });
            });

            editSeoBtn.addEventListener('click', async () => {
                const seoInputs = await loadSeoData(currentLang);
                let inputsHtml = `
                    <div class="space-y-4 text-left">
                `;
                seoInputs.forEach(input => {
                    inputsHtml += `
                        <div>
                            <label class="block font-semibold mb-1 capitalize">${input.tag}</label>
                            <input
                                type="text"
                                class="seo-input border border-gray-300 rounded-md p-2 w-full"
                                data-tag="${input.tag}"
                                value="${input.value}"
                            />
                        </div>
                    `;
                });
                inputsHtml += '</div>';

                Swal.fire({
                    title: 'Edit SEO Tags',
                    html: inputsHtml,
                    showCancelButton: true,
                    confirmButtonText: 'Save SEO',
                    cancelButtonText: 'Cancel',
                    focusConfirm: false,
                    preConfirm: () => {
                        const inputs = Swal.getPopup().querySelectorAll('.seo-input');
                        const hiddenForm = document.getElementById('hidden-seo-form');
                        const hiddenLangCode = document.getElementById('hidden-seo-lang-code');

                        hiddenForm.querySelectorAll('input[name^="seo"]').forEach(input => input.remove());
                        hiddenLangCode.value = currentLang;

                        inputs.forEach(input => {
                            const tag = input.dataset.tag;
                            const inputElement = document.createElement('input');
                            inputElement.type = 'hidden';
                            inputElement.name = `seo[${tag}]`;
                            inputElement.value = input.value;
                            hiddenForm.appendChild(inputElement);
                        });

                        hiddenForm.submit();
                    }
                });
            });
        });
    </script>
</x-admin.layouts.auth>
