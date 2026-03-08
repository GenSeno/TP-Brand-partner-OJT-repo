<template>
    <Head title="Adjust Stock" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Adjust Stock</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body">
                <div class="alert alert-light border mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold"
                            >Name: {{ props.inventoryItem.item_name }}</span
                        >
                        <span
                            class="badge shadow-none badge-xs badge-soft-info"
                            >{{ props.inventoryItem.type }}</span
                        >
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Current Stock:</span>
                        <strong
                            >{{
                                simplifyFloat(props.inventoryItem.current_stock)
                            }}
                            {{ props.inventoryItem.unit_measure?.code }}</strong
                        >
                    </div>
                </div>

                <div class="new-employee-field">
                    <div class="mb-3">
                        <label class="form-label required"
                            >Adjustment Type</label
                        >
                        <vue-select
                            v-model="form.data.type"
                            :options="props.movementTypes"
                            :reduce="(option) => option.value"
                            label="label"
                            placeholder="Select type"
                        />
                        <input-error :message="form.errors.type" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Quantity</label>
                        <div class="input-group">
                            <input
                                v-model="form.data.amount"
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="form-control"
                                placeholder="Enter Quantity"
                            />
                            <span class="input-group-text">{{
                                props.inventoryItem.unit_measure?.code
                            }}</span>
                        </div>
                        <input-error :message="form.errors.amount" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Notes</label>
                        <textarea
                            v-model="form.data.notes"
                            class="form-control"
                            rows="3"
                            placeholder="Reason for adjustment..."
                        ></textarea>
                        <input-error :message="form.errors.notes" />
                    </div>

                    <div v-if="newStock !== null" class="alert alert-info mb-0">
                        <strong>New Stock:</strong> {{ newStock }}
                        {{ props.inventoryItem.unit_measure?.code }}
                    </div>
                </div>

                <div
                    v-if="props.recentMovements.length > 0"
                    class="mt-3 border-top pt-3"
                >
                    <h6 class="mb-3">Recent Adjustments</h6>
                    <div class="table-responsive" style="max-height: 200px">
                        <table
                            class="table table-sm table-bordered text-center mb-0"
                        >
                            <thead style="position: sticky; top: 0; z-index: 1">
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="movement in props.recentMovements"
                                    :key="movement.id"
                                >
                                    <td>
                                        <span
                                            class="badge"
                                            :class="`bg-light-${movement.type === 'addition' ? 'success' : 'danger'} text-${movement.type === 'addition' ? 'success' : 'danger'}`"
                                            >{{ movement.type }}</span
                                        >
                                    </td>
                                    <td>
                                        {{ movement.amount }}
                                        {{
                                            props.inventoryItem.unit_measure
                                                ?.code
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            dayjs(movement.created_at).format(
                                                'DD MMM YYYY HH:mm',
                                            )
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="page-footer-buttons">
                <button
                    type="button"
                    class="btn btn-secondary"
                    @click="close()"
                >
                    Cancel
                </button>
                <submit-btn :loading="form.processing">
                    Adjust Stock
                </submit-btn>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { Head } from '@inertiajs/vue3';
import { computed, useTemplateRef } from 'vue';
import dayjs from 'dayjs';
import { simplifyFloat } from '@/helpers/number';

const props = defineProps({
    inventoryItem: Object,
    movementTypes: Array,
    recentMovements: Array,
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    type: '',
    amount: '',
    notes: '',
});

const newStock = computed(() => {
    if (!form.data.type || !form.data.amount) return null;

    const currentStock = parseFloat(props.inventoryItem.current_stock);
    const amount = parseFloat(form.data.amount);

    if (form.data.type === 'addition') {
        return (currentStock + amount).toFixed(2);
    } else {
        return Math.max(0, currentStock - amount).toFixed(2);
    }
});

const submitForm = () => {
    form.post(
        route(
            'admin.inventory-item.process-adjustment',
            props.inventoryItem.id,
        ),
        {
            onSuccess: ({ data }) => {
                modalRef.value.close();
                emitter.emit(
                    'inventory-item:adjusted',
                    data.inventoryItem || null,
                );
                alert.showSuccess(
                    data.message || 'Stock adjusted successfully.',
                );
            },
        },
    );
};
</script>
