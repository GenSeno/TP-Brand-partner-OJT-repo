<template>
    <Head title="Edit Product" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Product</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <label class="form-label">Image</label>
                <div class="mb-3">
                    <avatar-upload
                        v-model:avatar="form.data.image"
                        v-model:logo-removed="form.data.image_removed"
                        :should-preview="!!props.product.image"
                        :default-image="getImageUrl(props.product.image)"
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
                <div class="mb-3">
                    <label class="form-label">Product Remarks</label>
                    <textarea
                        v-model="form.data.description"
                        class="form-control"
                        rows="3"
                        @keypress.enter.prevent
                    ></textarea>
                    <input-error :message="form.errors.description" />
                </div>
                <div class="mb-0">
                    <div
                        class="status-toggle modal-status d-flex justify-content-between align-items-center"
                    >
                        <span class="status-label">Published</span>
                        <input
                            v-model="enabled"
                            type="checkbox"
                            id="user2"
                            class="check"
                        />
                        <label for="user2" class="checktoggle"></label>
                    </div>
                    <input-error :message="form.errors.status" />
                </div>
            </div>

            <div class="page-footer-buttons">
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing">
                        Save Changes
                    </submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>
<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import { getImageUrl } from '@/helpers/media';
import * as alert from '@/helpers/alert';
import { Head } from '@inertiajs/vue3';
import { ref, useTemplateRef, watch } from 'vue';
import { ProductStatus } from '@/enums/product-status';

const props = defineProps({
    product: Object,
    categories: Array,
    statuses: Object,
});

const modalRef = useTemplateRef('modalRef');
const enabled = ref(props.product.status === ProductStatus.PUBLISHED);
const form = useAxiosForm({
    _method: 'put',
    name: props.product.name || '',
    category_id: props.product.category_id || '',
    description: props.product.description || '',
    image: '',
    image_removed: false,
    status: enabled.value ? ProductStatus.PUBLISHED : ProductStatus.DISABLED,
});

const submitForm = () => {
    form.post(route('admin.product.update', props.product.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            emitter.emit('product:updated', data.product || null);
            alert.showSuccess(data.message || 'Product updated successfully.');
        },
    });
};

watch(enabled, (newVal) => {
    form.data.status = newVal
        ? ProductStatus.PUBLISHED
        : ProductStatus.DISABLED;
});
</script>
