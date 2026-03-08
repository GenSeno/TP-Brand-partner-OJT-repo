<template>
    <Head title="Add Brand Partner" />

    <Modal
        ref="modalRef"
        max-width="lg"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Add Brand Partner</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Brand Name</label>
                        <input-text v-model="form.data.name" autofocus />
                        <input-error :message="form.errors.name" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug</label>
                        <input-slug
                            v-model="form.data.slug"
                            :reference="form.data.name"
                            :hide-tip="!!form.errors.slug"
                        />
                        <input-error :message="form.errors.slug" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Email</label>
                        <input-text v-model="form.data.email" type="email" />
                        <input-error :message="form.errors.email" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Password</label>
                        <input-text
                            v-model="form.data.password"
                            type="password"
                        />
                        <input-error :message="form.errors.password" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Person</label>
                        <input-text v-model="form.data.contact_person" />
                        <input-error :message="form.errors.contact_person" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input-text v-model="form.data.phone" />
                        <input-error :message="form.errors.phone" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea
                        v-model="form.data.address"
                        class="form-control"
                        rows="2"
                    ></textarea>
                    <input-error :message="form.errors.address" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea
                        v-model="form.data.description"
                        class="form-control"
                        rows="3"
                    ></textarea>
                    <input-error :message="form.errors.description" />
                </div>

                <div class="mb-0">
                    <label class="form-label">Status</label>
                    <select v-model="form.data.status" class="form-select">
                        <option
                            v-for="(label, value) in statusOptions"
                            :key="value"
                            :value="value"
                        >
                            {{ label }}
                        </option>
                    </select>
                    <input-error :message="form.errors.status" />
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
                        Create Brand Partner
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

const props = defineProps({
    statusOptions: Object,
});

const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);

const form = useAxiosForm({
    name: '',
    slug: '',
    email: '',
    password: '',
    contact_person: '',
    phone: '',
    address: '',
    description: '',
    status: 'active',
});

const submitForm = () => {
    form.post(route('admin.brand-partners.store'), {
        onSuccess: ({ data }) => {
            if (createAnother.value) {
                form.reset();
                router.reload();
            } else {
                modalRef.value.close();
            }
            alert.showSuccess(
                data.message || 'Brand partner created successfully.',
            );
        },
    });
};
</script>
