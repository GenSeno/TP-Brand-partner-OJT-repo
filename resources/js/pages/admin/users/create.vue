<template>
    <Head title="Add User" />

    <Modal
        ref="modalRef"
        max-width="lg"
        :close-explicitly="true"
        v-slot="{ close, emit }"
    >
        <div class="page-header">
            <h4>Add User</h4>
        </div>

        <form @submit.prevent="submitForm(emit)">
            <div class="page-body new-employee-field">
                <label class="form-label">Avatar</label>
                <div class="mb-3">
                    <avatar-upload
                        v-model:avatar="form.data.avatar"
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
                        <label class="form-label required">Password</label>
                        <input-text
                            v-model="form.data.password"
                            type="password"
                        />
                        <input-error :message="form.errors.password" />
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label required"
                            >Confirm Password</label
                        >
                        <input-text
                            v-model="form.data.password_confirmation"
                            type="password"
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
                        Create User
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
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useTemplateRef } from 'vue';

defineProps({
    statuses: Array,
});

const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);
const form = useAxiosForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    avatar: '',
    status: '',
    verified: false,
});

const submitForm = () => {
    form.post(route('admin.users.store'), {
        onSuccess: ({ data }) => {
            if (createAnother.value) {
                form.reset();
                router.reload();
            } else {
                modalRef.value.close();
            }

            emitter.emit('user:created', data.user || null);
            alert.showSuccess(data.message || 'User created successfully.');
        },
    });
};
</script>
