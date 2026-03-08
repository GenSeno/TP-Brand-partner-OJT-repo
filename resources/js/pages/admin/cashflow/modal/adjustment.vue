<template>
    <div>
        <Head title="Add Adjustment" />

        <Modal
            ref="modalRef"
            max-width="sm"
            :close-explicitly="true"
            #default="{ close }"
        >
            <div class="page-header">
                <h4>Add Adjustment</h4>
            </div>

            <div class="page-body">
                <form @submit.prevent="submitForm">
                    <!-- Type -->
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select
                            v-model="form.data.method"
                            class="form-select"
                            :class="{ 'border-danger': form.errors.type }"
                            @change="form.clearErrors('type')"
                        >
                            <option value="" disabled>Select type</option>
                            <option
                                v-for="option in typeOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <input-error :message="form.errors.method" />
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            class="form-control"
                            v-model="form.data.amount"
                            :class="{ 'border-danger': form.errors.amount }"
                            @input="form.clearErrors('amount')"
                            placeholder="0.00"
                        />
                        <input-error :message="form.errors.amount" />
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea
                            class="form-control"
                            rows="3"
                            v-model="form.data.description"
                            :class="{
                                'border-danger': form.errors.description,
                            }"
                            @input="form.clearErrors('description')"
                            placeholder="Optional notes for this adjustment"
                        />
                        <input-error :message="form.errors.description" />
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
                            >Save Adjustment</submit-btn
                        >
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';

const modalRef = ref(null);

const props = defineProps({
    group: String,
});

// Enum options for type
const typeOptions = [
    { value: 'in', label: 'Cash In' },
    { value: 'out', label: 'Cash Out' },
];

const form = useAxiosForm({
    method: '',
    amount: '',
    description: '',
    group: '',
});

const submitForm = () => {
    // Prevent submitting if type is empty
    if (!form.data.method) {
        form.setError('type', 'Please select a type.');
        return;
    }

    const formData = new FormData();
    formData.append('method', form.data.method);
    formData.append('amount', form.data.amount);
    formData.append('description', form.data.description ?? '');
    formData.append('group', props.group);

    form.submit('post', route('admin.cashflow.store.adjustment'), {
        data: formData,
        headers: { 'Content-Type': 'multipart/form-data' },
        onSuccess: (data) => {
            alert.showSuccess(data.message || 'Adjustment saved successfully.');
            modalRef.value?.close();
            emitter.emit('cashflow:updated', data.adjustment || null);
        },
    });
};
</script>
