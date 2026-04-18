<template>
    <Head title="Edit Order" />

    <Modal
        ref="modalRef"
        max-width="lg"
        :close-explicitly="true"
        v-slot="{ close }"
    >
        <div class="page-header">
            <h4>Edit Order</h4>
        </div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <h6 class="mb-3">Customer Information</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Customer Name</label>
                        <input
                            v-model="form.data.customer_name"
                            type="text"
                            class="form-control"
                            autofocus
                        />
                        <input-error :message="form.errors.customer_name" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company Name</label>
                        <input
                            v-model="form.data.company_name"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.company_name" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Email</label>
                        <input
                            v-model="form.data.customer_email"
                            type="email"
                            class="form-control"
                        />
                        <input-error :message="form.errors.customer_email" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input
                            v-model="form.data.customer_phone"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.customer_phone" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address Line 1</label>
                    <input
                        v-model="form.data.address_line1"
                        type="text"
                        class="form-control"
                        placeholder="Street / Building"
                    />
                    <input-error :message="form.errors.address_line1" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Address Line 2</label>
                    <input
                        v-model="form.data.address_line2"
                        type="text"
                        class="form-control"
                        placeholder="Unit / Floor / Suite (optional)"
                    />
                    <input-error :message="form.errors.address_line2" />
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Barangay</label>
                        <input
                            v-model="form.data.barangay"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.barangay" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City</label>
                        <input
                            v-model="form.data.city"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.city" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Province</label>
                        <input
                            v-model="form.data.province"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.province" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Postcode</label>
                        <input
                            v-model="form.data.postcode"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.postcode" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Order Date</label>
                        <input
                            v-model="form.data.placed_at"
                            type="date"
                            class="form-control"
                        />
                        <input-error :message="form.errors.placed_at" />
                    </div>
                </div>

                <hr />

                <h6 class="mb-3">Job Order</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">JO Number</label>
                        <input
                            v-model="form.data.jo_number"
                            type="text"
                            class="form-control"
                        />
                        <input-error :message="form.errors.jo_number" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">JO Status</label>
                        <select
                            v-model="form.data.jo_status"
                            class="form-select"
                        >
                            <option value="">— None —</option>
                            <option
                                v-for="(label, value) in joStatusOptions"
                                :key="value"
                                :value="value"
                            >
                                {{ label }}
                            </option>
                        </select>
                        <input-error :message="form.errors.jo_status" />
                    </div>
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
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing"
                        >Save Changes</submit-btn
                    >
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import * as alert from '@/helpers/alert';
import { Head, router } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({
    order: Object,
    joStatusOptions: Object,
});

const modalRef = useTemplateRef('modalRef');

const form = useAxiosForm({
    customer_name: props.order.customer_name ?? '',
    company_name: props.order.company_name ?? '',
    customer_email: props.order.customer_email ?? '',
    customer_phone: props.order.customer_phone ?? '',
    address_line1: props.order.address_line1 ?? '',
    address_line2: props.order.address_line2 ?? '',
    barangay: props.order.barangay ?? '',
    city: props.order.city ?? '',
    province: props.order.province ?? '',
    postcode: props.order.postcode ?? '',
    placed_at: props.order.placed_at
        ? dayjs(props.order.placed_at).format('YYYY-MM-DD')
        : dayjs().format('YYYY-MM-DD'),
    jo_number: props.order.jo_number ?? '',
    jo_status: props.order.jo_status ?? '',
    notes: props.order.notes ?? '',
});

const submitForm = () => {
    form.put(route('brand-partner.orders.update', props.order.id), {
        onSuccess: () => {
            alert.showSuccess('Order updated successfully.');
            modalRef.value.close();
            router.reload();
        },
    });
};
</script>
