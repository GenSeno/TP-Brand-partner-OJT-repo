<template>
    <input
        ref="input"
        v-model="model"
        v-bind="$attrs"
        :type="props.type"
        :class="
            props.newClass || [
                'form-control',
                props.class,
                {
                    'is-invalid': props.errorMessage,
                },
            ]
        "
    />
</template>

<script setup>
import { onMounted, useTemplateRef } from 'vue';

const props = defineProps({
    type: { type: String, default: 'text' },
    newClass: { type: null, required: false },
    class: { type: null, required: false },
    errorMessage: { type: String, default: '' },
});

const model = defineModel({ type: [String, Number] });

const input = useTemplateRef('input');

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>
