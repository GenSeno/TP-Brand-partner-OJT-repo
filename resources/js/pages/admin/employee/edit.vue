<template>
    <Head title="Edit Employee" />
    <Modal
        ref="modalRef"
        max-width="lg"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Employee</h4>
        </div>
        <form @submit.prevent="submitForm" class="form">
            <div class="page-body new-employee-field">
                <label class="form-label">Avatar</label>
                <div class="mb-3">
                    <avatar-upload
                        v-model:avatar="form.data.avatar"
                        v-model:avatar-removed="form.data.avatar_removed"
                        :should-preview="!!props.employee.avatar"
                        :default-image="getImageUrl(props.employee.avatar)"
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
                <div class="me-auto"></div>
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing">
                        Update Employee
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
import { getImageUrl } from '@/helpers/media';
import { Head } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

const props = defineProps({
    employee: Object,
    statuses: Object,
    jobTitles: Object,
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    _method: 'put',
    first_name: props.employee.first_name,
    last_name: props.employee.last_name,
    job_title: props.employee.job_title,
    status: props.employee.status,
    avatar: '',
    avatar_removed: false,
});

const submitForm = () => {
    form.post(route('admin.employee.update', { employee: props.employee.id }), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            emitter.emit('employee:updated', data.employee || null);
            alert.showSuccess(data.message || 'Employee updated successfully.');
        },
    });
};
</script>
