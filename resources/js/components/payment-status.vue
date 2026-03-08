<template>
    <component
        :is="props.as"
        class="badge shadow-none badge-xs"
        :class="current.class"
    >
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
            return { label: 'Pending', class: 'badge-soft-warning' };
        case 'unpaid':
            return { label: 'Unpaid', class: 'badge-soft-secondary' };
        case 'partially-paid':
        case 'partially_paid':
        case 'partial':
            return { label: 'Partially Paid', class: 'badge-soft-info' };
        case 'paid':
            return { label: 'Paid', class: 'badge-soft-success' };
        case 'cancelled':
        case 'canceled':
            return { label: 'Cancelled', class: 'badge-soft-danger' };
        default:
            return { label: value.value || '--', class: 'badge text-muted' };
    }
});
</script>
