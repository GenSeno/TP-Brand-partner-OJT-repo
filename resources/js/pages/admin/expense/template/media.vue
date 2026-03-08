<template>
    <div class="attachment-component mt-3">
        <!-- Files button with total count -->
        <button
            type="button"
            class="btn btn-light position-relative"
            @click="showAttachments = !showAttachments"
        >
            <i data-feather="edit" class="feather-paperclip"></i> Attached Files
            <span
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"
                v-if="files.length"
            >
                {{ files.length }}
                <span class="visually-hidden"></span>
            </span>
        </button>
        <!-- Attachment area -->
        <div
            v-if="showAttachments"
            class="mt-2 border border-2 border-dashed rounded p-3"
        >
            <!-- File input (hidden) -->
            <input
                type="file"
                multiple
                ref="fileInput"
                class="d-none"
                @change="handleFiles"
            />

            <!-- Drag & drop / click area -->
            <div
                class="text-center cursor-pointer p-5"
                @click="$refs.fileInput.click()"
                @dragover.prevent
                @drop.prevent="handleDrop"
            >
                Drag and drop files here or click to select
            </div>

            <small class="text-muted d-block mt-1"
                >Max size per file: 25 MB</small
            >

            <!-- Upload Files button -->
            <button
                class="btn btn-primary btn-sm mt-3 w-100 p-2"
                :disabled="!files.some((f) => f.file) || uploading"
                @click="uploadFiles"
            >
                {{ uploading ? 'Uploading…' : 'Upload Files' }}
            </button>

            <!-- Uploaded files list -->
            <ul class="list-group list-group-flush mt-3">
                <li
                    v-for="(file, index) in files"
                    :key="file.id || index"
                    class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                >
                    <!-- File Info -->
                    <div
                        class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 w-100"
                    >
                        <div>
                            <strong class="text-danger">{{
                                file.extension
                                    ? file.extension.toUpperCase()
                                    : ''
                            }}</strong>
                            {{ file.name }}
                        </div>

                        <div class="text-muted">
                            {{ formatSize(file.size) }} | Uploaded:
                            {{ formatDate(file.created_at) }}
                        </div>

                        <!-- Actions: View & Delete -->
                        <div class="ms-auto d-flex gap-2 mt-2 mt-sm-0">
                            <a
                                v-if="file.original_url"
                                :href="file.original_url"
                                target="_blank"
                                class="btn btn-sm btn-secondary-light"
                            >
                                <i
                                    data-feather="eye"
                                    class="feather-paperclip"
                                ></i>
                            </a>
                            <button
                                type="button"
                                class="btn btn-sm btn-danger-light"
                                @click="removeFile(index, file.id)"
                            >
                                <i
                                    data-feather="trash-2"
                                    class="feather-trash-2"
                                ></i>
                            </button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import * as alert from '@/helpers/alert';

export default {
    props: {
        expenseId: Number,
        existingFiles: { type: Array, default: () => [] },
    },
    data() {
        return {
            showAttachments: false,
            files: [],
            uploading: false,
        };
    },
    mounted() {
        // Load existing files
        this.files = this.existingFiles.map((f) => ({
            id: f.id,
            name: f.name,
            original_url: f.original_url,
            extension: f.extension || f.file_name?.split('.').pop() || '',
            size: f.size || 0,
            created_at: f.created_at,
            file: null,
        }));
    },
    methods: {
        handleFiles(event) {
            const allowedTypes = [
                'image/',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];

            for (let file of event.target.files) {
                if (!allowedTypes.some((type) => file.type.startsWith(type))) {
                    alert.showError(`${file.name} is not an allowed file type`);
                    continue;
                }
                if (file.size / 1024 / 1024 > 25) {
                    alert.showError(`${file.name} exceeds 25 MB`);
                    continue;
                }

                this.files.push({
                    file,
                    name: file.name,
                    size: file.size,
                    original_url: file.original_url,
                    extension: file.name.split('.').pop(),
                    created_at: new Date().toISOString(),
                });
            }

            event.target.value = null;
        },

        handleDrop(event) {
            for (let file of event.dataTransfer.files) {
                this.handleFiles({ target: { files: [file] } });
            }
        },

        async uploadFiles() {
            if (!this.files.some((f) => f.file)) return;
            this.uploading = true;

            const formData = new FormData();
            this.files.forEach((f) => {
                if (f.file) formData.append('files[]', f.file);
            });

            try {
                const { data } = await axios.post(
                    route('admin.expense.upload', { expense: this.expenseId }),
                    formData,
                    { headers: { 'Content-Type': 'multipart/form-data' } },
                );

                const uploadedFiles = data.files.map((f) => ({
                    id: f.id,
                    name: f.name,
                    original_url: f.original_url,
                    extension: f.extension,
                    size: f.size,
                    created_at: f.created_at,
                    file: null,
                }));

                // Merge existing files with newly uploaded
                this.files = [
                    ...this.files.filter((f) => !f.file),
                    ...uploadedFiles,
                ];

                alert.showSuccess('Files uploaded successfully!');
            } catch {
                alert.showError('Upload failed. Please try again.');
            } finally {
                this.uploading = false;
            }
        },

        removeFile(index, id = null) {
            if (id) {
                axios
                    .delete(
                        route('admin.expense.media.destroy', {
                            expense: this.expenseId,
                            media: id,
                        }),
                    )
                    .then(() => this.files.splice(index, 1))
                    .catch(() => alert.showError('Failed to delete file'));
            } else {
                this.files.splice(index, 1);
            }
        },

        formatDate(date) {
            if (!date) return '';
            return new Date(date).toLocaleString();
        },

        formatSize(bytes) {
            if (!bytes) return '';
            const sizes = ['B', 'KB', 'MB', 'GB'];
            let i = 0;
            let size = bytes;
            while (size >= 1024 && i < sizes.length - 1) {
                size /= 1024;
                i++;
            }
            return size.toFixed(2) + ' ' + sizes[i];
        },
    },
};
</script>

<style>
.attachment-component .list-group-item {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-start;
}
</style>
