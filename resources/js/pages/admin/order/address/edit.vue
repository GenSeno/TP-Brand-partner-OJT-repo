<template>
    <Head title="Edit Address" />

    <Modal ref="modalRef" max-width="3xl" :close-explicitly="true">
        <div class="page-header"><h4>Edit Address</h4></div>

        <form @submit.prevent="submitForm">
            <div class="page-body new-employee-field">
                <!-- Billing Fields -->
                <h6 class="mb-3">Billing Address</h6>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Title</label>
                        <select
                            v-model="form.data.address.title"
                            class="form-select"
                        >
                            <option value="">None</option>
                            <option value="Mr.">Mr.</option>
                            <option value="Ms.">Ms.</option>
                            <option value="Mrs.">Mrs.</option>
                            <option value="Dr.">Dr.</option>
                            <option value="Prof.">Prof.</option>
                            <option value="Rev.">Rev.</option>
                        </select>
                        <input-error :message="form.errors['address.title']" />
                    </div>

                    <div class="col-md-5 mb-3">
                        <label class="form-label required">First Name</label>
                        <input-text v-model="form.data.address.first_name" />
                        <input-error
                            :message="form.errors['address.first_name']"
                        />
                    </div>

                    <div class="col-md-5 mb-3">
                        <label class="form-label required">Last Name</label>
                        <input-text v-model="form.data.address.last_name" />
                        <input-error
                            :message="form.errors['address.last_name']"
                        />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Company Name</label>
                    <input-text v-model="form.data.address.company_name" />
                    <input-error
                        :message="form.errors['address.company_name']"
                    />
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Email</label>
                        <input-text
                            v-model="form.data.address.email"
                            type="email"
                        />
                        <input-error :message="form.errors['address.email']" />
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Phone</label>
                        <input-text v-model="form.data.address.phone" />
                        <input-error :message="form.errors['address.phone']" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Street Address</label>
                    <input-text
                        v-model="form.data.address.line1"
                        placeholder="House number and street name"
                    />
                    <input-error :message="form.errors['address.line1']" />
                </div>

                <div class="mb-3">
                    <input-text
                        v-model="form.data.address.line2"
                        placeholder="Apartment, suite, unit, etc."
                    />
                    <input-error :message="form.errors['address.line2']" />
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required"
                            >Country/Region</label
                        >
                        <vue-select
                            v-model="form.data.address.country_id"
                            :options="props.countries"
                            :get-option-label="
                                (country) => `${country.emoji} ${country.name}`
                            "
                            :get-option-value="(country) => country.id"
                            label="name"
                            placeholder="Select a country"
                        />
                        <input-error
                            :message="form.errors['address.country_id']"
                        />
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Province</label>
                        <input-text v-model="form.data.address.province" />
                        <input-error
                            :message="form.errors['address.province']"
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">City</label>
                        <vue-select
                            v-model="form.data.address.city"
                            :options="states.billing"
                            :get-option-label="(city) => city.name"
                            :get-option-value="(city) => city.name"
                            label="city"
                            placeholder="Select a city"
                        />
                        <input-error :message="form.errors['address.city']" />
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Barangay</label>
                        <input-text v-model="form.data.address.barangay" />
                        <input-error
                            :message="form.errors['address.barangay']"
                        />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Postal / Zip Code</label>
                    <input-text v-model="form.data.address.postcode" />
                    <input-error :message="form.errors['address.postcode']" />
                </div>

                <div class="mb-3">
                    <label class="form-label">When do you need it?</label>
                    <input-text v-model="form.data.need" />
                    <input-error :message="form.errors.need" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea
                        v-model="form.data.notes"
                        class="form-control"
                        rows="4"
                        placeholder="Enter Additional details for order"
                    ></textarea>
                    <input-error :message="form.errors.notes" />
                </div>

                <div class="mb-3 mt-4">
                    <label class="form-check">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            v-model="differentShipping"
                        />
                        Ship to a different address ?
                    </label>
                </div>

                <!-- Shipping Fields -->
                <div v-if="differentShipping" class="shipping-fields">
                    <h6 class="mb-3">Shipping Address</h6>

                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Title</label>
                            <select
                                v-model="form.data.shipping.title"
                                class="form-select"
                            >
                                <option value="">None</option>
                                <option value="Mr.">Mr.</option>
                                <option value="Ms.">Ms.</option>
                                <option value="Mrs.">Mrs.</option>
                                <option value="Dr.">Dr.</option>
                                <option value="Prof.">Prof.</option>
                                <option value="Rev.">Rev.</option>
                            </select>
                            <input-error
                                :message="form.errors['shipping.title']"
                            />
                        </div>

                        <div class="col-md-5 mb-3">
                            <label class="form-label required"
                                >First Name</label
                            >
                            <input-text
                                v-model="form.data.shipping.first_name"
                            />
                            <input-error
                                :message="form.errors['shipping.first_name']"
                            />
                        </div>

                        <div class="col-md-5 mb-3">
                            <label class="form-label required">Last Name</label>
                            <input-text
                                v-model="form.data.shipping.last_name"
                            />
                            <input-error
                                :message="form.errors['shipping.last_name']"
                            />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input-text v-model="form.data.shipping.company_name" />
                        <input-error
                            :message="form.errors['shipping.company_name']"
                        />
                    </div>

                    <div class="mb-3">
                        <label class="form-label required"
                            >Street Address</label
                        >
                        <input-text
                            v-model="form.data.shipping.line1"
                            placeholder="House number and street name"
                        />
                        <input-error :message="form.errors['shipping.line1']" />
                    </div>

                    <div class="mb-3">
                        <input-text
                            v-model="form.data.shipping.line2"
                            placeholder="Apartment, suite, unit, etc."
                        />
                        <input-error :message="form.errors['shipping.line2']" />
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required"
                                >Country/Region</label
                            >
                            <vue-select
                                v-model="form.data.shipping.country_id"
                                :options="props.countries"
                                :get-option-label="
                                    (country) =>
                                        `${country.emoji} ${country.name}`
                                "
                                :get-option-value="(country) => country.id"
                                label="name"
                                placeholder="Select a country"
                            />
                            <input-error
                                :message="form.errors['shipping.country_id']"
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Province</label>
                            <input-text v-model="form.data.shipping.province" />
                            <input-error
                                :message="form.errors['shipping.province']"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">City</label>
                            <vue-select
                                v-model="form.data.shipping.city"
                                :options="states.shipping"
                                :get-option-label="(city) => city.name"
                                :get-option-value="(city) => city.name"
                                label="city"
                                placeholder="Select a city"
                            />
                            <input-error
                                :message="form.errors['shipping.city']"
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barangay</label>
                            <input-text v-model="form.data.shipping.barangay" />
                            <input-error
                                :message="form.errors['shipping.barangay']"
                            />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required"
                            >Postal / Zip Code</label
                        >
                        <input-text v-model="form.data.shipping.postcode" />
                        <input-error
                            :message="form.errors['shipping.postcode']"
                        />
                    </div>
                </div>
            </div>

            <div class="page-footer-buttons">
                <div>
                    <button
                        type="button"
                        class="btn btn-secondary me-2"
                        @click="modalRef.close()"
                    >
                        Cancel
                    </button>
                    <submit-btn :loading="form.processing"
                        >Update Sales Order</submit-btn
                    >
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import { ref, useTemplateRef, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';

const props = defineProps({
    order: Object,
    customers: Array,
    countries: Object,
});
const modalRef = useTemplateRef('modalRef');

const differentShipping = ref(false);

// Check if shipping address is different from billing
const billingAddress = props.order.billing_address || {};
const shippingAddress = props.order.shipping_address || {};
const states = ref({
    billing: [],
    shipping: [],
});

// If shipping exists and differs from billing, mark as different
if (
    shippingAddress.line1 &&
    (shippingAddress.line1 !== billingAddress.line1 ||
        shippingAddress.city !== billingAddress.city)
) {
    differentShipping.value = true;
}

const form = useAxiosForm({
    address: {
        title: billingAddress.title || '',
        first_name: billingAddress.first_name || '',
        last_name: billingAddress.last_name || '',
        company_name: billingAddress.company_name || '',
        email: billingAddress.email || '',
        phone: billingAddress.phone || '',
        line1: billingAddress.line1 || '',
        line2: billingAddress.line2 || '',
        province: billingAddress.province || '',
        city: billingAddress.city || '',
        barangay: billingAddress.barangay || '',
        postcode: billingAddress.postcode || '',
        country_id: billingAddress.country_id || '',
    },
    shipping: {
        title: shippingAddress.title || '',
        first_name: shippingAddress.first_name || '',
        last_name: shippingAddress.last_name || '',
        company_name: shippingAddress.company_name || '',
        line1: shippingAddress.line1 || '',
        line2: shippingAddress.line2 || '',
        province: shippingAddress.province || '',
        city: shippingAddress.city || '',
        barangay: shippingAddress.barangay || '',
        postcode: shippingAddress.postcode || '',
        country_id: shippingAddress.country_id || '',
    },
    need: props.order.need || '',
    notes: props.order.notes || '',
});

watch(
    () => form.data.address.country_id,
    (value) => {
        axios
            .post(route('admin.address.states'), {
                country_id: value,
            })
            .then(({ data }) => {
                states.value.billing = data;
            });
    },
    { immediate: true, deep: true },
);

watch(
    () => form.data.shipping.country_id,
    (value) => {
        axios
            .post(route('admin.address.states'), {
                country_id: value,
            })
            .then(({ data }) => {
                states.value.shipping = data;
            });
    },
    { immediate: true, deep: true },
);

const submitForm = () => {
    const payload = {
        customer_id: form.data.customer_id,
        notes: form.data.notes,
        need: form.data.need,
        address: form.data.address,
        shipping: differentShipping.value ? form.data.shipping : null,
    };

    form.put(route('admin.order.address.update', props.order.id), {
        data: payload,
        onSuccess: ({ data }) => {
            modalRef.value.close();
            emitter.emit('order:address-updated', data.order || null);
            alert.showSuccess(
                data.message || 'Sales Order updated successfully.',
            );
        },
    });
};
</script>
