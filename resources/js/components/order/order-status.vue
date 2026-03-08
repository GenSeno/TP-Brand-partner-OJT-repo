<template>
    <component :is="props.as" :class="current.class">
        {{ current.label }}
    </component>
</template>
<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: [String, Number],
        required: true,
    },
    as: {
        type: String,
        default: 'span',
    },
});

const value = computed(() => {
    // normalize to string and lowercase for robust comparisons
    return String(props.status ?? '').toLowerCase();
});

const current = computed(() => {
    switch (value.value) {
        case 'pending':
            return {
                label: 'Pending',
                class: 'badge shadow-none badge-xs text-bg-warning',
            };
        default:
            return {
                label: value.value || '--',
                class: 'text-uppercase text-muted',
            };
    }
});
</script>
