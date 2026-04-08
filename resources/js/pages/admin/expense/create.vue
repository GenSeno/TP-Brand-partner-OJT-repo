<template>
    <div>
        <Head title="Add Payment Voucher" />

        <Modal
            ref="modalRef"
            max-width="md"
            :close-explicitly="true"
            #default="{ close }"
        >
            <div class="page-header">
                <h4>Add Payment Voucher</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="page-body new-employee-field">
                    <!-- Expense Date -->
                    <div class="mb-3">
                        <label class="form-label required">Expense Date</label>
                        <VueDatePicker
                            v-model="form.data.expense_date"
                            placeholder="Select Date"
                            :time-config="{ enableTimePicker: false }"
                            :ui="{
                                input: form.errors.expense_date
                                    ? 'border-danger'
                                    : '',
                            }"
                            @update:model-value="
                                form.clearErrors('expense_date')
                            "
                            auto-apply
                        />
                        <input-error :message="form.errors.expense_date" />
                    </div>

                    <!-- Supplier -->
                    <div class="mb-3">
                        <label class="form-label required">Supplier</label>
                        <vue-select
                            v-model="form.data.supplier_id"
                            :options="suppliers"
                            :reduce="(o) => o.id"
                            label="name"
                            placeholder="Select supplier"
                        />
                        <input-error :message="form.errors.supplier_id" />
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea
                            v-model="form.data.description"
                            rows="2"
                            class="form-control"
                            placeholder="Enter note"
                        ></textarea>
                        <input-error :message="form.errors.description" />
                    </div>
                </div>

                <!-- Footer Buttons -->
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

                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close"
                    >
                        Cancel
                    </button>

                    <submit-btn :loading="form.processing"
                        >Create Voucher</submit-btn
                    >
                </div>
            </form>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import dayjs from 'dayjs';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import { Head } from '@inertiajs/vue3';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { useTemplateRef } from 'vue';

const props = defineProps({
    suppliers: Array, // expects [{id: 1, name: 'agd'}, {id: 2, name: 'efg'}, ...]
});

const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);

// Default today
const form = useAxiosForm({
    expense_date: dayjs().toDate(), // ✅ Today as Date object
    supplier_id: null,
    description: null,
});

const submitForm = () => {
    form.submit('post', route('admin.expense.store'), {
        data: {
            ...form.data,
            // Format expense_date for backend
            expense_date: dayjs(form.data.expense_date).format(
                'YYYY-MM-DD HH:mm:ss',
            ),
            createAnother: createAnother.value,
        },
        onSuccess: (response) => {
            const data = response.data ?? response;

            alert.showSuccess(
                data.message || 'Payment voucher created successfully.',
            );

            emitter.emit('expense:created', data.expense ?? null);

            if (createAnother.value) {
                form.reset();
                form.data.expense_date = dayjs().toDate(); // reset to today
            } else {
                modalRef.value.close();
                setTimeout(() => {
                    window.location.href = route(
                        'admin.expense.show',
                        data.expense.id,
                    );
                }, 1000);
            }
        },
    });
};
</script>
