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

                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input-slug
                        v-model="form.data.slug"
                        :reference="form.data.name"
                        :hide-tip="!!form.errors.slug"
                    />
                    <input-error :message="form.errors.slug" />
                </div>

                <div class="mb-3">
                    <label class="form-label required">Type</label>
                    <select v-model="form.data.type" class="form-select">
                        <option
                            v-for="(label, value) in typeOptions"
                            :key="value"
                            :value="value"
                        >
                            {{ label }}
                        </option>
                    </select>
                    <input-error :message="form.errors.type" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Position</label>
                    <input-text
                        v-model="form.data.position"
                        type="number"
                        min="0"
                    />
                    <input-error :message="form.errors.position" />
                </div>

                <div class="mb-0">
                    <div
                        class="status-toggle modal-status d-flex justify-content-between align-items-center"
                    >
                        <span class="status-label">Enabled</span>
                        <input
                            v-model="form.data.enabled"
                            type="checkbox"
                            id="enabled"
                            class="check"
                        />
                        <label for="enabled" class="checktoggle"></label>
                    </div>
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
                        Update Category
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
    category: Object,
    typeOptions: Object,
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    name: props.category.name,
    notes: props.category.notes || '',
    slug: props.category.slug,
    type: props.category.type,
    position: props.category.position,
    enabled: props.category.enabled,
});

const submitForm = () => {
    form.put(route('brand-partner.categories.update', props.category.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            alert.showSuccess(data.message || 'Category updated successfully.');
        },
    });
};
</script>
