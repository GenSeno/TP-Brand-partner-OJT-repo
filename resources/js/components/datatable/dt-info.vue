<template>
    <div class="d-flex align-items-center gap-3 justify-content-center">
        <small>Rows per page</small>
        <div class="dropdown">
            <button
                type="button"
                class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center"
                data-bs-toggle="dropdown"
            >
                {{ perPage }}
            </button>
            <ul class="dropdown-menu dropdown-menu-start">
                <li v-for="value in pageRowOptions" :key="value">
                    <button
                        type="button"
                        @click="perPage = value"
                        class="dropdown-item rounded-1"
                    >
                        {{ value }}
                    </button>
                </li>
            </ul>
        </div>
        <small
            >{{ startRecord || 0 }} - {{ endRecord || 0 }} of
            {{ totalRecords || 0 }}</small
        >
    </div>
</template>

<script setup>
import { watch } from 'vue';

const perPage = defineModel({
    type: Number,
    required: true,
});

const emit = defineEmits(['change']);

defineProps({
    pageRowOptions: {
        type: Array,
        required: true,
    },
    totalRecords: {
        type: Number,
        default: 0,
    },
    startRecord: {
        type: Number,
        default: 0,
    },
    endRecord: {
        type: Number,
        default: 0,
    },
});

watch(perPage, () => emit('change'));

defineOptions({
    inheritAttrs: false,
});
</script>
