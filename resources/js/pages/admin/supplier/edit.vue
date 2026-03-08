<template>
    <Head title="Edit Supplier" />

    <Modal
        ref="modalRef"
        max-width="md"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Supplier</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <label class="form-label">Logo</label>
                <div class="mb-3">
                    <avatar-upload
                        v-model:avatar="form.data.logo"
                        v-model:avatar-removed="form.data.logo_removed"
                        :should-preview="!!props.supplier.logo"
                        :default-image="getImageUrl(props.supplier.logo)"
                        :error-message="form.errors.logo"
                    />
                </div>

                <div class="mb-3">
                    <label class="form-label required">Supplier Name</label>
                    <input-text v-model="form.data.name" autofocus />
                    <input-error :message="form.errors.name" />
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Person</label>
                        <input-text v-model="form.data.contact_person" />
                        <input-error :message="form.errors.contact_person" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input-text v-model="form.data.email" type="email" />
                        <input-error :message="form.errors.email" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input-text v-model="form.data.phone" />
                        <input-error :message="form.errors.phone" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country</label>
                        <vue-select
                            v-model="form.data.country_id"
                            :options="props.countries"
                            :get-option-label="
                                (country) => `${country.emoji} ${country.name}`
                            "
                            :get-option-value="(country) => country.id"
                            placeholder="Select country"
                        />
                        <input-error :message="form.errors.country_id" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Street Address</label>
                    <input-text v-model="form.data.address" />
                    <input-error :message="form.errors.address" />
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City</label>
                        <input-text v-model="form.data.city" />
                        <input-error :message="form.errors.city" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Province</label>
                        <input-text v-model="form.data.province" />
                        <input-error :message="form.errors.province" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Postal / Zip code</label>
                        <input-text v-model="form.data.postcode" />
                        <input-error :message="form.errors.postcode" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <input-text v-model="form.data.notes" />
                    <input-error :message="form.errors.notes" />
                </div>

                <div class="mb-0">
                    <div
                        class="status-toggle modal-status d-flex justify-content-between align-items-center"
                    >
                        <span class="status-label">Enabled</span>
                        <input
                            v-model="form.data.enabled"
                            type="checkbox"
                            id="enabled2"
                            class="check"
                        />
                        <label for="enabled2" class="checktoggle"></label>
                    </div>
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
                    <submit-btn :loading="form.processing"
                        >Update Supplier</submit-btn
                    >
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { getImageUrl } from '@/helpers/media';
import { Head } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

const props = defineProps({
    supplier: Object,
    countries: { type: Array, default: () => [] },
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    _method: 'put',
    name: props.supplier.name || '',
    contact_person: props.supplier.contact_person || '',
    email: props.supplier.email || '',
    phone: props.supplier.phone || '',
    country_id: props.supplier.country_id || '',
    address: props.supplier.address || '',
    city: props.supplier.city || '',
    province: props.supplier.province || '',
    postcode: props.supplier.postcode || '',
    notes: props.supplier.notes || '',
    enabled: props.supplier.enabled ?? true,
    logo: '',
    logo_removed: false,
});

const submitForm = () => {
    form.post(route('admin.supplier.update', props.supplier.id), {
        onSuccess: ({ data }) => {
            modalRef.value.close();
            emitter.emit('supplier:updated', data.supplier || null);
            alert.showSuccess(data.message || 'Supplier updated successfully.');
        },
    });
};
</script>
