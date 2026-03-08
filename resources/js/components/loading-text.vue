<template>
    <div v-if="loading">
        <i v-if="!hideSpinner" class="fas fa-spinner fa-spin"></i>
        <slot name="loading">
            <span v-if="!hideText" class="ms-2">{{ text }}</span>
        </slot>
    </div>
    <slot v-else></slot>
</template>

<script setup>
defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
    text: {
        type: String,
        default: 'Loading...',
    },
    hideText: {
        type: Boolean,
        default: false,
    },
    hideSpinner: {
        type: Boolean,
        default: false,
    },
});
</script>
