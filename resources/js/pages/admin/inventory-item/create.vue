<template>
    <Head title="Add Inventory Item" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Add Inventory Item</h4>
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
                        />
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
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
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
                    <label class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            v-model="createAnother"
                        />
                        Create Another
                    </label>
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
                        Create Item
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
import { Head, router } from '@inertiajs/vue3';
import { ref, useTemplateRef } from 'vue';

const props = defineProps({
    inventoryTypes: Array,
    unitMeasures: Array,
});

const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);

const form = useAxiosForm({
    type: '',
    item_name: '',
    current_stock: 0,
    uom_code: '',
    notes: '',
    enabled: '1',
    reorder_qty: '',
});

const submitForm = () => {
    form.post(route('admin.inventory-item.store'), {
        onSuccess: ({ data }) => {
            if (createAnother.value) {
                form.reset();
                router.reload();
            } else {
                modalRef.value.close();
            }

            emitter.emit('inventory-item:created', data.inventoryItem || null);
            alert.showSuccess(
                data.message || 'Inventory item created successfully.',
            );
        },
    });
};
</script>
