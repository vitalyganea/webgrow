<x-admin.layouts.auth title="File Manager">
    <x-slot:header>
        File Manager
    </x-slot:header>

    <x-admin.card>
        <x-slot:content>
            <!-- Upload Area -->
            <div class="mb-6">
                <div id="dropzone" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-gray-400 transition-colors cursor-pointer">
                    <div class="space-y-4">
                        <div class="mx-auto w-12 h-12 text-gray-400">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Drop files here or click to upload</p>
                            <p class="text-sm text-gray-500">Support for images, PDFs, CSVs, and other file types</p>
                        </div>
                        <input type="file" id="file-input" class="hidden" multiple accept="*/*">
                        <button type="button" id="upload-btn" class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/80 transition-colors">
                            Choose Files
                        </button>
                    </div>
                </div>

                <!-- Upload Progress -->
                <div id="upload-progress" class="hidden mt-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span id="upload-status">Uploading...</span>
                            <span id="upload-percentage">0%</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-3">
                            <div id="progress-bar" class="bg-blue-600 h-3 rounded-full transition-all duration-300 relative overflow-hidden" style="width: 0%">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-pulse"></div>
                            </div>
                        </div>
                        <div id="file-progress-list" class="space-y-1 max-h-32 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <!-- Files Grid -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Uploaded Files ({{ count($files) }})</h3>

                @if(count($files) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="files-grid">
                        @foreach($files as $file)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow file-item" data-filename="{{ $file['name'] }}">
                                <!-- File Preview -->
                                <div class="mb-3">
                                    @if($file['is_image'])
                                        <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="w-full h-20 object-cover rounded">
                                    @else
                                        <div class="w-full h-20 bg-gray-100 rounded flex items-center justify-center">
                                            <div class="text-center">
                                                <div class="text-lg font-bold text-gray-600 mb-1">{{ strtoupper($file['type']) }}</div>
                                                <div class="text-xs text-gray-500">{{ $file['size'] }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- File Info -->
                                <div class="space-y-2">
                                    <h4 class="font-medium text-sm truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</h4>
                                    <div class="flex justify-between text-xs text-gray-500">
                                        <span>{{ $file['size'] }}</span>
                                        <span>{{ $file['modified'] }}</span>
                                    </div>
                                </div>

                                <!-- File Actions -->
                                <div class="mt-3 flex space-x-2">
                                    <button onclick="copyPath('{{ $file['url'] }}')" class="flex-1 px-3 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors">
                                        Copy URL
                                    </button>
                                    <a href="{{ $file['url'] }}" target="_blank" class="flex-1 px-3 py-1 text-xs bg-green-50 text-green-700 rounded hover:bg-green-100 transition-colors text-center">
                                        View
                                    </a>
                                    <button onclick="deleteFile('{{ $file['name'] }}')" class="flex-1 px-3 py-1 text-xs bg-red-50 text-red-700 rounded hover:bg-red-100 transition-colors">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">📁</div>
                        <p class="text-gray-500">No files uploaded yet. Drop some files above to get started!</p>
                    </div>
                @endif
            </div>

            <!-- Toast Notification - REMOVED -->

            <script>
                // CSRF Token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // DOM Elements
                const dropzone = document.getElementById('dropzone');
                const fileInput = document.getElementById('file-input');
                const uploadBtn = document.getElementById('upload-btn');
                const uploadProgress = document.getElementById('upload-progress');
                const progressBar = document.getElementById('progress-bar');
                const uploadStatus = document.getElementById('upload-status');

                // Drag and Drop functionality
                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('border-blue-400', 'bg-blue-50');
                });

                dropzone.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-blue-400', 'bg-blue-50');
                });

                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-blue-400', 'bg-blue-50');
                    const files = Array.from(e.dataTransfer.files);
                    uploadFiles(files);
                });

                // Click to upload
                dropzone.addEventListener('click', () => {
                    fileInput.click();
                });

                uploadBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    fileInput.click();
                });

                fileInput.addEventListener('change', (e) => {
                    const files = Array.from(e.target.files);
                    uploadFiles(files);
                });

                // Upload files function
                async function uploadFiles(files) {
                    if (files.length === 0) return;

                    uploadProgress.classList.remove('hidden');
                    const fileProgressList = document.getElementById('file-progress-list');
                    const uploadPercentage = document.getElementById('upload-percentage');

                    // Clear previous progress items
                    fileProgressList.innerHTML = '';

                    let completedFiles = 0;
                    const totalFiles = files.length;

                    // Create progress items for each file
                    files.forEach((file, index) => {
                        const progressItem = document.createElement('div');
                        progressItem.className = 'flex items-center justify-between text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded';
                        progressItem.innerHTML = `
                            <span class="truncate flex-1 mr-2">${file.name}</span>
                            <span class="file-status">Waiting...</span>
                        `;
                        progressItem.id = `file-progress-${index}`;
                        fileProgressList.appendChild(progressItem);
                    });

                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        const formData = new FormData();
                        const progressItem = document.getElementById(`file-progress-${i}`);
                        const statusSpan = progressItem.querySelector('.file-status');

                        formData.append('file', file);

                        try {
                            // Update overall status
                            uploadStatus.textContent = `Uploading ${file.name} (${i + 1}/${files.length})...`;
                            statusSpan.textContent = 'Uploading...';
                            statusSpan.className = 'file-status text-blue-600';

                            const response = await fetch('{{ route("admin.upload.file") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: formData
                            });

                            const result = await response.json();

                            completedFiles++;
                            const overallProgress = (completedFiles / totalFiles) * 100;
                            progressBar.style.width = `${overallProgress}%`;
                            uploadPercentage.textContent = `${Math.round(overallProgress)}%`;

                            if (result.success) {
                                statusSpan.textContent = 'Success';
                                statusSpan.className = 'file-status text-green-600';
                            } else {
                                statusSpan.textContent = 'Failed';
                                statusSpan.className = 'file-status text-red-600';
                            }
                        } catch (error) {
                            completedFiles++;
                            const overallProgress = (completedFiles / totalFiles) * 100;
                            progressBar.style.width = `${overallProgress}%`;
                            uploadPercentage.textContent = `${Math.round(overallProgress)}%`;

                            statusSpan.textContent = 'Failed';
                            statusSpan.className = 'file-status text-red-600';
                        }
                    }

                    uploadStatus.textContent = 'Upload completed!';

                    // Hide progress after a delay
                    setTimeout(() => {
                        uploadProgress.classList.add('hidden');
                        progressBar.style.width = '0%';
                        uploadPercentage.textContent = '0%';
                        fileInput.value = '';
                    }, 2000);

                    const successfulUploads = files.length - document.querySelectorAll('.file-status.text-red-600').length;
                    const failedUploads = document.querySelectorAll('.file-status.text-red-600').length;

                    // Show summary notification
                    if (successfulUploads > 0 && failedUploads === 0) {
                        // All files uploaded successfully
                        Swal.fire({
                            icon: 'success',
                            title: 'Upload Complete!',
                            text: `Successfully uploaded ${successfulUploads} file(s)!`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else if (successfulUploads > 0 && failedUploads > 0) {
                        // Mixed results
                        Swal.fire({
                            icon: 'warning',
                            title: 'Upload Partially Complete',
                            text: `${successfulUploads} file(s) uploaded successfully, ${failedUploads} failed.`,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else if (failedUploads > 0) {
                        // All failed
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Failed',
                            text: `Failed to upload ${failedUploads} file(s).`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }

                    if (successfulUploads > 0) {
                        // Reload page to show new files
                        setTimeout(() => {
                            location.reload();
                        }, 2500);
                    }
                }

                // Copy URL to clipboard
                function copyPath(url) {
                    navigator.clipboard.writeText(url).then(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Copied!',
                            text: 'URL copied to clipboard!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }).catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to copy URL',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    });
                }

                // Delete file
                function deleteFile(filename) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this! The file will be permanently deleted.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('{{ route("admin.delete.file") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ filename: filename })
                            })
                                .then(response => response.json())
                                .then(result => {
                                    if (result.success) {
                                        // Remove the file item from DOM
                                        const fileItem = document.querySelector(`[data-filename="${filename}"]`);
                                        if (fileItem) {
                                            fileItem.remove();
                                        }
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Deleted!',
                                            text: result.message,
                                            timer: 1500,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: result.message,
                                            timer: 1500,
                                            showConfirmButton: false
                                        });
                                    }
                                })
                                .catch(() => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Failed to delete file',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                });
                        }
                    });
                }
            </script>
        </x-slot:content>
    </x-admin.card>
</x-admin.layouts.auth>
