<template>
    <Head title="Edit Category" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Category</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <label class="form-label">Logo</label>
                <div class="mb-3">
                    <avatar-upload
                        v-model:avatar="form.data.logo"
                        v-model:logo-removed="form.data.logo_removed"
                        :should-preview="!!props.category.logo"
                        :default-image="getImageUrl(props.category.logo)"
                        :error-message="form.errors.logo"
                    />
                </div>
                <div class="mb-3">
                    <label class="form-label required">Category</label>
                    <input-text v-model="form.data.name" autofocus />
                    <input-error :message="form.errors.name" />
                </div>
                <div class="mb-3">
                    <label class="form-label required">Slug (URL)</label>
                    <input-slug
                        v-model="form.data.slug"
                        :reference="form.data.name"
                        :hide-tip="!!form.errors.slug"
                    />
                    <input-error :message="form.errors.slug" />
                </div>
                <div class="mb-0">
                    <div
                        class="status-toggle modal-status d-flex justify-content-between align-items-center"
                    >
                        <span class="status-label">Enabled</span>
                        <input
                            v-model="form.data.enabled"
                            type="checkbox"
                            id="user2"
                            class="check"
                        />
                        <label for="user2" class="checktoggle"></label>
                    </div>
                    <input-error :message="form.errors.enabled" />
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
    category: {
        type: Object,
        required: true,
    },
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    _method: 'put',
    name: props.category.name || '',
    slug: props.category.slug || '',
    logo: '',
    logo_removed: false,
    enabled: props.category.enabled || false,
});

const submitForm = () => {
    form.post(route('admin.category.update', props.category.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            emitter.emit('category:updated', data.category || null);
            alert.showSuccess(data.message || 'Category updated successfully.');
        },
    });
};
</script>
