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
    isBadge: {
        type: Boolean,
        default: true,
    },
    as: {
        type: String,
        default: 'span',
    },
});

const value = computed(() => {
    return String(props.status ?? '').toLowerCase();
});

const current = computed(() => {
    switch (value.value) {
        case 'draft':
            return {
                label: 'Draft',
                class: props.isBadge
                    ? 'badge text-bg-secondary'
                    : 'text-secondary',
            };
        case 'unpaid':
            return {
                label: 'Unpaid',
                class: props.isBadge ? 'badge text-bg-danger' : 'text-danger',
            };
        case 'partially-paid':
        case 'partially_paid':
            return {
                label: 'Partially Paid',
                class: props.isBadge ? 'badge text-bg-warning' : 'text-warning',
            };
        case 'paid':
            return {
                label: 'Paid',
                class: props.isBadge ? 'badge text-bg-success' : 'text-success',
            };
        case 'overdue':
            return {
                label: 'Overdue',
                class: props.isBadge ? 'badge text-bg-dark' : 'text-dark',
            };
        case 'cancelled':
        case 'canceled':
            return {
                label: 'Cancelled',
                class: props.isBadge
                    ? 'badge text-bg-secondary'
                    : 'text-secondary',
            };
        default:
            return {
                label: value.value || '—',
                class: props.isBadge ? 'badge text-muted' : 'text-muted',
            };
    }
});
</script>
