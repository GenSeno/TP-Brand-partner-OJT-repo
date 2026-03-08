<template>
    <div class="dropdown me-2">
        <button
            type="button"
            class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            {{ name }}:
            {{ selectedLabel ? selectedLabel : props.withAll ? 'All' : '' }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end p-3">
            <li v-if="props.withAll">
                <button
                    type="button"
                    @click="model = null"
                    class="dropdown-item rounded-1"
                >
                    {{
                        props.placeholder
                            ? props.placeholder
                            : `All ${props.name}`
                    }}
                </button>
            </li>
            <li v-for="(label, value) in props.options" :key="value">
                <button
                    type="button"
                    @click="model = value"
                    class="dropdown-item rounded-1"
                >
                    {{ label }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { computed, watch } from 'vue';

const emit = defineEmits(['change']);

const props = defineProps({
    options: {
        type: Object,
        required: true,
    },
    name: String,
    placeholder: String,
    withAll: {
        type: Boolean,
        default: true,
    },
});

const model = defineModel();
const selectedLabel = computed(() => {
    return props.options[model.value] ?? null;
});

watch(model, (value) => {
    emit('change', value);
});
</script>
