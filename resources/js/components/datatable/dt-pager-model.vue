<template>
    <nav aria-label="Page navigation" class="pagination-style-4">
        <ul class="pagination mb-0 flex-wrap justify-content-center">
            <li class="page-item" :class="{ disabled: modelValue === 1 }">
                <button
                    type="button"
                    class="page-link"
                    :class="{ 'text-primary': modelValue > 1 }"
                    :disabled="modelValue === 1"
                    @click="changePage(modelValue - 1)"
                >
                    <span v-html="'&laquo; Previous'"></span>
                </button>
            </li>

            <li
                v-for="page in pageNumbers"
                :key="page"
                class="page-item"
                :class="{ active: page === modelValue }"
            >
                <button
                    v-if="typeof page === 'number'"
                    type="button"
                    class="page-link"
                    @click="changePage(page)"
                >
                    {{ page }}
                </button>
                <span v-else class="page-link">{{ page }}</span>
            </li>

            <li
                class="page-item"
                :class="{ disabled: modelValue === totalPages }"
            >
                <button
                    type="button"
                    class="page-link"
                    :class="{ 'text-primary': modelValue < totalPages }"
                    :disabled="modelValue === totalPages"
                    @click="changePage(modelValue + 1)"
                >
                    <span v-html="'Next &raquo;'"></span>
                </button>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Number,
        required: true,
    },
    totalPages: {
        type: Number,
        required: true,
    },
    maxVisible: {
        type: Number,
        default: 7,
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const pageNumbers = computed(() => {
    const pages = [];
    const current = props.modelValue;
    const total = props.totalPages;
    const max = props.maxVisible;

    if (total <= max) {
        // Show all pages if total is less than max
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {
        // Always show first page
        pages.push(1);

        let start = Math.max(2, current - Math.floor((max - 4) / 2));
        let end = Math.min(total - 1, current + Math.floor((max - 4) / 2));

        // Adjust if at the beginning
        if (current <= Math.ceil((max - 2) / 2)) {
            end = max - 2;
        }

        // Adjust if at the end
        if (current >= total - Math.floor((max - 2) / 2)) {
            start = total - max + 3;
        }

        // Add ellipsis after first page if needed
        if (start > 2) {
            pages.push('...');
        }

        // Add middle pages
        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        // Add ellipsis before last page if needed
        if (end < total - 1) {
            pages.push('...');
        }

        // Always show last page
        pages.push(total);
    }

    return pages;
});

const changePage = (page) => {
    if (page < 1 || page > props.totalPages || page === props.modelValue) {
        return;
    }

    emit('update:modelValue', page);
    emit('change', page);
};
</script>
