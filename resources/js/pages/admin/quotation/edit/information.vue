<template>
    <div>
        <Head title="Edit Quotation" />

        <Modal ref="modalRef" max-width="3xl" :close-explicitly="true">
            <div class="page-header">
                <h4>Edit Information</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="page-body new-employee-field">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Title</label>
                            <select
                                v-model="form.data.address.title"
                                class="form-select"
                                :class="{
                                    'is-invalid': form.errors['address.title'],
                                }"
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
                                :message="form.errors['address.title']"
                            />
                        </div>

                        <div class="col-md-5 mb-3">
                            <label class="form-label required"
                                >First Name</label
                            >
                            <input-text
                                v-model="form.data.address.first_name"
                                :class="{
                                    'is-invalid':
                                        form.errors['address.first_name'],
                                }"
                            />
                            <input-error
                                :message="form.errors['address.first_name']"
                            />
                        </div>

                        <div class="col-md-5 mb-3">
                            <label class="form-label required">Last Name</label>
                            <input-text
                                v-model="form.data.address.last_name"
                                :class="{
                                    'is-invalid':
                                        form.errors['address.last_name'],
                                }"
                            />
                            <input-error
                                :message="form.errors['address.last_name']"
                            />
                        </div>
                    </div>

                    <!-- Company -->
                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input-text
                            v-model="form.data.address.company_name"
                            :class="{
                                'is-invalid':
                                    form.errors['address.company_name'],
                            }"
                        />
                        <input-error
                            :message="form.errors['address.company_name']"
                        />
                    </div>

                    <!-- Email & Phone -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Email</label>
                            <input-text
                                v-model="form.data.address.email"
                                :class="{
                                    'is-invalid': form.errors['address.email'],
                                }"
                            />
                            <input-error
                                :message="form.errors['address.email']"
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Phone</label>
                            <input-text
                                v-model="form.data.address.phone"
                                :class="{
                                    'is-invalid': form.errors['address.phone'],
                                }"
                            />
                            <input-error
                                :message="form.errors['address.phone']"
                            />
                        </div>
                    </div>

                    <!-- Address Fields -->
                    <div class="mb-3">
                        <label class="form-label required"
                            >Street Address</label
                        >
                        <input-text
                            v-model="form.data.address.line1"
                            placeholder="House number and street name"
                            :class="{
                                'is-invalid': form.errors['address.line1'],
                            }"
                        />
                        <input-error :message="form.errors['address.line1']" />
                    </div>

                    <div class="mb-3">
                        <input-text
                            v-model="form.data.address.line2"
                            placeholder="Apartment, suite, unit, etc."
                            :class="{
                                'is-invalid': form.errors['address.line2'],
                            }"
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
                                :options="props.countries.data"
                                :get-option-label="
                                    (country) =>
                                        `${country.emoji} ${country.name}`
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
                            <input-text
                                v-model="form.data.address.province"
                                :class="{
                                    'is-invalid':
                                        form.errors['address.province'],
                                }"
                            />
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
                            <input-error
                                :message="form.errors['address.city']"
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barangay</label>
                            <input-text
                                v-model="form.data.address.barangay"
                                :class="{
                                    'is-invalid':
                                        form.errors['address.barangay'],
                                }"
                            />
                            <input-error
                                :message="form.errors['address.barangay']"
                            />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required"
                            >Postal / Zip Code</label
                        >
                        <input-text
                            v-model="form.data.address.postcode"
                            :class="{
                                'is-invalid': form.errors['address.postcode'],
                            }"
                        />
                        <input-error
                            :message="form.errors['address.postcode']"
                        />
                    </div>
                    <div class="mb-3">
                        <label class="form-label required"
                            >When do you need it?</label
                        >
                        <input-text
                            v-model="form.data.address.need"
                            :class="{
                                'is-invalid': form.errors['address.need'],
                            }"
                        />
                        <input-error :message="form.errors['address.need']" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea
                            v-model="form.data.address.notes"
                            class="form-control"
                            rows="4"
                            placeholder="Enter Additional details for quotation"
                        ></textarea>
                    </div>

                    <div class="mb-3 mt-4">
                        <label class="form-check">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                v-model="differentShipping"
                                @change="syncShippingDefaults"
                            />
                            Ship to a different address ?
                        </label>
                    </div>

                    <!-- Shipping Fields -->
                    <div v-show="differentShipping" class="shipping-fields">
                        <h6 class="mb-3">Shipping Address</h6>

                        <!-- Shipping Title / First / Last -->
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Title</label>
                                <select
                                    v-model="form.data.shipping.title"
                                    class="form-select"
                                    :class="{
                                        'is-invalid':
                                            form.errors['shipping.title'],
                                    }"
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
                                    :class="{
                                        'is-invalid':
                                            form.errors['shipping.first_name'],
                                    }"
                                />
                                <input-error
                                    :message="
                                        form.errors['shipping.first_name']
                                    "
                                />
                            </div>

                            <div class="col-md-5 mb-3">
                                <label class="form-label required"
                                    >Last Name</label
                                >
                                <input-text
                                    v-model="form.data.shipping.last_name"
                                    :class="{
                                        'is-invalid':
                                            form.errors['shipping.last_name'],
                                    }"
                                />
                                <input-error
                                    :message="form.errors['shipping.last_name']"
                                />
                            </div>
                        </div>

                        <!-- Shipping Street Address -->
                        <div class="mb-3">
                            <label class="form-label required"
                                >Street Address</label
                            >
                            <input-text
                                v-model="form.data.shipping.line1"
                                :class="{
                                    'is-invalid': form.errors['shipping.line1'],
                                }"
                            />
                            <input-error
                                :message="form.errors['shipping.line1']"
                            />
                        </div>

                        <div class="mb-3">
                            <input-text
                                v-model="form.data.shipping.line2"
                                :class="{
                                    'is-invalid': form.errors['shipping.line2'],
                                }"
                            />
                            <input-error
                                :message="form.errors['shipping.line2']"
                            />
                        </div>

                        <!-- Shipping Country / Province -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required"
                                    >Country/Region</label
                                >
                                <vue-select
                                    v-model="form.data.shipping.country_id"
                                    :options="props.countries.data"
                                    :get-option-label="
                                        (country) =>
                                            `${country.emoji} ${country.name}`
                                    "
                                    :get-option-value="(country) => country.id"
                                    label="name"
                                    placeholder="Select a country"
                                />
                                <input-error
                                    :message="
                                        form.errors['shipping.country_id']
                                    "
                                />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required"
                                    >Province</label
                                >
                                <input-text
                                    v-model="form.data.shipping.province"
                                    :class="{
                                        'is-invalid':
                                            form.errors['shipping.province'],
                                    }"
                                />
                                <input-error
                                    :message="form.errors['shipping.province']"
                                />
                            </div>
                        </div>

                        <!-- Shipping City / Barangay -->
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
                                <input-text
                                    v-model="form.data.shipping.barangay"
                                    :class="{
                                        'is-invalid':
                                            form.errors['shipping.barangay'],
                                    }"
                                />
                                <input-error
                                    :message="form.errors['shipping.barangay']"
                                />
                            </div>
                        </div>

                        <!-- Shipping Postal / Zip Code -->
                        <div class="mb-3">
                            <label class="form-label required"
                                >Postal / Zip Code</label
                            >
                            <input-text
                                v-model="form.data.shipping.postcode"
                                :class="{
                                    'is-invalid':
                                        form.errors['shipping.postcode'],
                                }"
                            />
                            <input-error
                                :message="form.errors['shipping.postcode']"
                            />
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
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
                            >Update Information</submit-btn
                        >
                    </div>
                </div>
            </form>
        </Modal>
    </div>
</template>

<script setup>
import { ref, useTemplateRef, watch } from 'vue';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    quotation: Object,
    countries: Object,
});
const modalRef = useTemplateRef('modalRef');
const differentShipping = ref(!!props.quotation?.shipping_address);
const billing = props.quotation?.billing_address || {};
const shipping = props.quotation?.shipping_address || {};
const states = ref({
    billing: [],
    shipping: [],
});

// Populate form with existing customer data
const form = useAxiosForm({
    address: {
        title: billing?.title || '',
        first_name: billing?.first_name || '',
        last_name: billing?.last_name || '',
        company_name: billing?.company_name || '',
        email: billing?.email || '',
        phone: billing?.phone || props.countries.default.phonecode || '+63',
        line1: billing?.line1 || '',
        line2: billing?.line2 || '',
        province: billing?.province || '',
        city: billing?.city || '',
        barangay: billing?.barangay || '',
        postcode: billing?.postcode || '',
        country_id: billing?.country_id || props.countries.default.id || '',
        need: billing?.meta?.need || '',
        notes: billing?.meta?.notes || '',
    },
    shipping: {
        title: shipping?.title || '',
        first_name: shipping?.first_name || '',
        last_name: shipping?.last_name || '',
        line1: shipping?.line1 || '',
        line2: shipping?.line2 || '',
        province: shipping?.province || '',
        city: shipping?.city || '',
        barangay: shipping?.barangay || '',
        postcode: shipping?.postcode || '',
        country_id: shipping?.country_id || props.countries.default.id || '',
    },
});

watch(differentShipping, (newVal) => {
    if (newVal) {
        if (shipping) {
            form.reset('shipping');
        } else {
            form.data.shipping = {
                ...form.data.shipping,
                title: form.data.address.title,
                first_name: form.data.address.first_name,
                last_name: form.data.address.last_name,
                country_id: props.countries.default?.id || '',
            };
        }
    } else {
        form.data.shipping = {
            title: '',
            first_name: '',
            last_name: '',
            line1: '',
            line2: '',
            province: '',
            city: '',
            barangay: '',
            postcode: '',
            country_id: '',
        };
    }
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

// Submit form
const submitForm = () => {
    form.put(route('admin.quotation.update', props.quotation.id), {
        onSuccess: (data) => {
            modalRef.value.close();
            emitter.emit('quotation:updated', data.quotation);
            alert.showSuccess(
                data.message || 'Quotation updated successfully.',
            );
        },
    });
};
</script>
