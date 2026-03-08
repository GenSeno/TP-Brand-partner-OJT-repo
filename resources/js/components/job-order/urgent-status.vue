<template>
    <component :is="props.as" :class="current.class">
        {{ current.label }}
    </component>
</template>
<script setup>
import { UrgencyFlag } from '@/enums/urgency-flag';
import { computed } from 'vue';

const props = defineProps({
    urgencyFlag: {
        type: [Number, String],
        required: true,
    },
    as: {
        type: String,
        default: 'span',
    },
});

const value = computed(() => {
    return Number(props.urgencyFlag);
});

const current = computed(() => {
    switch (value.value) {
        case UrgencyFlag.RUSH:
            return {
                label: 'Rush',
                class: 'badge text-bg-danger',
            };
        case UrgencyFlag.PRIORITY:
            return {
                label: 'Priority',
                class: 'badge text-bg-warning',
            };

        default:
            return {
                label: '--',
                class: 'badge text-muted',
            };
    }
});
</script>
