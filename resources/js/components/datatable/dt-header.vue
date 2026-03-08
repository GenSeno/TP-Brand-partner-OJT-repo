<template>
    <thead>
        <tr>
            <th v-if="selectable">
                <div class="form-check form-check-md selectable">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        :checked="checked"
                        @change="emit('checked', $event.target.checked)"
                    />
                </div>
            </th>
            <th
                v-for="column in columns"
                :key="column.key"
                :class="column.class ?? ''"
            >
                <button
                    v-if="column.sortable"
                    type="button"
                    class="btn btn-light-ghost"
                    @click="toggleSort(column.dataIndex)"
                >
                    <div
                        class="d-flex align-items-center justify-content-between gap-2"
                    >
                        <span>{{ column.title }}</span>
                        <div>
                            <i
                                v-show="isAscending(column.dataIndex)"
                                class="ti ti-arrow-up"
                            ></i>
                            <i
                                v-show="isDescending(column.dataIndex)"
                                class="ti ti-arrow-down"
                            ></i>
                            <i
                                v-show="isUnsorted(column.dataIndex)"
                                class="ti ti-selector"
                            ></i>
                        </div>
                    </div>
                </button>
                <span v-else>{{ column.title }}</span>
            </th>
        </tr>
    </thead>
</template>
<script setup>
const sortings = defineModel({ type: Array });

const emit = defineEmits(['sort', 'checked']);

defineProps({
    selectable: {
        type: Boolean,
        default: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    checked: {
        type: Boolean,
        default: false,
    },
});

function toggleSort(key) {
    let items = [...sortings.value];
    let existing = items.find((s) => s === key || s === `-${key}`);
    if (!existing) {
        items.push(key);
    } else if (!existing.startsWith('-')) {
        items = items.map((s) => (s === existing ? `-${key}` : s));
    } else {
        items = items.filter((s) => s !== existing);
    }
    sortings.value = items;

    emit('sort');
}

function isUnsorted(key) {
    return !sortings.value.find((s) => s === key || s === `-${key}`);
}

function isAscending(key) {
    return sortings.value.includes(key);
}

function isDescending(key) {
    return sortings.value.includes(`-${key}`);
}

defineOptions({
    inheritAttrs: false,
});
</script>
