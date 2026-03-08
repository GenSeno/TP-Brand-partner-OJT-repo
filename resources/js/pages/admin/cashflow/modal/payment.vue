<template>
    <div>
        <Head title="Post Payment" />

        <Modal
            ref="modalRef"
            max-width="sm"
            :close-explicitly="true"
            #default="{ close }"
        >
            <div class="page-header">
                <h4>Post Payment</h4>
            </div>

            <div class="page-body">
                <form @submit.prevent="submitForm">
                    <div class="mb-3">
                        <label class="form-label">Transaction Reference:</label>
                        <p>{{ transaction.reference }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date Posted</label>
                        <VueDatePicker
                            v-model="form.data.posted_at"
                            placeholder="Select Date"
                            :time-config="{ enableTimePicker: false }"
                            :ui="{
                                input: form.errors['posted_at']
                                    ? 'border-danger'
                                    : '',
                            }"
                            @update:model-value="form.clearErrors('posted_at')"
                            auto-apply
                        />
                        <input-error :message="form.errors.posted_at" />
                    </div>

                    <!-- Buttons -->
                    <div class="page-footer-buttons">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="modalRef.close()"
                        >
                            Cancel
                        </button>
                        <submit-btn :loading="form.processing"
                            >Post Payment</submit-btn
                        >
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { Head } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { VueDatePicker } from '@vuepic/vue-datepicker';

const props = defineProps({
    transaction: Object, // payment object
    default_date: String, // e.g., today
});

const modalRef = ref(null);

const form = useAxiosForm({
    posted_at: props.default_date,
});

const submitForm = () => {
    const formData = new FormData();
    formData.append(
        'posted_at',
        form.data.posted_at
            ? dayjs(form.data.posted_at).format('YYYY-MM-DD')
            : '',
    );

    form.submit(
        'post',
        route('admin.cashflow.post.payment', props.transaction.id),
        {
            data: formData,
            headers: { 'Content-Type': 'multipart/form-data' },
            onSuccess: (data) => {
                alert.showSuccess(
                    data.message || 'Payment posted successfully.',
                );
                modalRef.value?.close(); // closes modal
                emitter.emit('cashflow:updated', data.transaction || null); // table can listen and refresh
            },
        },
    );
};
</script>
