<template>
    <Head title="Add Role" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close, emit }"
    >
        <div class="page-header">
            <h4>Add Role</h4>
        </div>

        <form @submit.prevent="submitForm(emit)">
            <div class="page-body new-employee-field">
                <div class="mb-0">
                    <label class="form-label required">Role Name</label>
                    <input-text
                        v-model="form.data.name"
                        type="text"
                        autofocus
                    />
                    <input-error :message="form.errors.name" />
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
                        Create Role
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

const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);
const form = useAxiosForm({
    name: '',
});

const submitForm = () => {
    form.post(route('admin.access-control.role.store'), {
        onSuccess: ({ data }) => {
            if (createAnother.value) {
                form.reset();
                router.reload();
            } else {
                modalRef.value.close();
            }

            emitter.emit('role:created', data.role || null);
            alert.showSuccess(data.message || 'Role created successfully.');
        },
    });
};
</script>
