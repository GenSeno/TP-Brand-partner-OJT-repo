<template>
    <Head title="Edit Inventory Item" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Inventory Item</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <div class="mb-3">
                    <label class="form-label required">Type</label>
                    <vue-select
                        v-model="form.data.type"
                        :options="props.inventoryTypes"
                        :reduce="(option) => option.value"
                        label="label"
                        placeholder="Select type"
                    />
                    <input-error :message="form.errors.type" />
                </div>
                <div class="mb-3">
                    <label class="form-label required">Item Name</label>
                    <input-text v-model="form.data.item_name" autofocus />
                    <input-error :message="form.errors.item_name" />
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Current Stock</label>
                        <input-text
                            v-model="form.data.current_stock"
                            type="number"
                            step="0.01"
                            min="0"
                            disabled
                        />
                        <small class="text-muted"
                            >Use adjust stock to change quantity</small
                        >
                        <input-error :message="form.errors.current_stock" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reorder Qty</label>
                        <input-text
                            v-model="form.data.reorder_qty"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                        <input-error :message="form.errors.reorder_qty" />
                    </div>
                    <div class="col-md-12  mb-3">
                         <label class="form-label required"
                            >Unit of Measure</label
                        >
                        <vue-select
                            v-model="form.data.uom_code"
                            :options="props.unitMeasures"
                            :reduce="(option) => option.code"
                            label="name"
                            placeholder="Select UOM"
                        >
                            <template #value="{ option }">
                                {{ option.label }} ({{ option.value }})
                            </template>
                            <template #option="{ option }">
                                {{ option.label }} ({{ option.value }})
                            </template>
                        </vue-select>
                        <input-error :message="form.errors.uom_code" />
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label">Notes</label>
                    <textarea
                        v-model="form.data.notes"
                        class="form-control"
                        rows="3"
                        placeholder="Additional notes..."
                    ></textarea>
                    <input-error :message="form.errors.notes" />
                </div>
            </div>
            <div class="page-footer-buttons">
                <div class="me-auto">
                    <div class="status-toggle modal-status d-flex justify-content-between align-items-center">
                        <input
                            v-model="form.data.enabled"
                            type="checkbox"
                            id="enabled"
                            class="check"
                            :true-value="1"
                            :false-value="0"
                        />
                        <label for="enabled" class="checktoggle"></label>
                    </div>
                    <input-error :message="form.errors.enabled" />
                </div>
                <div>
                    <button
                    type="button"
                    class="btn btn-secondary me-2"
                    @click="close()"
                     >
                    Cancel
                    </button>
                    <submit-btn :loading="form.processing">
                        Update Item
                    </submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { Head } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

const props = defineProps({
    inventoryItem: Object,
    inventoryTypes: Object,
    unitMeasures: Object,
});
console.log('data',props.inventoryItem);
const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    type: props.inventoryItem.type,
    item_name: props.inventoryItem.item_name,
    current_stock: props.inventoryItem.current_stock,
    uom_code: props.inventoryItem.uom_code,
    notes: props.inventoryItem.notes || '',
    enabled: props.inventoryItem.enabled || '',
    reorder_qty: props.inventoryItem.reorder_qty || '',
});
console.log('form', form);
const submitForm = () => {
    form.patch(route('admin.inventory-item.update', props.inventoryItem.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            emitter.emit('inventory-item:updated', data.inventoryItem || null);
            alert.showSuccess(
                data.message || 'Inventory item updated successfully.',
            );
        },
    });
};
</script>
