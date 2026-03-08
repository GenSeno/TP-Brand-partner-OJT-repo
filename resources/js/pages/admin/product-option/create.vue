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
                            <input-text
                                v-model="value.label"
                                placeholder="ex. Extra Small, Black"
                            />
                            <input-error
                                :message="form.errors[`values.${index}.label`]"
                            />
                        </div>
                        <div class="col">
                            <input-text
                                v-model="value.value"
                                placeholder="ex. XS, BLACK"
                            />
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
                <div class="mb-3">
                    <label class="form-label required">Position</label>
                    <input-text v-model="form.data.position" />
                    <input-error :message="form.errors.position" />
                </div>
                <div class="mb-0">
                    <div
                        class="status-toggle modal-status d-flex justify-content-between align-items-center"
                    >
                        <span class="status-label">Auto-Apply</span>
                        <input
                            v-model="form.data.autoapply"
                            type="checkbox"
                            id="user3"
                            class="check"
                        />
                        <label for="user3" class="checktoggle"></label>
                    </div>
                    <input-error :message="form.errors.autoapply" />
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
                        Create Option
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
    autoapply: true,
    position: 1,
    values: [
        {
            label: '',
            value: '',
        },
    ],
});

const submitForm = () => {
    form.post(route('admin.product-option.store'), {
        onSuccess: ({ data }) => {
            if (createAnother.value) {
                form.reset();
                router.reload();
            } else {
                modalRef.value.close();
            }

            emitter.emit('product-option:created', data.productOption || null);
            alert.showSuccess(
                data.message || 'Product option created successfully.',
            );
        },
    });
};
</script>
