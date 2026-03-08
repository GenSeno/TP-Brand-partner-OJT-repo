<template>
    <span :class="`badge shadow-none badge-xs badge-soft-${stateData.color}`">
        <i :class="`feather ${stateData.icon}`"></i>
        {{ label ? label : stateData.label }}
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    state: {
        type: String,
        required: true,
    },
    stateData: {
        type: Object,
        default: null,
    },
    label: {
        type: String,
        default: null,
    },
});

const stateData = computed(() => {
    if (props.stateData) {
        return props.stateData;
    }

    // Fallback for basic state display if stateData not provided
    const stateName = props.state
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (l) => l.toUpperCase());

    return {
        label: stateName,
        icon: 'feather-circle',
        color: 'primary',
    };
});
</script>
