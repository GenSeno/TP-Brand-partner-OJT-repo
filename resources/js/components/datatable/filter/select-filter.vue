<template>
    <div class="sf-dropdown me-2">
        <button
            type="button"
            class="sf-trigger btn btn-white btn-md d-inline-flex align-items-center"
            @click="open = !open"
        >
            {{ name }}:
            {{ selectedLabel ? selectedLabel : props.withAll ? 'All' : '' }}
            <i class="ri-arrow-down-s-line ms-1"></i>
        </button>
        <ul class="sf-menu" v-if="open">
            <li v-if="props.withAll">
                <button
                    type="button"
                    @click="select(null)"
                    class="sf-item"
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
                    @click="select(value)"
                    class="sf-item"
                    :class="{ active: model === value }"
                >
                    {{ label }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

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
const open = ref(false);

const selectedLabel = computed(() => {
    return props.options[model.value] ?? null;
});

function select(value) {
    model.value = value;
    open.value = false;
    emit('change', value);
}

function onClickOutside(e) {
    if (open.value && !e.target.closest('.sf-dropdown')) {
        open.value = false;
    }
}

onMounted(() => document.addEventListener('click', onClickOutside));
onUnmounted(() => document.removeEventListener('click', onClickOutside));
</script>

<style scoped>
.sf-dropdown {
    position: relative;
}

.sf-trigger {
    gap: 4px;
}

.sf-trigger i {
    font-size: 16px;
    transition: transform 0.2s;
}

.sf-menu {
    position: absolute;
    top: 100%;
    right: 0;
    z-index: 1055;
    min-width: 200px;
    margin-top: 4px;
    padding: 8px;
    list-style: none;
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 8px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
}

.sf-item {
    display: block;
    width: 100%;
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    background: none;
    color: #333;
    font-size: 13px;
    font-family: inherit;
    text-align: left;
    cursor: pointer;
    transition: background 0.15s;
}

.sf-item:hover {
    background: #f5f5f5;
}

.sf-item.active {
    background: #fff6e5;
    color: #fe9f43;
    font-weight: 600;
}
</style>
