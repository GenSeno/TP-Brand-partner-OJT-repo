<template>
    <div>
        <Head title="Edit Expense Account" />

        <Modal
            ref="modalRef"
            max-width="md"
            :close-explicitly="true"
            #default="{ close }"
        >
            <div class="page-header">
                <h4>Edit Expense Account</h4>
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
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close"
                    >
                        Cancel
                    </button>

                    <submit-btn :loading="form.processing">
                        Update Expense Account
                    </submit-btn>
                </div>
            </form>
        </Modal>
    </div>
</template>
<script setup>
import { ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { useTemplateRef } from 'vue';

const props = defineProps({
    account: {
        type: Object,
        required: true,
    },
});

const modalRef = useTemplateRef('modalRef');

/**
 * Form State
 */
const form = useAxiosForm({
    data: {
        name: null,
        description: null,
        enabled: false,
    },
});

/**
 * Prefill when account changes
 */
watch(
    () => props.account,
    (account) => {
        if (!account) return;

        form.data.name = account.name;
        form.data.description = account.description;
        form.data.enabled = !!account.enabled;
    },
    { immediate: true },
);

/**
 * Submit Update
 */
const submitForm = () => {
    form.submit(
        'post',
        route('admin.expense_account.update', props.account.id),
        {
            data: {
                ...form.data,
            },
            onSuccess: (response) => {
                alert.showSuccess(
                    response.data.message ||
                        'Expense account updated successfully.',
                );

                emitter.emit('expenseaccount:updated', response);
                modalRef.value.close();
            },
        },
    );
};
</script>
