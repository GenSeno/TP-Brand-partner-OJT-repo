<template>
    <button
        type="submit"
        class="btn btn-submit"
        :disabled="loading || disabled || submitted"
    >
        <template v-if="loading">
            <slot name="loading">{{ loadingText }}</slot>
        </template>
        <template v-else
            ><slot v-if="submitted" name="submitted"></slot> <slot v-else></slot
        ></template>
    </button>
</template>

<script setup>
defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
    loadingText: {
        type: String,
        default: 'Loading...',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    submitted: {
        type: Boolean,
        default: false,
    },
});
</script>
