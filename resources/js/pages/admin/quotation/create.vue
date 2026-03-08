<style lang="scss" scoped>
.search-area {
    position: relative;
    width: 100%;
}

.dropdown-results {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    margin-top: 4px;
    max-height: 260px;
    overflow-y: auto;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 0;
    list-style: none;
    z-index: 1000;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);

    .dropdown-item {
        padding: 0;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.15s ease;
        color: #374151;
        display: flex;
        flex-direction: column;
        border-bottom: 1px solid #f1f5f9;

        a {
            padding: 10px 14px;
        }

        &:last-child {
            border-bottom: none;
        }

        &:hover {
            background: #f8fafc;
            a {
                padding-left: 18px;
            }
        }
    }

    small {
        font-size: 12px;
        color: #6b7280;
    }
}
</style>

<template>
    <div>
        <Head title="Add Quotation" />

        <Modal ref="modalRef" max-width="3xl" :close-explicitly="true">
            <div class="page-header">
                <h4>Add Quotation</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="page-body new-employee-field">
                    <!-- Existing Customer Checkbox -->
                    <div class="mb-4">
                        <label class="inline-flex items-center mb-2">
                            <input
                                type="checkbox"
                                v-model="existingCustomer"
                                class="form-check-input"
                            />
                            <span class="ml-2 text-secondary">
                                Existing Customer?</span
                            >
                        </label>

                        <!-- Search Box -->
                        <div
                            v-if="existingCustomer"
                            class="relative w-full search-area"
                        >
                            <div class="col-lg-12">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i
                                            data-feather="search"
                                            class="feather-search"
                                        ></i>
                                    </span>
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Search Customer ..."
                                        class="form-control"
                                        @input="handleInput"
                                    />
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <ul
                                    v-show="searchResults.length > 0"
                                    class="dropdown-results"
                                >
                                    <li
                                        v-for="customer in searchResults"
                                        :key="customer.id"
                                        class="dropdown-item"
                                        @click="selectedCustomer = customer"
                                    >
                                        <a href="#">
                                            <p class="mb-0">
                                                <b>{{ customer.last_name }}</b
                                                >, {{ customer.first_name }}
                                            </p>
                                            <small v-if="customer.company_name">
                                                {{ customer.company_name }}
                                            </small>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info Fields -->
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
                            <vue-select
                                v-if="isBillingPH"
                                v-model="form.data.address.province"
                                :options="provinces"
                                :get-option-label="(p) => p.province_name"
                                :get-option-value="(p) => p.province_name"
                                label="province_name"
                                placeholder="Select a province"
                            />
                            <input-text
                                v-else
                                v-model="form.data.address.province"
                                placeholder="e.g. N/A"
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
                                v-if="isBillingPH"
                                v-model="form.data.address.city"
                                :options="cities.billing"
                                :get-option-label="(city) => city.city_name"
                                :get-option-value="(city) => city.city_name"
                                label="city_name"
                                placeholder="Select a city"
                            />
                            <vue-select
                                v-else-if="cities.billing.length > 0"
                                v-model="form.data.address.city"
                                :options="cities.billing"
                                :get-option-label="(city) => city.label"
                                :get-option-value="(city) => city.value"
                                label="label"
                                placeholder="Select a city"
                            />
                            <input-text
                                v-else
                                v-model="form.data.address.city"
                                placeholder="Enter city"
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required"
                                >Postal / Zip Code</label
                            >
                            <input-text
                                v-model="form.data.address.postcode"
                                :class="{
                                    'is-invalid':
                                        form.errors['address.postcode'],
                                }"
                            />
                            <input-error
                                :message="form.errors['address.postcode']"
                            />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required"
                                >When do you need it?</label
                            >
                            <VueDatePicker
                                v-model="form.data.address.need"
                                placeholder="Select Date"
                                :time-config="{
                                    enableTimePicker: false,
                                }"
                                :ui="{
                                    input: form.errors['address.need']
                                        ? 'border-danger'
                                        : '',
                                }"
                                @update:model-value="
                                    form.clearErrors('address.need')
                                "
                                auto-apply
                            />
                            <input-error
                                :message="form.errors['address.need']"
                            />
                        </div>
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
                                <label
                                    class="form-label"
                                    :class="{ required: differentShipping }"
                                    >First Name</label
                                >
                                <input-text
                                    v-model="form.data.shipping.first_name"
                                    :class="{
                                        'is-invalid':
                                            form.errors['shipping.first_name'],
                                    }"
                                    :required="differentShipping"
                                />
                                <input-error
                                    :message="
                                        form.errors['shipping.first_name']
                                    "
                                />
                            </div>

                            <div class="col-md-5 mb-3">
                                <label
                                    class="form-label"
                                    :class="{ required: differentShipping }"
                                    >Last Name</label
                                >
                                <input-text
                                    v-model="form.data.shipping.last_name"
                                    :class="{
                                        'is-invalid':
                                            form.errors['shipping.last_name'],
                                    }"
                                    :required="differentShipping"
                                />
                                <input-error
                                    :message="form.errors['shipping.last_name']"
                                />
                            </div>
                        </div>

                        <!-- Shipping Street Address -->
                        <div class="mb-3">
                            <label
                                class="form-label"
                                :class="{ required: differentShipping }"
                                >Street Address</label
                            >
                            <input-text
                                v-model="form.data.shipping.line1"
                                :class="{
                                    'is-invalid': form.errors['shipping.line1'],
                                }"
                                :required="differentShipping"
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
                                <label
                                    class="form-label"
                                    :class="{ required: differentShipping }"
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
                                <label
                                    class="form-label"
                                    :class="{ required: differentShipping }"
                                    >Province</label
                                >
                                <vue-select
                                    v-if="isShippingPH"
                                    v-model="form.data.shipping.province"
                                    :options="provinces"
                                    :get-option-label="(p) => p.province_name"
                                    :get-option-value="(p) => p.province_name"
                                    label="province_name"
                                    placeholder="Select a province"
                                />
                                <input-text
                                    v-else
                                    v-model="form.data.shipping.province"
                                    placeholder="e.g. N/A"
                                />
                                <input-error
                                    :message="form.errors['shipping.province']"
                                />
                            </div>
                        </div>

                        <!-- Shipping City / Barangay -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label
                                    class="form-label"
                                    :class="{ required: differentShipping }"
                                    >City</label
                                >
                                <vue-select
                                    v-if="isShippingPH"
                                    v-model="form.data.shipping.city"
                                    :options="cities.shipping"
                                    :get-option-label="(city) => city.city_name"
                                    :get-option-value="(city) => city.city_name"
                                    label="city_name"
                                    placeholder="Select a city"
                                />
                                <vue-select
                                    v-else-if="cities.shipping.length > 0"
                                    v-model="form.data.shipping.city"
                                    :options="cities.shipping"
                                    :get-option-label="(city) => city.label"
                                    :get-option-value="(city) => city.value"
                                    label="label"
                                    placeholder="Select a city"
                                />
                                <input-text
                                    v-else
                                    v-model="form.data.shipping.city"
                                    placeholder="Enter city"
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
                            <label
                                class="form-label"
                                :class="{ required: differentShipping }"
                                >Postal / Zip Code</label
                            >
                            <input-text
                                v-model="form.data.shipping.postcode"
                                :class="{
                                    'is-invalid':
                                        form.errors['shipping.postcode'],
                                }"
                                :required="differentShipping"
                            />
                            <input-error
                                :message="form.errors['shipping.postcode']"
                            />
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-2">
                        <div
                            class="status-toggle modal-status d-flex justify-content-between align-items-center"
                        >
                            <span class="status-label">Status</span>
                            <input
                                v-model="form.data.enabled"
                                type="checkbox"
                                id="enabled"
                                class="check"
                            />
                            <label for="enabled" class="checktoggle"></label>
                        </div>
                        <input-error :message="form.errors.enabled" />
                    </div>
                </div>

                <!-- Buttons -->
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
                            @click="modalRef.close()"
                        >
                            Cancel
                        </button>

                        <submit-btn :loading="form.processing">
                            Save Quotation
                        </submit-btn>
                    </div>
                </div>
            </form>
        </Modal>
    </div>
</template>

<script setup>
import { computed, ref, useTemplateRef, watch, nextTick } from 'vue';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { debounce } from 'lodash';
import { VueDatePicker } from '@vuepic/vue-datepicker';

const props = defineProps({
    countries: Object,
});

const modalRef = useTemplateRef('modalRef');

const createAnother = ref(false);
const existingCustomer = ref(false);
const differentShipping = ref(false);
const selectedCustomer = ref(null);

const searchQuery = ref('');
const searchResults = ref([]);
const PHILIPPINES_ID = 175;
const cities = ref({
    billing: [],
    shipping: [],
});
const provinces = ref([]);

const isBillingPH = computed(
    () => form.data.address.country_id === PHILIPPINES_ID,
);
const isShippingPH = computed(
    () => form.data.shipping.country_id === PHILIPPINES_ID,
);

axios.get(route('admin.address.provinces')).then(({ data }) => {
    provinces.value = data;
});

const form = useAxiosForm({
    quotable_id: '',
    quotable_type: '',
    enabled: true,
    address: {
        title: '',
        first_name: '',
        last_name: '',
        company_name: '',
        email: '',
        phone: props.countries.default?.phonecode || '+63',
        line1: '',
        line2: '',
        province: '',
        city: '',
        barangay: '',
        postcode: '',
        country_id: PHILIPPINES_ID,
        need: '',
        notes: '',
    },
    shipping: {
        title: '',
        first_name: '',
        last_name: '',
        company_name: '',
        line1: '',
        line2: '',
        province: '',
        city: '',
        barangay: '',
        postcode: '',
        country_id: PHILIPPINES_ID,
    },
});

watch(existingCustomer, (newVal) => {
    if (!newVal) {
        selectedCustomer.value = null;
        searchQuery.value = '';
        searchResults.value = [];
        resetCustomerForm();
    }
});

watch(selectedCustomer, (customer) => {
    if (customer) {
        searchQuery.value = `${customer.last_name}, ${customer.first_name}`;
        searchResults.value = [];

        // Set customer ID
        form.data.quotable_id = customer.id || null;
        form.data.quotable_type = 'App\\Models\\Customer';

        // Populate addresses
        populateBillingAddress(customer);
        populateShippingAddress(customer);
    }
});

watch(differentShipping, (newVal) => {
    if (newVal) {
        // Clear any previous shipping errors
        form.clearErrors([
            'shipping.first_name',
            'shipping.last_name',
            'shipping.line1',
            'shipping.city',
            'shipping.province',
            'shipping.postcode',
            'shipping.country_id',
        ]);

        if (selectedCustomer.value?.shipping) {
            populateShippingAddress(selectedCustomer.value);
        } else {
            form.data.shipping = {
                title: form.data.address.title,
                first_name: form.data.address.first_name,
                last_name: form.data.address.last_name,
                company_name: form.data.address.company_name,
                line1: form.data.address.line1,
                line2: form.data.address.line2,
                province: form.data.address.province,
                city: form.data.address.city,
                barangay: form.data.address.barangay,
                postcode: form.data.address.postcode,
                country_id: form.data.address.country_id,
            };
        }
    } else {
        // Clear shipping errors and reset shipping data
        form.clearErrors([
            'shipping.first_name',
            'shipping.last_name',
            'shipping.line1',
            'shipping.city',
            'shipping.province',
            'shipping.postcode',
            'shipping.country_id',
        ]);
        form.data.shipping = {
            title: '',
            first_name: '',
            last_name: '',
            company_name: '',
            line1: '',
            line2: '',
            province: '',
            city: '',
            barangay: '',
            postcode: '',
            country_id: PHILIPPINES_ID,
        };
    }
});

watch(
    () => form.data.address.country_id,
    (value) => {
        form.data.address.province = '';
        form.data.address.city = '';
        cities.value.billing = [];
        if (value && value !== PHILIPPINES_ID) {
            axios
                .post(route('admin.address.states'), { country_id: value })
                .then(({ data }) => {
                    cities.value.billing = data.map((s) => ({
                        label: s.name,
                        value: s.name,
                    }));
                });
        }
    },
);

watch(
    () => form.data.shipping.country_id,
    (value) => {
        form.data.shipping.province = '';
        form.data.shipping.city = '';
        cities.value.shipping = [];
        if (value && value !== PHILIPPINES_ID) {
            axios
                .post(route('admin.address.states'), { country_id: value })
                .then(({ data }) => {
                    cities.value.shipping = data.map((s) => ({
                        label: s.name,
                        value: s.name,
                    }));
                });
        }
    },
);

watch(
    () => form.data.address.province,
    (value) => {
        form.data.address.city = '';
        const province = provinces.value.find((p) => p.province_name === value);
        if (!province) {
            cities.value.billing = [];
            return;
        }
        axios
            .get(route('admin.address.cities'), {
                params: { province_id: province.id },
            })
            .then(({ data }) => {
                cities.value.billing = data;
            });
    },
);

watch(
    () => form.data.shipping.province,
    (value) => {
        form.data.shipping.city = '';
        const province = provinces.value.find((p) => p.province_name === value);
        if (!province) {
            cities.value.shipping = [];
            return;
        }
        axios
            .get(route('admin.address.cities'), {
                params: { province_id: province.id },
            })
            .then(({ data }) => {
                cities.value.shipping = data;
            });
    },
);

// reset form
const resetCustomerForm = () => {
    form.reset();
    differentShipping.value = false;
};

// Handle input with debounce
const handleInput = debounce(() => {
    const query = searchQuery.value.trim();

    if (query.length < 2) {
        searchResults.value = [];
        return;
    }

    axios
        .get(route('admin.customer.search', { search: query }))
        .then(({ data }) => {
            searchResults.value = data || [];
        })
        .catch((error) => {
            console.error('Error searching customers:', error);
            searchResults.value = [];
        });
}, 300);

const populateBillingAddress = (customer) => {
    const billing = customer?.billing || {};
    form.data.address = {
        title: customer.title || '',
        first_name: customer.first_name || '',
        last_name: customer.last_name || '',
        company_name: billing.company_name || '',
        email: billing.email || '',
        phone: billing.phone || '',
        line1: billing.line1 || '',
        line2: billing.line2 || '',
        province: billing.province || '',
        city: billing.city || '',
        barangay: billing.barangay || '',
        postcode: billing.postcode || '',
        country_id: billing.country_id || '',
    };
};

const populateShippingAddress = (customer) => {
    const shipping = customer?.shipping;

    if (shipping) {
        differentShipping.value = true;
        form.data.shipping = {
            title: shipping.title || '',
            first_name: shipping.first_name || '',
            last_name: shipping.last_name || '',
            company_name: shipping.company_name || '',
            line1: shipping.line1 || '',
            line2: shipping.line2 || '',
            province: shipping.province || '',
            city: shipping.city || '',
            barangay: shipping.barangay || '',
            postcode: shipping.postcode || '',
            country_id: shipping.country_id || '',
        };
    } else {
        differentShipping.value = false;
    }
};

// Submit form
const submitForm = () => {
    // Temporarily modify form data based on differentShipping state
    const originalShipping = form.data.shipping;

    if (!differentShipping.value) {
        // Remove shipping data completely when checkbox is unchecked
        delete form.data.shipping;
    }

    form.post(route('admin.quotation.store'), {
        onSuccess: (response) => {
            const data = response.data;
            emitter.emit('quotation:created', data.quotation);
            alert.showSuccess(data.message || 'Quotation saved successfully.');

            const url = route('admin.quotation.item', {
                quotation: data.quotation.id,
            });

            modalRef.value.close();

            nextTick(() => {
                router.visit(url);
            });
        },
        onError: () => {
            // Restore shipping data if there was an error
            if (!differentShipping.value) {
                form.data.shipping = originalShipping;
            }
        },
        onFinish: () => {
            // Always restore shipping data
            if (!differentShipping.value) {
                form.data.shipping = originalShipping;
            }
        },
    });
};
</script>
