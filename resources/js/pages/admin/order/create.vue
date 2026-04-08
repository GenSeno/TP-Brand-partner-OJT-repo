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
        <Head title="Add Sales Order" />

        <Modal ref="modalRef" max-width="3xl" :close-explicitly="true">
            <div class="page-header">
                <h4>Add Sales Order</h4>
            </div>

            <form @submit.prevent="submitForm">
                <div class="page-body new-employee-field">
                    <!-- Existing Customer Checkbox + Search -->
                    <div class="mb-4">
                        <label class="inline-flex items-center mb-2">
                            <input
                                type="checkbox"
                                v-model="existingCustomer"
                                @change="resetCustomerForm"
                                class="form-check-input"
                            />
                            <span class="ml-2 text-secondary">
                                Existing Customer?</span
                            >
                        </label>

                        <div
                            v-if="existingCustomer"
                            class="relative w-full search-area"
                        >
                            <div class="col-lg-12">
                                <div class="input-group">
                                    <span
                                        class="input-group-text"
                                        id="search-customer"
                                    >
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
                                    v-if="searchResults.length > 0"
                                    class="dropdown-results"
                                >
                                    <li
                                        v-for="customer in searchResults"
                                        :key="customer.id"
                                        class="dropdown-item"
                                        @click="selectCustomer(customer)"
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

                    <!-- Billing Fields -->
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
                            />
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
                            <input-error
                                :message="form.errors['address.email']"
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Phone</label>
                            <input-text v-model="form.data.address.phone" />
                            <input-error
                                :message="form.errors['address.phone']"
                            />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required"
                            >Street Address</label
                        >
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
                            <input-text v-model="form.data.address.barangay" />
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
                            <input-text v-model="form.data.address.postcode" />
                            <input-error
                                :message="form.errors['address.postcode']"
                            />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"
                                >Expected delivery date</label
                            >
                            <VueDatePicker
                                v-model="form.data.need"
                                placeholder="Select Date"
                                :time-config="{
                                    enableTimePicker: false,
                                }"
                                :ui="{
                                    input: form.errors['need']
                                        ? 'border-danger'
                                        : '',
                                }"
                                @update:model-value="form.clearErrors('need')"
                                auto-apply
                            />
                            <input-error :message="form.errors.need" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea
                            v-model="form.data.address.notes"
                            class="form-control"
                            rows="4"
                            placeholder="Enter Additional details for order"
                        ></textarea>
                        <input-error :message="form.errors['address.notes']" />
                    </div>

                    <!-- Shipping Option -->
                    <div class="mb-3 mt-3">
                        <label class="form-label required"
                            >Shipping Option</label
                        >
                        <div class="d-flex gap-4">
                            <label class="form-check mb-0">
                                <input
                                    type="radio"
                                    class="form-check-input"
                                    v-model="shippingOption"
                                    value="delivery"
                                />
                                <span class="form-check-label">Delivery</span>
                            </label>
                            <label class="form-check mb-0">
                                <input
                                    type="radio"
                                    class="form-check-input"
                                    v-model="shippingOption"
                                    value="pickup"
                                />
                                <span class="form-check-label">Pickup</span>
                            </label>
                        </div>
                    </div>

                    <div v-if="shippingOption === 'delivery'" class="mb-3 mt-4">
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
                    <div
                        v-if="
                            shippingOption === 'delivery' && differentShipping
                        "
                        class="shipping-fields"
                    >
                        <h6 class="mb-3">Shipping Address</h6>

                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Title</label>
                                <select
                                    v-model="form.data.shipping.title"
                                    class="form-select"
                                >
                                    <option value="">Select</option>
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
                                />
                                <input-error
                                    :message="form.errors['shipping.last_name']"
                                />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required"
                                >Street Address</label
                            >
                            <input-text v-model="form.data.shipping.line1" />
                            <input-error
                                :message="form.errors['shipping.line1']"
                            />
                        </div>

                        <div class="mb-3">
                            <input-text v-model="form.data.shipping.line2" />
                            <input-error
                                :message="form.errors['shipping.line2']"
                            />
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
                                    :message="
                                        form.errors['shipping.country_id']
                                    "
                                />
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required"
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

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">City</label>
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
                                />
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
                        <submit-btn :loading="form.processing"
                            >Save Sales Order</submit-btn
                        >
                    </div>
                </div>
            </form>
        </Modal>
    </div>
</template>

<script setup>
import { computed, nextTick, ref, useTemplateRef, watch } from 'vue';
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { Head, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import axios from 'axios';
import { VueDatePicker } from '@vuepic/vue-datepicker';

const props = defineProps({
    customers: { type: Array, default: () => [] },
    countries: Object,
});
const modalRef = useTemplateRef('modalRef');

const createAnother = ref(false);
const existingCustomer = ref(false);
const differentShipping = ref(false);
const shippingOption = ref('delivery');

const PHILIPPINES_ID = 175;
const searchQuery = ref('');
const searchResults = ref([]);
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
    orderable_id: '',
    address: {
        title: '',
        first_name: '',
        last_name: '',
        company_name: '',
        email: '',
        phone: '+63',
        line1: '',
        line2: '',
        province: '',
        city: '',
        barangay: '',
        postcode: '',
        country_id: PHILIPPINES_ID,
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
    need: '',
});

const toggleDifferentShipping = () => {
    form.data.shipping.title = form.data.address.title;
    form.data.shipping.first_name = form.data.address.first_name;
    form.data.shipping.last_name = form.data.address.last_name;
};

const syncShippingDefaults = () => {
    if (differentShipping.value) {
        toggleDifferentShipping();
    } else {
        form.data.shipping = {
            title: '',
            first_name: '',
            last_name: '',
        };
    }
};

watch(
    () => ({
        title: form.data.address.title,
        first_name: form.data.address.first_name,
        last_name: form.data.address.last_name,
    }),
    (newVal) => {
        if (differentShipping.value) Object.assign(form.data.shipping, newVal);
    },
);

const resetCustomerForm = () => {
    form.data.address = {
        title: '',
        first_name: '',
        last_name: '',
        company_name: '',
        email: '',
        phone: '+63',
        line1: '',
        line2: '',
        province: '',
        city: '',
        barangay: '',
        postcode: '',
        country_id: PHILIPPINES_ID,
        notes: '',
    };

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

    differentShipping.value = false;
    shippingOption.value = 'delivery';
    form.data.orderable_id = '';
};

watch(existingCustomer, (newVal) => {
    if (!newVal) resetCustomerForm();
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
            if (isBillingPH.value) cities.value.billing = [];
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
            if (isShippingPH.value) cities.value.shipping = [];
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

const handleInput = debounce(async () => {
    const query = searchQuery.value.trim();

    if (query.length < 2) {
        searchResults.value = [];
        return;
    }

    try {
        const { data } = await axios.get(
            route('admin.customer.search', { search: query }),
        );
        searchResults.value = data || [];
    } catch (error) {
        console.error('Error searching customers:', error);
        searchResults.value = [];
    }
}, 300);

const selectCustomer = (customer) => {
    searchQuery.value = `${customer.last_name}, ${customer.first_name}`;
    searchResults.value = [];

    // Set customer ID
    form.data.orderable_id = customer.id || null;

    // Populate addresses
    populateBillingAddress(customer);
    populateShippingAddress(customer);
};

const populateBillingAddress = (customer) => {
    const billing = customer?.billing || {};
    form.data.address = {
        title: customer.title || '',
        first_name: customer.first_name || '',
        last_name: customer.last_name || '',
        company_name: billing.company_name || '',
        email: billing.email || '',
        phone: billing.phone || '+63',
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

const submitForm = () => {
    const payload = {
        customer_id: form.data.orderable_id || null,
        address: form.data.address,
        shipping:
            shippingOption.value === 'delivery' && differentShipping.value
                ? form.data.shipping
                : null,
        need: form.data.need,
        shipping_option: shippingOption.value,
    };

    form.post(route('admin.order.store'), {
        data: payload,
        onSuccess: (response) => {
            const data = response.data;
            emitter.emit('order:created', data.order || null);
            alert.showSuccess(
                data.message || 'Sales Order saved successfully.',
            );

            if (!createAnother.value) {
                const url = route('admin.order.show', data.order.id);
                modalRef.value.close();
                nextTick(() => {
                    router.visit(url);
                });
            } else {
                resetCustomerForm();
            }
        },
    });
};
</script>
