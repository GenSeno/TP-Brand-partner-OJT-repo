<template>
    <div>
        <Head title="Edit Quotation" />

        <Modal
            ref="modalRef"
            max-width="md"
            :close-explicitly="true"
            #default="{ close }"
        >
            <div class="page-header">
                <h4>Edit Information</h4>
            </div>

            <div class="page-body new-employee-field">
                <form @submit.prevent="submitForm">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label"
                                >Quotation No. : {{ quotation.reference }}
                            </label>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Date Quoted</label>
                            <VueDatePicker
                                v-model="form.data.quoted_at"
                                placeholder="Select Date"
                                :time-config="{
                                    enableTimePicker: false,
                                }"
                                :min-date="new Date()"
                                :ui="{
                                    input: form.errors['quoted_at']
                                        ? 'border-danger'
                                        : '',
                                }"
                                @update:model-value="
                                    form.clearErrors('quoted_at')
                                "
                                auto-apply
                            />

                            <input-error :message="form.errors.quoted_at" />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label"
                                >Expected Delivery Date</label
                            >
                            <VueDatePicker
                                v-model="form.data.expected_delivery"
                                placeholder="Select Date"
                                :time-config="{
                                    enableTimePicker: false,
                                }"
                                :min-date="form.data.quoted_at || new Date()"
                                :ui="{
                                    input: form.errors['expected_delivery']
                                        ? 'border-danger'
                                        : '',
                                }"
                                @update:model-value="
                                    form.clearErrors('expected_delivery')
                                "
                                auto-apply
                            />

                            <input-error
                                :message="form.errors.expected_delivery"
                            />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Validity:</label>
                            <input
                                type="number"
                                min="14"
                                class="form-control"
                                v-model="form.data.validity_days"
                                :class="{
                                    'is-invalid': form.errors['validity_days'],
                                }"
                                placeholder="Enter No. of Days"
                            />
                            <input-error
                                :message="form.errors['validity_days']"
                            />
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="page-footer-buttons">
                        <div>
                            <button
                                type="button"
                                class="btn btn-secondary me-2"
                                @click="modalRef.close()"
                            >
                                Cancel
                            </button>
                            <submit-btn :loading="form.processing"
                                >Update Data</submit-btn
                            >
                        </div>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref, useTemplateRef, computed } from 'vue';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { Head } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { VueDatePicker } from '@vuepic/vue-datepicker';

const props = defineProps({
    quotation: Object,
});
console.log('quotation', props.quotation);
const modalRef = useTemplateRef('modalRef');
const dateFormat = ref('MMMM DD, YYYY');
const date = ref();
// Populate form with existing customer data
const form = useAxiosForm({
    quoted_at: props.quotation.quoted_at ?? new Date(),
    expected_delivery: props.quotation.expected_delivery ?? null,
    validity_days: props.quotation.validity_days ?? 14,
});

// Submit form
const submitForm = () => {
    const formData = new FormData();

    formData.append(
        'quoted_at',
        form.data.quoted_at
            ? dayjs(form.data.quoted_at).format('YYYY-MM-DD HH:mm:ss')
            : '',
    );
    formData.append(
        'expected_delivery',
        form.data.expected_delivery
            ? dayjs(form.data.expected_delivery).format('YYYY-MM-DD HH:mm:ss')
            : '',
    );

    formData.append('validity_days', form.data.validity_days ?? '');
    form.submit(
        'post',
        route('admin.quotation.update_date', props.quotation.id),
        {
            data: formData,
            headers: { 'Content-Type': 'multipart/form-data' },
            onSuccess: (data) => {
                alert.showSuccess(
                    data.message || 'Quotation updated successfully.',
                );
                modalRef.value?.close(); // should close
                emitter.emit('quotation:updated', data.quotation || null);
            },
        },
    );
};
</script>
