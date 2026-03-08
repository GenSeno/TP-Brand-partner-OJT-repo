<template>
    <component
        :is="props.as"
        class="badge shadow-none badge-xs"
        :class="`badge-soft-${current.color}`"
    >
        <i class="feather" :class="current.icon"></i>
        {{ current.label }}
    </component>
</template>
<script setup>
import { JobOrderStageOptions } from '@/enums/job-order-stage';
import { computed } from 'vue';

const props = defineProps({
    stage: {
        type: String,
        required: true,
    },
    as: {
        type: String,
        default: 'span',
    },
});

const current = computed(
    () =>
        JobOrderStageOptions[props.stage] || {
            label: 'Unknown',
            color: 'secondary',
            icon: 'feather-help-circle',
        },
);
</script>
