<template>
    <Head title="Edit Product Option" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        #default="{ close }"
    >
        <div class="page-header">
            <h4>Edit Product Option</h4>
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
                            <i>Label</i>
                        </div>
                        <div class="col">
                            <i>Value</i>
                        </div>
                        <div
                            v-if="form.data.values.length > 1"
                            class="col-auto"
                        >
                            <span class="btn-icon h-auto"></span>
                        </div>
                    </div>
                    <div
                        v-for="(value, index) in form.data.values"
                        :key="index"
                        class="row g-2 mb-2"
                    >
                        <div class="col">
                            <input-text v-model="value.label" />
                            <input-error
                                :message="form.errors[`values.${index}.label`]"
                            />
                        </div>
                        <div class="col">
                            <input-text v-model="value.value" />
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
                                title="Delete value"
                            >
                                <i class="feather feather-trash-2"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <button
                            type="button"
                            class="btn btn-sm btn-secondary"
                            @click="
                                form.data.values.push({
                                    label: '',
                                    value: '',
                                })
                            "
                        >
                            Add Value
                        </button>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label required">Position</label>
                    <input-text v-model="form.data.position" />
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
                        Update Option
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
import { Head } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

const props = defineProps({
    option: Object,
});

const modalRef = useTemplateRef('modalRef');
const form = useAxiosForm({
    name: props.option?.name || '',
    autoapply: true,
    position: props.option?.position ?? 1,
    values:
        props.option?.values?.map((val) => ({
            label: val.label,
            value: val.value,
        })) || [],
});

const submitForm = () => {
    form.put(route('admin.product-option.update', props.option.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();

            emitter.emit('product-option:updated', data.productOption || null);
            alert.showSuccess(
                data.message || 'Product option updated successfully.',
            );
        },
    });
};
</script>
