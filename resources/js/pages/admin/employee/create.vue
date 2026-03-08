<template>
    <Head title="Add Employee" />

    <Modal
        ref="modalRef"
        max-width="lg"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Add Employee</h4>
        </div>

        <form @submit.prevent="submitForm" class="form">
            <div class="page-body new-employee-field">
                <label class="form-label">Avatar</label>
                <div class="mb-3">
                    <avatar-upload
                        v-model:avatar="form.data.avatar"
                        :error-message="form.errors.avatar"
                    />
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-lg-6">
                        <label class="form-label required">First Name</label>
                        <input-text v-model="form.data.first_name" autofocus />
                        <input-error :message="form.errors.first_name" />
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label required">Last Name</label>
                        <input-text v-model="form.data.last_name" />
                        <input-error :message="form.errors.last_name" />
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-lg-6">
                        <label class="form-label required">Status</label>
                        <select v-model="form.data.status" class="form-select">
                            <option value="">Select Status</option>
                            <option
                                v-for="(label, status) in statuses"
                                :key="status"
                                :value="status"
                            >
                                {{ label }}
                            </option>
                        </select>
                        <input-error :message="form.errors.status" />
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label required">Job Title</label>
                        <select
                            v-model="form.data.job_title"
                            class="form-select"
                        >
                            <option value="">Select Job Title</option>
                            <option
                                v-for="(label, value) in jobTitles"
                                :key="value"
                                :value="value"
                            >
                                {{ label }}
                            </option>
                        </select>
                        <input-error :message="form.errors.job_title" />
                    </div>
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
                        Create Employee
                    </submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>
<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import { Head } from '@inertiajs/vue3';
import { ref, useTemplateRef } from 'vue';

defineProps({
    statuses: Object,
    jobTitles: Object,
});

const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);
const form = useAxiosForm({
    first_name: '',
    last_name: '',
    job_title: '',
    status: '',
    avatar: null,
});

const submitForm = () => {
    form.post(route('admin.employee.store'), {
        onSuccess: ({ data }) => {
            if (createAnother.value) {
                form.reset();
                router.reload();
            } else {
                modalRef.value.close();
            }

            emitter.emit('employee:created', data.employee || null);
            alert.showSuccess(data.message || 'Employee created successfully.');
        },
    });
};
</script>
