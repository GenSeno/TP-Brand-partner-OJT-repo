<template>
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold">{{ title }}</h6>

            <button
                type="button"
                class="btn btn-primary btn-xs mb-2"
                @click="$emit('add', type)"
            >
                + Add Row
            </button>
        </div>
        <div
            v-for="(row, index) in rows"
            :key="row.id"
            class="d-flex align-items-center gap-2 mb-2"
        >
            <input class="form-control" placeholder="Name" v-model="row.name" />

            <select
                v-if="showTypeDropdown"
                class="form-select w-50 form-select"
                v-model="row.adjustment_type"
            >
                <option value="fixed">Fixed</option>
                <option value="percentage">Percentage</option>
            </select>

            <input
                type="number"
                class="form-control w-50"
                placeholder="Amount"
                v-model.number="row.amount"
            />

            <button
                type="button"
                class="btn btn-icon btn-danger-light"
                @click="$emit('remove', type, index)"
            >
                <i data-feather="trash-2" class="feather-trash-2"></i>
            </button>
        </div>
    </div>
</template>
<script setup>
defineProps({
    title: String,
    type: String,
    rows: Array,
    showTypeDropdown: Boolean,
});

defineEmits(['add', 'remove']);
</script>
