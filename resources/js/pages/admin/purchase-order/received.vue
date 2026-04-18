<template>
    <Head title="Received Purchase Order" />

    <Modal
        ref="modalRef"
        max-width="7xl"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Received Purchase Order</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <div class="row g-3">
                    <!-- Supplier -->
                    <div class="col-md-4">
                        <label class="form-label">Supplier</label>
                        <select
                            class="form-control"
                            v-model="form.data.supplier_id"
                            disabled
                        >
                            <option
                                v-for="supplier in props.suppliers"
                                :key="supplier.value"
                                :value="supplier.value"
                            >
                                {{ supplier.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Order Date -->
                    <div class="col-md-4">
                        <label class="form-label">Order Date</label>
                        <VueDatePicker
                            v-model="form.data.order_date"
                            placeholder="Select Date"
                            :time-config="{ enableTimePicker: false }"
                            auto-apply
                            @update:model-value="form.clearErrors('order_date')"
                            :disabled="true"
                        />
                        <input-error :message="form.errors.order_date" />
                    </div>

                    <!-- Expected Delivery -->
                    <div class="col-md-4">
                        <label class="form-label">Date Received</label>
                        <VueDatePicker
                            v-model="form.data.date_received"
                            placeholder="Select Date"
                            :time-config="{ enableTimePicker: false }"
                            auto-apply
                            @update:model-value="
                                form.clearErrors('date_received')
                            "
                        />
                        <input-error :message="form.errors.date_received" />
                    </div>
                </div>

                <!-- Lines Table -->
                <div class="modal-body-table mt-3">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th width="15%">Unit Price</th>
                                <th width="15%">Qty Ordered</th>
                                <th width="15%">Qty Received</th>
                                <th width="15%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Existing Lines -->
                            <tr
                                v-for="(line, idx) in form.data.lines"
                                :key="line.id || line.temp_id"
                            >
                                <td>
                                    <select
                                        class="form-control"
                                        v-model="line.inventory_item_id"
                                        disabled
                                    >
                                        <option
                                            v-for="inventoryd in props.inventory"
                                            :key="inventoryd.value"
                                            :value="inventoryd.value"
                                        >
                                            {{ inventoryd.label }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <input-text
                                        v-model.number="line.unit_price"
                                    />
                                </td>
                                <td>
                                    <input-text
                                        v-model.number="line.quantity"
                                        disabled
                                    />
                                </td>
                                <td>
                                    <input-text
                                        v-model.number="line.qty_received"
                                    />
                                </td>
                                <td>
                                    <input-text
                                        v-if="line.qty_received > 1"
                                        :model-value="lineReceivedTotal(line)"
                                        disabled
                                    />
                                    <input-text
                                        v-else
                                        :model-value="lineTotal(line)"
                                        disabled
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input-error :message="form.errors.lines" />
                </div>

                <!-- Notes -->
                <div class="mb-3">
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
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing"
                        >Received Purchase Order</submit-btn
                    >
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { reactive, computed, ref } from 'vue';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import dayjs from 'dayjs';

const props = defineProps({
    purchaseOrder: Object,
    suppliers: Array,
    inventory: Array,
});
const modalRef = ref(null);

// Form with existing purchase order data

// Format money
const formatMoney = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2,
    }).format(amount);
};

const form = useAxiosForm({
    supplier_id: props.purchaseOrder.supplier_id || '',
    date_received: props.purchaseOrder.date_received || '',
    order_date: props.purchaseOrder.order_date || '',
    notes: props.purchaseOrder.notes || '',
    lines: props.purchaseOrder.lines.map((line) =>
        reactive({
            ...line,
            unit_price: Number(line.unit_price.decimal ?? line.unit_price ?? 0),
            quantity: line.quantity,
            qty_received: line.qty_received,
        }),
    ),
    deleted_line_ids: [],
});

// Reactive new line
const newLine = reactive({
    item: null,
    unit_price: '',
    quantity: '',
    qty_received: '',
});

const lineReceivedTotal = (line) => {
    return formatMoney((line.unit_price || 0) * (line.qty_received || 0));
};

const lineTotal = (line) => {
    return formatMoney((line.unit_price || 0) * (line.quantity || 0));
};

// Submit form
const submitForm = () => {
    form.post(route('admin.purchase-order.received', props.purchaseOrder.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            emitter.emit('purchase-order:updated', data.purchaseOrder || null);
            alert.showSuccess(
                data.message || 'Purchase Order received successfully.',
            );
        },
    });
};

defineEmits(['modalEvent']);
</script>
