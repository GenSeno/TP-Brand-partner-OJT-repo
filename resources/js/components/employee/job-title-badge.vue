<template>
    <span
        :class="`badge-soft-${badgeClass}`"
        class="badge shadow-none badge-xs"
    >
        {{ label }}
    </span>
</template>
<script setup>
import { computed } from 'vue';

const props = defineProps({
    jobTitle: {
        type: String,
        required: true,
    },
});

const jobTitleMap = {
    management: { label: 'Management', color: 'primary' },
    artist: { label: 'Artist', color: 'info' },
    'printer operator': { label: 'Printer Operator', color: 'warning' },
    'heat press': { label: 'Heat Press', color: 'danger' },
    sewer: { label: 'Sewer', color: 'success' },
    qa: { label: 'Quality Assurance', color: 'secondary' },
    dispatcher: { label: 'Dispatcher', color: 'dark' },
};

const label = computed(
    () => jobTitleMap[props.jobTitle]?.label || props.jobTitle,
);
const badgeClass = computed(
    () => jobTitleMap[props.jobTitle]?.color || 'secondary',
);
</script>
