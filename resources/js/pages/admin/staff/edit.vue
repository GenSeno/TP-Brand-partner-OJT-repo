<template>
    <Head title="Edit Staff" />

    <Modal
        ref="modalRef"
        max-width="lg"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Staff</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <label class="form-label">Avatar</label>
                <div class="mb-3">
                    <avatar-upload
                        v-model:avatar="form.data.avatar"
                        v-model:avatar-removed="form.data.avatar_removed"
                        :should-preview="!!props.staff.avatar"
                        :default-image="getImageUrl(props.staff.avatar)"
                        :error-message="form.errors.avatar"
                    />
                </div>
                <div class="row mb-3">
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
                <div class="mb-3">
                    <label class="form-label required">Email</label>
                    <input-text v-model="form.data.email" type="email" />
                    <input-error :message="form.errors.email" />
                </div>
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label class="form-label">New Password</label>
                        <input-text
                            v-model="form.data.password"
                            type="password"
                        />
                        <input-error :message="form.errors.password" />
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">Confirm Password</label>
                        <input-text
                            v-model="form.data.password_confirmation"
                            type="password"
                        />
                        <input-error
                            :message="form.errors.password_confirmation"
                        />
                    </div>
                </div>
                <div class="mb-3">
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
                <div class="mb-0">
                    <label class="form-label required">Role</label>
                    <select v-model="form.data.role" class="form-select">
                        <option value="">Select Role</option>
                        <option
                            v-for="role in roles"
                            :key="role.name"
                            :value="role.name"
                        >
                            {{ role.name }}
                        </option>
                    </select>
                    <input-error :message="form.errors.roles" />
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
                    Save Changes
                </submit-btn>
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
    staff: {
        type: Object,
        required: true,
    },
    statuses: Object,
    roles: Array,
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    _method: 'put',
    first_name: props.staff.first_name || '',
    last_name: props.staff.last_name || '',
    email: props.staff.email || '',
    password: '',
    password_confirmation: '',
    status: props.staff.status || '',
    avatar: '',
    avatar_removed: false,
    role: props.staff.current_role || '',
});

const submitForm = () => {
    form.post(route('admin.staff.update', props.staff.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            emitter.emit('staff:updated', data.staff || null);
            alert.showSuccess(data.message || 'Staff updated successfully.');
        },
    });
};
</script>
