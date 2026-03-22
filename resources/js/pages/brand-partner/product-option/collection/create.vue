<template>
    <Head title="Add Collection" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Add Collection</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <div class="mb-3">
                    <label class="form-label required">Name</label>
                    <input-text v-model="form.data.name" autofocus />
                    <input-error :message="form.errors.name" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea
                        v-model="form.data.notes"
                        class="form-control"
                        rows="3"
                    ></textarea>
                    <input-error :message="form.errors.notes" />
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
                        Create Collection
                    </submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import * as alert from '@/helpers/alert';
import { Head, router } from '@inertiajs/vue3';
import { ref, useTemplateRef } from 'vue';

const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);

const form = useAxiosForm({
    name: '',
    notes: '',
});

const submitForm = () => {
    form.post(route('brand-partner.product-options.collections.store'), {
        onSuccess: ({ data }) => {
            if (createAnother.value) {
                form.reset();
                router.reload();
            } else {
                modalRef.value.close();
            }
            alert.showSuccess(data.message || 'Collection created successfully.');
        },
    });
};
</script>
