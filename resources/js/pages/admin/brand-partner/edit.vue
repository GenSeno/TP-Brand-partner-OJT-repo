<template>
    <Head title="Edit Brand Partner" />

    <Modal
        ref="modalRef"
        max-width="lg"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Brand Partner</h4>
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
                        <label class="form-label">New Password</label>
                        <input-text
                            v-model="form.data.password"
                            type="password"
                            placeholder="Leave empty to keep current"
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
                        Update Brand Partner
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
    brandPartner: Object,
    statusOptions: Object,
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    name: props.brandPartner.name,
    slug: props.brandPartner.slug,
    email: props.brandPartner.email,
    password: '',
    contact_person: props.brandPartner.contact_person || '',
    phone: props.brandPartner.phone || '',
    address: props.brandPartner.address || '',
    description: props.brandPartner.description || '',
    status: props.brandPartner.status,
});

const submitForm = () => {
    form.put(route('admin.brand-partners.update', props.brandPartner.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            alert.showSuccess(
                data.message || 'Brand partner updated successfully.',
            );
        },
    });
};
</script>
