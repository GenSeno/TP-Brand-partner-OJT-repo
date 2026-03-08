<template>
    <div class="dropdown">
        <div data-bs-toggle="dropdown" aria-expanded="false">
            <slot
                name="trigger"
                :selected-label="selectedLabel"
                :selected-value="modelValue"
                :placeholder="placeholder"
            >
                <button
                    type="button"
                    class="btn dropdown-toggle text-start d-flex align-items-center justify-content-between"
                    :class="buttonClass"
                    :disabled="disabled"
                >
                    <span v-if="selectedLabel">{{ selectedLabel }}</span>
                    <span v-else class="text-muted">{{ placeholder }}</span>
                </button>
            </slot>
        </div>
        <ul class="dropdown-menu p-1" :class="menuClass">
            <li v-for="(label, value) in options" :key="value">
                <button
                    type="button"
                    class="dropdown-item rounded-1"
                    :class="{
                        'active text-primary': value == modelValue,
                    }"
                    @click="selectOption(value)"
                >
                    {{ label }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: null,
    },
    options: {
        type: Object,
        required: true,
    },
    placeholder: {
        type: String,
        default: 'Select an option',
    },
    buttonClass: {
        type: String,
        default: 'btn-outline-secondary',
    },
    menuClass: {
        type: String,
        default: 'dropdown-menu-start',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

const selectedLabel = computed(() => {
    return props.options[props.modelValue] || null;
});

const selectOption = (value) => {
    emit('update:modelValue', value);
};
</script>

<style scoped>
.dropdown-toggle {
    min-width: 150px;
}
</style>
