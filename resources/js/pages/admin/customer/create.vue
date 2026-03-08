<template>
    <div>
        <Head title="Add Customer" />

        <Modal
            ref="modalRef"
            max-width="3xl"
            :close-explicitly="true"
            #default="{ close }"
        >
            <div class="page-header">
                <h4>Add Customer</h4>
            </div>
            <form @submit.prevent="submitForm">
                <div class="page-body new-employee-field">
                    <!-- Profile Picture -->
                    <div class="mb-3">
                        <label class="form-label"
                            >Profile Picture (JPEG, PNG, JPG up to 2MB)</label
                        >
                        <avatar-upload
                            v-model:avatar="form.data.avatar"
                            :error-message="form.errors.avatar"
                            accept=".jpg,.jpeg,.png"
                            @change="handleAvatarSelect"
                        />
                        <div
                            v-if="form.errors.avatar"
                            class="text-danger small mt-1"
                        >
                            {{ form.errors.avatar }}
                        </div>
                    </div>

                    <!-- Title - First - Last -->
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Title</label>
                            <select
                                v-model="form.data.title"
                                class="form-select"
                                :class="{ 'is-invalid': form.errors.title }"
                            >
                                <option value="">Select</option>
                                <option value="Mr.">Mr.</option>
                                <option value="Ms.">Ms.</option>
                                <option value="Mrs.">Mrs.</option>
                                <option value="Dr.">Dr.</option>
                                <option value="Prof.">Prof.</option>
                                <option value="Rev.">Rev.</option>
                            </select>
                            <input-error :message="form.errors.title" />
                        </div>

                        <div class="col-md-5 mb-3">
                            <label class="form-label required"
                                >First Name</label
                            >
                            <input-text
                                v-model="form.data.first_name"
                                :class="{
                                    'is-invalid': form.errors.first_name,
                                }"
                            />
                            <input-error :message="form.errors.first_name" />
                        </div>

                        <div class="col-md-5 mb-3">
                            <label class="form-label required">Last Name</label>
                            <input-text
                                v-model="form.data.last_name"
                                :class="{ 'is-invalid': form.errors.last_name }"
                            />
                            <input-error :message="form.errors.last_name" />
                        </div>
                    </div>

                    <!-- Company -->
                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input-text
                            v-model="form.data.company_name"
                            :class="{ 'is-invalid': form.errors.company_name }"
                        />
                        <input-error :message="form.errors.company_name" />
                    </div>

                    <!-- Email / Phone -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Email</label>
                            <input-text
                                v-model="form.data.email"
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
                                v-model="form.data.phone"
                                :class="{
                                    'is-invalid': form.errors['address.phone'],
                                }"
                            />
                            <input-error
                                :message="form.errors['address.phone']"
                            />
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label class="form-label required"
                            >Street Address</label
                        >
                        <input-text
                            v-model="form.data.line1"
                            :class="{
                                'is-invalid': form.errors['address.line1'],
                            }"
                        />
                        <input-error :message="form.errors['address.line1']" />
                    </div>

                    <div class="mb-3">
                        <input-text
                            v-model="form.data.line2"
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
                                v-model="form.data.country_id"
                                :options="props.countries.data"
                                :get-option-label="
                                    (country) =>
                                        `${country.emoji} ${country.name}`
                                "
                                :get-option-value="(country) => country.id"
                                label="country"
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
                                v-model="form.data.province"
                                :options="provinces"
                                :get-option-label="(p) => p.province_name"
                                :get-option-value="(p) => p.province_name"
                                label="province_name"
                                placeholder="Select a province"
                            />
                            <input-text
                                v-else
                                v-model="form.data.province"
                                :class="{
                                    'is-invalid':
                                        form.errors['address.province'],
                                }"
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
                                v-model="form.data.city"
                                :options="cities.billing"
                                :get-option-label="(city) => city.city_name"
                                :get-option-value="(city) => city.city_name"
                                label="city_name"
                                placeholder="Select a city"
                            />
                            <vue-select
                                v-else-if="cities.billing.length > 0"
                                v-model="form.data.city"
                                :options="cities.billing"
                                :get-option-label="(city) => city.label"
                                :get-option-value="(city) => city.value"
                                label="label"
                                placeholder="Select a city"
                            />
                            <input-text
                                v-else
                                v-model="form.data.city"
                                :class="{
                                    'is-invalid': form.errors['address.city'],
                                }"
                                placeholder="Enter city"
                            />
                            <input-error
                                :message="form.errors['address.city']"
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barangay</label>
                            <input-text
                                v-model="form.data.barangay"
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
                            v-model="form.data.postcode"
                            :class="{
                                'is-invalid': form.errors['address.postcode'],
                            }"
                        />
                        <input-error
                            :message="form.errors['address.postcode']"
                        />
                    </div>

                    <!-- Different Shipping Toggle -->
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
                    <div v-if="differentShipping" class="shipping-fields">
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
                                    <option value="">Select</option>
                                    <option value="Mr.">Mr.</option>
                                    <option value="Ms.">Ms.</option>
                                    <option value="Mrs.">Mrs.</option>
                                    <option value="Dr.">Dr.</option>
                                    <option value="Prof.">Prof.</option>
                                    <option value="Rev.">Rev.</option>
                                </select>
                                <input-error
                                    :message="
                                        form.errors['shipping.title']?.[0]
                                    "
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
                                        form.errors['shipping.first_name']?.[0]
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
                                    :message="
                                        form.errors['shipping.last_name']?.[0]
                                    "
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
                                :message="form.errors['shipping.line1']?.[0]"
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
                                :message="form.errors['shipping.line2']?.[0]"
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
                                    label="country"
                                    placeholder="Select a country"
                                />
                                <input-error
                                    :message="
                                        form.errors['shipping.country_id']?.[0]
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
                                    :class="{
                                        'is-invalid':
                                            form.errors['shipping.province'],
                                    }"
                                    placeholder="e.g. N/A"
                                />
                                <input-error
                                    :message="
                                        form.errors['shipping.province']?.[0]
                                    "
                                />
                            </div>
                        </div>

                        <!-- Shipping City / Barangay -->
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
                                    :class="{
                                        'is-invalid':
                                            form.errors['shipping.city'],
                                    }"
                                    placeholder="Enter city"
                                />
                                <input-error
                                    :message="form.errors['shipping.city']?.[0]"
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
                                    :message="
                                        form.errors['shipping.barangay']?.[0]
                                    "
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
                                :message="form.errors['shipping.postcode']?.[0]"
                            />
                        </div>
                    </div>
                    <!-- Status -->
                    <div class="mb-0">
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
                            @click="close()"
                        >
                            Cancel
                        </button>

                        <submit-btn :loading="form.processing">
                            Create Customer
                        </submit-btn>
                    </div>
                </div>
            </form>
        </Modal>
    </div>
</template>
<script setup>
import { useAxiosForm } from '@/composables/axiosForm';
import { emitter } from '@/composables/eventBus';
import * as alert from '@/helpers/alert';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref, useTemplateRef, watch } from 'vue';

const props = defineProps({
    countries: Object,
});

const modalRef = useTemplateRef('modalRef');
const createAnother = ref(false);
const avatarFile = ref(null);
const differentShipping = ref(false);
const PHILIPPINES_ID = 175;
const provinces = ref([]);
const cities = ref({
    billing: [],
    shipping: [],
});

const isBillingPH = computed(() => form.data.country_id === PHILIPPINES_ID);
const isShippingPH = computed(
    () => form.data.shipping.country_id === PHILIPPINES_ID,
);

axios.get(route('admin.address.provinces')).then(({ data }) => {
    provinces.value = data;
});

const form = useAxiosForm({
    title: '',
    first_name: '',
    last_name: '',
    company_name: '',
    enabled: true,

    email: '',
    phone: props.countries.default?.phonecode || '+63',
    line1: '',
    line2: '',
    province: '',
    city: '',
    barangay: '',
    postcode: '',
    country_id: props.countries.default?.id || '',

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
        country_id: props.countries.default?.id || '',
    },
});

const syncShippingDefaults = () => {
    if (!differentShipping.value) return;

    form.data.shipping.title = form.data.title;
    form.data.shipping.first_name = form.data.first_name;
    form.data.shipping.last_name = form.data.last_name;
};

// Live sync
watch(
    () => ({
        title: form.data.title,
        first_name: form.data.first_name,
        last_name: form.data.last_name,
    }),
    (newVal) => {
        if (differentShipping.value) {
            Object.assign(form.data.shipping, newVal);
        }
    },
);

watch(
    () => form.data.country_id,
    (value) => {
        form.data.province = '';
        form.data.city = '';
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
    () => form.data.province,
    (value) => {
        form.data.city = '';
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

const handleAvatarSelect = (event) => {
    const file = event.target?.files?.[0];
    avatarFile.value = file;

    if (form.clearErrors) form.clearErrors('avatar');

    if (!file) return;

    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        if (form.setError) {
            form.setError(
                'avatar',
                'Please select a valid image file (JPEG, PNG, JPG only)',
            );
        }
        return;
    }

    const maxSize = 2 * 1024 * 1024;
    if (file.size > maxSize) {
        if (form.setError) {
            form.setError('avatar', 'Image size must be less than 2MB');
        }
        return;
    }
};

const submitForm = () => {
    const formData = new FormData();

    if (avatarFile.value) {
        formData.append('avatar', avatarFile.value);
    }

    // Billing values
    formData.append('title', form.data.title);
    formData.append('first_name', form.data.first_name);
    formData.append('last_name', form.data.last_name);
    formData.append('company_name', form.data.company_name);
    formData.append('enabled', form.data.enabled ? '1' : '0');

    formData.append('address[email]', form.data.email);
    formData.append('address[phone]', form.data.phone);
    formData.append('address[line1]', form.data.line1);
    formData.append('address[line2]', form.data.line2);
    formData.append('address[city]', form.data.city);
    formData.append('address[province]', form.data.province);
    formData.append('address[barangay]', form.data.barangay);
    formData.append('address[postcode]', form.data.postcode);
    formData.append('address[country_id]', form.data.country_id);
    formData.append('address[title]', form.data.title);
    formData.append('address[first_name]', form.data.first_name);
    formData.append('address[last_name]', form.data.last_name);
    formData.append('address[company_name]', form.data.company_name);

    // Shipping
    if (differentShipping.value) {
        formData.append('shipping[title]', form.data.shipping.title);
        formData.append('shipping[first_name]', form.data.shipping.first_name);
        formData.append('shipping[last_name]', form.data.shipping.last_name);

        formData.append('shipping[line1]', form.data.shipping.line1);
        formData.append('shipping[line2]', form.data.shipping.line2);
        formData.append('shipping[city]', form.data.shipping.city);
        formData.append('shipping[province]', form.data.shipping.province);
        formData.append('shipping[barangay]', form.data.shipping.barangay);
        formData.append('shipping[postcode]', form.data.shipping.postcode);
        formData.append('shipping[country_id]', form.data.shipping.country_id);
    }

    form.submit('post', route('admin.customer.store'), {
        data: formData,
        headers: { 'Content-Type': 'multipart/form-data' },

        onSuccess: (data) => {
            if (createAnother.value) {
                form.reset();
                avatarFile.value = null;
                differentShipping.value = false;
            } else {
                modalRef.value.close();
            }

            emitter.emit('customer:created', data.customer || null);
            alert.showSuccess(data.message || 'Customer created successfully.');
        },
        onError: (errors) => {
            const firstError = Object.keys(errors)[0];
            const element = document.querySelector(`[name="${firstError}"]`);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                element.focus();
            }
        },
    });
};
</script>
