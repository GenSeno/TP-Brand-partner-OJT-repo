<template>
    <Head title="Edit Role" />

    <Modal
        ref="modalRef"
        max-width="lg"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Role</h4>
        </div>

        <form @submit.prevent="submitForm">
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
import { Head } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    name: props.role.name || '',
});

const submitForm = () => {
    form.put(route('admin.access-control.role.update', props.role.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            emitter.emit('role:updated', data.role || null);
            alert.showSuccess(data.message || 'Role updated successfully.');
        },
    });
};
</script>
