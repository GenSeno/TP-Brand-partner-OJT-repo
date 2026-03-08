<template>
    <Head title="Add Product" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Add Product</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <label class="form-label">Image</label>
                <div class="mb-3">
                    <avatar-upload
                        v-model:avatar="form.data.image"
                        :error-message="form.errors.image"
                    />
                </div>
                <div class="mb-3">
                    <label class="form-label required">Product Name</label>
                    <input-text v-model="form.data.name" autofocus />
                    <input-error :message="form.errors.name" />
                </div>
                <div class="mb-3">
                    <label class="form-label required">Category</label>
                    <vue-select
                        :options="props.categories"
                        v-model="form.data.category_id"
                        placeholder="Select option"
                    />
                    <input-error :message="form.errors.category_id" />
                </div>
                <div>
                    <label class="form-label">Product Remarks</label>
                    <textarea
                        v-model="form.data.description"
                        class="form-control"
                        rows="3"
                        @keypress.enter.prevent
                    ></textarea>
                    <input-error :message="form.errors.description" />
                </div>
            </div>

            <div class="page-footer-buttons">
                <div class="me-auto">
                    <label class="form-check">
                        <input
                            v-model="createAnother"
                            type="checkbox"
                            class="form-check-input"
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
                        Create Product
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
import { ref, useTemplateRef } from 'vue';

const props = defineProps({
    categories: Array,
});

const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);
const form = useAxiosForm({
    name: '',
    category_id: '',
    description: '',
    image: '',
});

const submitForm = () => {
    form.post(route('admin.product.store'), {
        onSuccess: ({ data }) => {
            if (createAnother.value) {
                form.reset();
                router.reload();
            } else {
                modalRef.value.close();
            }

            emitter.emit('product:created', data.product || null);
            alert.showSuccess(data.message || 'Product created successfully.');
        },
    });
};
</script>
