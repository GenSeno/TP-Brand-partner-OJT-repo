<style>
.attachment-component .list-group-item {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-start;
}
</style>

<template>
    <div class="attachment-component mt-3">
        <!-- Files button with total count -->
        <button
            type="button"
            class="btn btn-light position-relative"
            @click="showAttachments = !showAttachments"
        >
            <i data-feather="paperclip" class="feather-paperclip"></i>
            Attached Files
            <span
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"
                v-show="filesPreview.length > 0"
            >
                {{ filesPreview.length }}
                <span class="visually-hidden"></span>
            </span>
        </button>
        <!-- Attachment area -->
        <label
            v-if="showAttachments"
            class="d-block mt-2 border border-2 border-dashed rounded p-3"
            :class="{
                'bg-light': isDragging,
            }"
            @dragover.prevent="handleDragOver"
            @dragleave="handleDragLeave"
            @drop.prevent="handleDrop"
        >
            <!-- File input (hidden) -->
            <input
                type="file"
                multiple
                ref="fileInput"
                class="d-none"
                @change="handleFileChange"
            />

            <!-- Drag & drop / click area -->
            <div class="text-center cursor-pointer p-5">
                Drag and drop files here or click to select
            </div>

            <small class="text-muted d-block mt-1"
                >Max size per file: 25 MB</small
            >

            <!-- Upload Files button -->
            <button
                class="btn btn-primary btn-sm mt-3 w-100 p-2"
                :disabled="!isFilesUploadable"
                @click="uploadFiles"
            >
                {{ form.processing ? 'Loading…' : 'Upload Files' }}
            </button>

            <!-- Uploaded files list -->
            <ul class="list-group list-group-flush mt-3">
                <li
                    v-for="(file, index) in filesPreview"
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
                                :disabled="isDeleting || form.processing"
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
        </label>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import * as alert from '@/helpers/alert';
import { formatSize } from '@/helpers/number';
import { useAxiosForm } from '@/composables/axiosForm';
import { computed } from 'vue';

const props = defineProps({
    modelId: [Number, String],
    postRoute: String,
    deleteRoute: String,
    existingFiles: { type: Array, default: () => [] },
});

const showAttachments = ref(false);
const isDragging = ref(false);
const isDeleting = ref(false);
const form = useAxiosForm({
    files: [],
});
const filesPreview = ref(
    props.existingFiles.map((f) => ({
        id: f.id,
        name: f.name,
        original_url: f.original_url,
        extension: f.extension || f.file_name?.split('.').pop() || '',
        size: f.size || 0,
        created_at: f.created_at,
        file: null,
    })),
);
const allowedTypes = [
    'image/',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
];

const formatDate = (date) => {
    return date ? new Date(date).toLocaleString() : '';
};

const handleDragOver = () => {
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const handleDrop = (event) => {
    isDragging.value = false;
    for (let file of event.dataTransfer.files) {
        handleFileChange({ target: { files: [file] } });
    }
};

const handleFileChange = (event) => {
    for (let file of event.target.files) {
        if (!allowedTypes.some((type) => file.type.startsWith(type))) {
            alert.showError(`${file.name} is not an allowed file type`);
            continue;
        }
        if (file.size / 1024 / 1024 > 25) {
            alert.showError(`${file.name} exceeds 25 MB`);
            continue;
        }

        form.data.files.push(file);
        filesPreview.value.push({
            file,
            name: file.name,
            size: file.size,
            original_url: file.original_url,
            extension: file.name.split('.').pop(),
            created_at: new Date().toISOString(),
        });
    }
    event.target.value = null;
};

const isFilesUploadable = computed(() => {
    return form.data.files.length > 0 && !form.processing;
});

const uploadFiles = () => {
    if (!isFilesUploadable.value) return;

    form.post(route(props.postRoute, props.modelId), {
        onSuccess: ({ data }) => {
            // Merge existing files with newly uploaded
            filesPreview.value = data.files.map((f) => ({
                id: f.id,
                name: f.name,
                original_url: f.original_url,
                extension: f.extension,
                size: f.size,
                created_at: f.created_at,
                file: null,
            }));

            alert.showSuccess('Files uploaded successfully!');
        },
        onError: (error) => {
            console.error('Upload error:', error);
            alert.showError('Upload failed. Please try again.');
        },
    });
};

const removeFile = (index, id = null) => {
    isDeleting.value = true;

    if (id) {
        form.delete(route(props.deleteRoute, [props.modelId, id]), {
            onSuccess: () => {
                filesPreview.value.splice(index, 1);
            },
            onError: () => {
                alert.showError('Failed to delete file');
            },
        });
    } else {
        filesPreview.value.splice(index, 1);
    }

    isDeleting.value = false;
};
</script>
