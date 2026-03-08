<template>
    <div class="mb-5">
        <div class="mb-3 border-bottom pb-2">
            <h5>Inventory</h5>
        </div>

        <div class="row">
            <div
                v-for="material in materials"
                :key="material.state"
                class="col-lg-6 col-xl-4"
            >
                <InventoryItem
                    :jobOrder="jobOrder"
                    :material="material"
                    :inventory-emitter="inventoryEmitter"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import InventoryItem from './inventory-item.vue';

defineProps({
    jobOrder: {
        type: Object,
        required: true,
    },
    materials: {
        type: Object,
        required: true,
    },
    inventoryEmitter: {
        type: String,
        default: 'job-order-inventory:updated',
    },
});
</script>
