<template>
    <Head title="Add Category" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close, emit }"
    >
        <div class="page-header">
            <h4>Add Category</h4>
        </div>

        <form @submit.prevent="submitForm(emit)">
            <div class="page-body new-employee-field">
                <label class="form-label">Logo</label>
                <div class="mb-3">
                    <avatar-upload
                        v-model:avatar="form.data.logo"
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
                        Create Category
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
    slug: '',
    logo: '',
    enabled: true,
});

const submitForm = () => {
    form.post(route('admin.category.store'), {
        onSuccess: ({ data }) => {
            if (createAnother.value) {
                form.reset();
                router.reload();
            } else {
                modalRef.value.close();
            }

            emitter.emit('category:created', data.category || null);
            alert.showSuccess(data.message || 'Category created successfully.');
        },
    });
};
</script>
