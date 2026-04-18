<template>
    <Head title="Add Product Option" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        #default="{ close }"
    >
        <div class="page-header">
            <h4>Add Product Option</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <div class="mb-3">
                    <label class="form-label required">Option Name</label>
                    <input-text v-model="form.data.name" autofocus />
                    <input-error :message="form.errors.name" />
                </div>

                <div class="mb-3">
                    <label class="form-label required">Option Values</label>
                    <div class="row g-2 mb-2">
                        <div class="col">
                            <i>{{ labelHeader }}</i>
                        </div>
                        <div class="col">
                            <i>{{ valueHeader }}</i>
                        </div>
                        <div
                            v-if="form.data.values.length > 1"
                            class="col-auto"
                        >
                            <span class="btn-icon h-auto"></span>
                        </div>
                    </div>
                    <div
                        v-for="(val, index) in form.data.values"
                        :key="index"
                        class="row g-2 mb-2"
                    >
                        <div class="col">
                            <input-text v-model="val.label" />
                            <input-error
                                :message="form.errors[`values.${index}.label`]"
                            />
                        </div>
                        <div class="col">
                            <input-text v-model="val.value" />
                            <input-error
                                :message="form.errors[`values.${index}.value`]"
                            />
                        </div>
                        <div class="col-auto">
                            <button
                                v-if="form.data.values.length > 1"
                                type="button"
                                class="btn btn-icon rounded-pill btn-soft-danger"
                                @click="form.data.values.splice(index, 1)"
                                title="Remove"
                            >
                                <vue-feather
                                    type="trash-2"
                                    class="feather-14"
                                ></vue-feather>
                            </button>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-secondary"
                        @click="form.data.values.push({ label: '', value: '' })"
                    >
                        Add Value
                    </button>
                </div>

                <div class="mb-0">
                    <label class="form-label required">Position</label>
                    <input-text
                        v-model="form.data.position"
                        type="number"
                        min="1"
                    />
                    <input-error :message="form.errors.position" />
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
                        Create Option
                    </submit-btn>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { computed, useTemplateRef } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useAxiosForm } from '@/composables/axiosForm';
import * as alert from '@/helpers/alert';

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    name: '',
    position: 1,
    values: [{ label: '', value: '' }],
});

const labelHeader = computed(() => {
    const name = form.data.name.toLowerCase();
    if (name.includes('collection')) return 'Collection Name';
    if (name.includes('category')) return 'Category Name';
    return 'Label';
});

const valueHeader = computed(() => {
    const name = form.data.name.toLowerCase();
    if (name.includes('collection') || name.includes('category'))
        return 'Notes';
    return 'Value';
});

const submitForm = () => {
    form.post(route('brand-partner.product-options.store'), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            alert.showSuccess(
                data.message || 'Product option created successfully.',
            );
        },
    });
};
</script>
