<template>
    <div>
        <Head title="Add Expense Account" />

        <Modal
            ref="modalRef"
            max-width="md"
            :close-explicitly="true"
            #default="{ close }"
        >
            <div class="page-header">
                <h4>Add Expense Account</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="page-body new-employee-field">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label required">Name</label>
                        <input-text v-model="form.data.name" autofocus />
                        <input-error :message="form.errors.name" />
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea
                            v-model="form.data.description"
                            rows="2"
                            class="form-control"
                            placeholder="Enter description"
                        ></textarea>
                        <input-error :message="form.errors.description" />
                    </div>
                    <!-- Status -->
                    <div class="mb-3">
                        <div
                            class="status-toggle modal-status d-flex justify-content-between align-items-center"
                        >
                            <span class="status-label">Status</span>
                            <input
                                v-model="form.data.enabled"
                                type="checkbox"
                                id="enabled"
                                class="check"
                            />
                            <label for="enabled" class="checktoggle"></label>
                        </div>
                        <input-error :message="form.errors.enabled" />
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

                    <submit-btn :loading="form.processing">
                        Create Expense Account
                    </submit-btn>
                </div>
            </form>
        </Modal>
    </div>
</template>
<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { useTemplateRef } from 'vue';

const props = defineProps({
    suppliers: Array,
});

const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);

/**
 * Form State (single source of truth)
 */
const form = useAxiosForm({
    data: {
        name: null,
        description: null,
        enabled: 1,
    },
});

const submitForm = () => {
    form.submit('post', route('admin.expense_account.store'), {
        data: {
            ...form.data,
        },
        onSuccess: (response) => {
            alert.showSuccess(
                response.data.message ||
                    'Expense account created successfully.',
            );

            emitter.emit('expenseaccount:created', response);

            if (createAnother.value) {
                form.reset();
            } else {
                modalRef.value.close();
            }
        },
    });
};
</script>
