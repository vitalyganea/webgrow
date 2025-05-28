<x-auth-layout title="Edit Page">
    <x-slot:header>Edit Page</x-slot:header>

    <x-card>
        <x-slot:header>
            <x-slot:title>Page Information</x-slot:title>
            <x-slot:description>Update the page title, slug, and content.</x-slot:description>
        </x-slot:header>

        <x-slot:content>
            <form method="POST" action="{{ route('admin.update.page', $page) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div>
                    <x-label for="title" value="Title" />
                    <x-input id="title" name="title" type="text" class="mt-1 block w-full"
                             value="{{ old('title', $page->title) }}" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                </div>

                {{-- Slug --}}
                <div class="mt-6">
                    <x-label for="slug" value="Slug (URL)" />
                    <x-input id="slug" name="slug" type="text" class="mt-1 block w-full"
                             value="{{ old('slug', $page->slug) }}" required />
                    <x-input-error class="mt-2" :messages="$errors->get('slug')" />
                </div>

                {{-- Content Blocks --}}
                <div class="mt-6">
                    <label class="block font-semibold mb-2">Page Content Blocks</label>
                    <div id="content-blocks" class="space-y-4 p-4 border rounded bg-gray-50 max-h-[500px] overflow-auto">
                        @php
                            $oldContents = old('contents', $page->contents ?? []);
                        @endphp

                        @foreach($oldContents as $index => $block)
                            @php
                                $type = is_array($block) ? $block['type'] : $block->type;
                                $contentValue = is_array($block) ? ($block['content'] ?? '') : $block->content;
                                $blockId = is_array($block) ? ($block['id'] ?? null) : $block->id;
                            @endphp

                            <div class="bg-white border rounded p-4 relative" data-index="{{ $index }}">
                                <input type="hidden" name="contents[{{ $index }}][id]" value="{{ $blockId }}">
                                <input type="hidden" name="contents[{{ $index }}][type]" value="{{ $type }}">

                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-semibold text-gray-700">{{ ucfirst($type) }} Block</span>
                                    <button type="button" class="text-red-600 hover:underline remove-block" title="Remove block">&times;</button>
                                </div>

                                @if($type === 'title')
                                    <input type="text" name="contents[{{ $index }}][content]"
                                           class="w-full rounded border-gray-300 p-2 focus:ring-indigo-500 focus:border-indigo-500"
                                           value="{{ $contentValue }}" required />
                                @elseif($type === 'text')
                                    <textarea name="contents[{{ $index }}][content]" rows="4"
                                              class="w-full rounded border-gray-300 p-2 focus:ring-indigo-500 focus:border-indigo-500"
                                              required>{{ $contentValue }}</textarea>
                                @elseif($type === 'image')
                                    <div>
                                        <input type="file" name="contents[{{ $index }}][image_file]" accept="image/*"
                                               class="w-full rounded border-gray-300 p-2" />
                                        @if($contentValue)
                                            <input type="hidden" name="contents[{{ $index }}][content]" value="{{ $contentValue }}" />
                                            <img src="{{ asset('storage/' . $contentValue) }}" alt="Image preview"
                                                 class="mt-3 max-h-40 rounded shadow-sm object-contain" />
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <button type="button" id="add-block-btn"
                            class="mt-4 inline-block px-5 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 transition">
                        + Add Content Block
                    </button>
                </div>

                {{-- Submit --}}
                <div class="mt-8 flex items-center gap-4">
                    <x-button>Update Page</x-button>
                    <x-action-message status="page-updated" />
                </div>
            </form>

            {{-- Modal --}}
            <div id="block-type-modal"
                 class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg p-6 w-72 shadow-lg">
                    <h3 class="text-lg font-bold mb-4 text-center">Select Content Block Type</h3>
                    <div class="space-y-3">
                        <button data-type="title"
                                class="w-full py-2 rounded border border-gray-300 hover:bg-gray-100 transition">Title</button>
                        <button data-type="text"
                                class="w-full py-2 rounded border border-gray-300 hover:bg-gray-100 transition">Text</button>
                        <button data-type="image"
                                class="w-full py-2 rounded border border-gray-300 hover:bg-gray-100 transition">Image</button>
                        <button id="close-modal"
                                class="w-full mt-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition">Cancel</button>
                    </div>
                </div>
            </div>

            {{-- JS --}}
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const addBlockBtn = document.getElementById('add-block-btn');
                    const contentBlocks = document.getElementById('content-blocks');
                    const modal = document.getElementById('block-type-modal');
                    const closeModalBtn = document.getElementById('close-modal');

                    // Use highest existing index + 1 for new blocks to avoid duplicates
                    let blockIndex = (() => {
                        const indexes = Array.from(contentBlocks.children)
                            .map(div => parseInt(div.dataset.index))
                            .filter(n => !isNaN(n));
                        return indexes.length ? Math.max(...indexes) + 1 : 0;
                    })();

                    // Create content block HTML by type
                    function createContentBlock(type) {
                        const container = document.createElement('div');
                        container.className = 'bg-white border rounded p-4 relative';
                        container.dataset.index = blockIndex;

                        container.innerHTML = `
                            <input type="hidden" name="contents[${blockIndex}][type]" value="${type}">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-gray-700">${type.charAt(0).toUpperCase() + type.slice(1)} Block</span>
                                <button type="button" class="text-red-600 hover:underline remove-block" title="Remove block">&times;</button>
                            </div>
                            ${
                            type === 'title' ?
                                `<input type="text" name="contents[${blockIndex}][content]"
                                        class="w-full rounded border-gray-300 p-2 focus:ring-indigo-500 focus:border-indigo-500" required>` :
                                type === 'text' ?
                                    `<textarea name="contents[${blockIndex}][content]" rows="4"
                                           class="w-full rounded border-gray-300 p-2 focus:ring-indigo-500 focus:border-indigo-500" required></textarea>` :
                                    type === 'image' ?
                                        `<input type="file" name="contents[${blockIndex}][image_file]" accept="image/*"
                                        class="w-full rounded border-gray-300 p-2">` : ''
                        }
                        `;
                        contentBlocks.appendChild(container);
                        blockIndex++;
                    }

                    // Show modal on add button click
                    addBlockBtn.addEventListener('click', () => {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });

                    // Close modal
                    closeModalBtn.addEventListener('click', () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    });

                    // Block type buttons
                    modal.querySelectorAll('button[data-type]').forEach(button => {
                        button.addEventListener('click', () => {
                            const type = button.getAttribute('data-type');
                            createContentBlock(type);
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        });
                    });

                    // Delegate remove block clicks
                    contentBlocks.addEventListener('click', e => {
                        if (e.target.classList.contains('remove-block')) {
                            e.target.closest('div[data-index]').remove();
                        }
                    });
                });
            </script>
        </x-slot:content>
    </x-card>
</x-auth-layout>
