<template>
    <Head title="Edit User" />

    <Modal
        ref="modalRef"
        max-width="lg"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit User</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <label class="form-label">Avatar</label>
                <div class="mb-3">
                    <avatar-upload
                        v-model:avatar="form.data.avatar"
                        v-model:avatar-removed="form.data.avatar_removed"
                        :should-preview="!!props.user.avatar"
                        :default-image="getImageUrl(props.user.avatar)"
                        :error-message="form.errors.avatar"
                    />
                </div>
                <div class="mb-3">
                    <label class="form-label required">Name</label>
                    <input-text v-model="form.data.name" />
                    <input-error :message="form.errors.name" />
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
                    <div
                        class="status-toggle modal-status d-flex justify-content-between align-items-center"
                    >
                        <span class="status-label">Verified</span>
                        <input
                            v-model="form.data.verified"
                            type="checkbox"
                            id="user2"
                            class="check"
                        />
                        <label for="user2" class="checktoggle"></label>
                    </div>
                    <input-error :message="form.errors.verified" />
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
    user: {
        type: Object,
        required: true,
    },
    statuses: Object,
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    _method: 'put',
    name: props.user.name || '',
    email: props.user.email || '',
    password: '',
    password_confirmation: '',
    status: props.user.status || '',
    avatar: '',
    avatar_removed: false,
    verified: !!props.user.email_verified_at || false,
});

const submitForm = () => {
    form.post(route('admin.users.update', props.user.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            emitter.emit('user:updated', data.user || null);
            alert.showSuccess(data.message || 'User updated successfully.');
        },
    });
};
</script>
