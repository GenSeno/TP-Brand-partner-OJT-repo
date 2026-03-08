<template>
    <input-text v-model="model" />
    <small v-show="!props.hideTip"
        >The URL-friendly version of the item (auto)</small
    >
</template>

<script setup>
import { slugify } from '@/helpers/string';
import { debounce } from 'lodash';
import { watch } from 'vue';

const props = defineProps({
    reference: {
        type: String,
        required: true,
    },
    debounceTime: {
        type: Number,
        default: 300,
    },
    hideTip: Boolean,
});

const model = defineModel({ type: [String, Number] });

watch(
    () => props.reference,
    debounce((newVal) => {
        model.value = slugify(newVal);
    }, props.debounceTime),
);
</script>
