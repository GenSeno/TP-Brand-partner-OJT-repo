<template>
    <Head title="Edit Size" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Size</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <div class="mb-3">
                    <label class="form-label required">Name</label>
                    <input-text
                        v-model="form.data.name"
                        autofocus
                        placeholder="e.g. S, M, L, XL"
                    />
                    <input-error :message="form.errors.name" />
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
                        Update Size
                    </submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import * as alert from '@/helpers/alert';
import { Head } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

const props = defineProps({
    size: Object,
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    name: props.size.name,
});

const submitForm = () => {
    form.put(route('brand-partner.product-options.sizes.update', props.size.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            alert.showSuccess(data.message || 'Size updated successfully.');
        },
    });
};
</script>
