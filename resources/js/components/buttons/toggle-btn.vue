<template>
    <button
        type="button"
        :class="[
            'btn rounded-pill',
            proxyChecked ? 'btn-soft-primary' : 'btn-soft-light',
        ]"
        @click="proxyChecked = !proxyChecked"
    >
        <slot>{{ proxyChecked ? 'On' : 'Off' }}</slot>
    </button>
</template>

<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: Boolean,
        default: false,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },
    set(val) {
        emit('update:checked', val);
    },
});
</script>
