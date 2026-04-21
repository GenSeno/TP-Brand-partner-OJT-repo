<template>
    <div>
        <div
            class="d-flex justify-content-between align-items-center mb-4"
        >
            <div>
                <h2 class="account-page-title">My Addresses</h2>
                <p class="account-page-subtitle">
                    Manage and protect your account
                </p>
            </div>
        </div>

        <div v-if="isAddressFormVisible" class="address-form-box">
            <h4 style="margin-bottom: 20px">
                {{
                    editingAddressId
                        ? 'Edit Address'
                        : 'Add New Address'
                }}
            </h4>
            <form @submit.prevent="submitAddress">
                <div class="form-row-grid">
                    <div class="form-group full-width">
                        <label class="required">Full Name</label>
                        <input
                            v-model="addressForm.name"
                            type="text"
                            class="grocery-input"
                            required
                        />
                    </div>
                    <div class="form-group full-width">
                        <label class="required">Street Address</label>
                        <input
                            v-model="addressForm.line1"
                            type="text"
                            class="grocery-input"
                            placeholder="House number and street name"
                            required
                        />
                    </div>
                    <div class="form-group full-width">
                        <label>Apartment, suite, unit, etc.</label>
                        <input
                            v-model="addressForm.line2"
                            type="text"
                            class="grocery-input"
                        />
                    </div>
                    <div class="form-group">
                        <label class="required">Country/Region</label>
                        <select
                            v-model="addressForm.country_id"
                            class="grocery-input"
                            required
                        >
                            <option
                                v-for="c in countries"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Province</label>
                        <select
                            v-if="
                                addressForm.country_id ===
                                defaultCountryId
                            "
                            v-model="addressForm.province"
                            class="grocery-input"
                        >
                            <option value="">Select a province</option>
                            <option
                                v-for="p in provinces"
                                :key="p.id"
                                :value="p.province_name"
                            >
                                {{ p.province_name }}
                            </option>
                        </select>
                        <input
                            v-else
                            v-model="addressForm.province"
                            type="text"
                            class="grocery-input"
                        />
                    </div>
                    <div class="form-group">
                        <label class="required">City</label>
                        <select
                            v-if="
                                addressForm.country_id ===
                                    defaultCountryId &&
                                cities.length > 0
                            "
                            v-model="addressForm.city"
                            class="grocery-input"
                            required
                        >
                            <option value="">Select a city</option>
                            <option
                                v-for="c in cities"
                                :key="c.id"
                                :value="c.city_name"
                            >
                                {{ c.city_name }}
                            </option>
                        </select>
                        <input
                            v-else
                            v-model="addressForm.city"
                            type="text"
                            class="grocery-input"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <label>Barangay</label>
                        <input
                            v-model="addressForm.barangay"
                            type="text"
                            class="grocery-input"
                        />
                    </div>
                    <div class="form-group full-width">
                        <label class="required">Postal / Zip Code</label>
                        <input
                            v-model="addressForm.postcode"
                            type="text"
                            class="grocery-input"
                            required
                        />
                    </div>
                </div>
                <div
                    class="form-actions mt-4 d-flex"
                    style="gap: 12px; justify-content: flex-end"
                >
                    <button
                        type="button"
                        @click="cancelAddressForm"
                        class="btn-cancel"
                        style="
                            padding: 10px 20px;
                            border: 1px solid #ccc;
                            background: white;
                            border-radius: 6px;
                            cursor: pointer;
                        "
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="btn-save"
                        style="
                            padding: 10px 20px;
                            border: none;
                            background: #1b5e38;
                            color: white;
                            border-radius: 6px;
                            cursor: pointer;
                        "
                        :disabled="addressForm.processing"
                    >
                        Save Address
                    </button>
                </div>
            </form>
        </div>

        <div v-else class="address-grid">
            <!-- Existing Addresses -->
            <div
                v-for="addr in user.addresses"
                :key="addr.id"
                class="address-card"
            >
                <div class="address-card-content">
                    <div class="address-card-header">
                        <span class="addr-name"
                            >{{ addr.first_name }}
                            {{ addr.last_name }}</span
                        >
                        <span
                            v-if="addr.default"
                            class="addr-default-badge"
                            >DEFAULT</span
                        >
                    </div>
                    <div class="addr-body">
                        <p>{{ addr.line1 }}</p>
                        <p v-if="addr.line2">{{ addr.line2 }}</p>
                        <p>
                            {{
                                [
                                    addr.barangay,
                                    addr.city,
                                    addr.province,
                                ]
                                    .filter(Boolean)
                                    .join(', ')
                            }}
                        </p>
                        <p>{{ addr.postcode }}</p>
                        <p>
                            {{
                                addr.country?.name || 'Philippines'
                            }}
                        </p>
                    </div>
                    <div class="addr-actions">
                        <button
                            @click="editAddress(addr)"
                            class="btn-link"
                        >
                            Edit
                        </button>
                        <button
                            @click="removeAddress(addr.id)"
                            class="btn-link"
                        >
                            Remove
                        </button>
                        <button
                            v-if="!addr.default"
                            @click="setDefault(addr.id)"
                            class="btn-link"
                        >
                            Set Default
                        </button>
                    </div>
                </div>
            </div>
            <!-- Add New Address Card -->
            <div
                class="address-card add-new-card"
                @click="openAddressForm()"
            >
                <div class="add-new-content">
                    <div class="add-icon">+</div>
                    <div class="add-title">Add New Address</div>
                    <div
                        class="add-subtitle"
                        style="
                            font-size: 12px;
                            color: #888;
                            margin-top: 8px;
                        "
                    >
                        Manage and protect your account
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    countries: {
        type: Array,
        default: () => [],
    },
    defaultCountryId: {
        type: Number,
        required: true,
    },
});

const isAddressFormVisible = ref(false);
const editingAddressId = ref(null);

const provinces = ref([]);
const cities = ref([]);

axios.get(route('store.address.provinces')).then(({ data }) => {
    provinces.value = data;
});

const addressForm = useForm({
    name: '',
    line1: '',
    line2: '',
    country_id: props.defaultCountryId,
    province: '',
    city: '',
    barangay: '',
    postcode: '',
    default: false,
});

watch(
    () => addressForm.country_id,
    (value) => {
        addressForm.province = '';
        addressForm.city = '';
        cities.value = [];
        if (value && value !== props.defaultCountryId) {
            axios
                .post(route('store.address.states'), { country_id: value })
                .then(({ data }) => {
                    cities.value = data.map((s) => ({
                        city_name: s.name,
                    }));
                });
        }
    },
);

watch(
    () => addressForm.province,
    (value) => {
        addressForm.city = '';
        const province = provinces.value.find((p) => p.province_name === value);
        if (!province) {
            cities.value = [];
            return;
        }
        axios
            .get(route('store.address.cities'), {
                params: { province_id: province.id },
            })
            .then(({ data }) => {
                cities.value = data;
            });
    },
);

const openAddressForm = () => {
    editingAddressId.value = null;
    addressForm.reset();
    addressForm.country_id = props.defaultCountryId;
    isAddressFormVisible.value = true;
};

const editAddress = (addr) => {
    editingAddressId.value = addr.id;
    addressForm.name = (addr.first_name + ' ' + addr.last_name).trim();
    addressForm.line1 = addr.line1;
    addressForm.line2 = addr.line2;
    addressForm.country_id = addr.country_id || props.defaultCountryId;
    addressForm.province = addr.province;

    if (addr.province && addressForm.country_id === props.defaultCountryId) {
        const province = provinces.value.find(
            (p) => p.province_name === addr.province,
        );
        if (province) {
            axios
                .get(route('store.address.cities'), {
                    params: { province_id: province.id },
                })
                .then(({ data }) => {
                    cities.value = data;
                    addressForm.city = addr.city;
                });
        } else {
            addressForm.city = addr.city;
        }
    } else {
        addressForm.city = addr.city;
    }

    addressForm.barangay = addr.barangay;
    addressForm.postcode = addr.postcode;
    addressForm.default = addr.default;
    isAddressFormVisible.value = true;
};

const cancelAddressForm = () => {
    isAddressFormVisible.value = false;
    addressForm.reset();
};

const submitAddress = () => {
    if (editingAddressId.value) {
        addressForm.patch(
            route(
                'store.brand-partner.addresses.update',
                editingAddressId.value,
            ),
            {
                preserveScroll: true,
                onSuccess: () => {
                    isAddressFormVisible.value = false;
                },
            },
        );
    } else {
        if (props.user.addresses?.length === 0) {
            addressForm.default = true;
        }
        addressForm.post(route('store.brand-partner.addresses.store'), {
            preserveScroll: true,
            onSuccess: () => {
                isAddressFormVisible.value = false;
            },
        });
    }
};

const removeAddress = (id) => {
    if (confirm('Are you sure you want to remove this address?')) {
        router.delete(route('store.brand-partner.addresses.destroy', id), {
            preserveScroll: true,
        });
    }
};

const setDefault = (id) => {
    const addr = props.user.addresses.find((a) => a.id === id);
    router.patch(
        route('store.brand-partner.addresses.update', id),
        {
            name: (addr.first_name + ' ' + addr.last_name).trim() || 'Tim Lim',
            line1: addr.line1,
            line2: addr.line2,
            country_id: addr.country_id,
            province: addr.province,
            city: addr.city,
            barangay: addr.barangay,
            postcode: addr.postcode,
            default: true,
        },
        { preserveScroll: true },
    );
};
</script>

<style scoped>
/* My Addresses Styles */
.address-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}
.address-card {
    background: #fdfdfd;
    border: 1px solid #f0f0f0;
    border-radius: 12px;
    padding: 24px;
    transition:
        transform 0.2s,
        box-shadow 0.2s;
}
.address-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.address-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}
.addr-name {
    font-size: 16px;
    font-weight: 800;
    color: #1b1b3e;
}
.addr-default-badge {
    font-size: 10px;
    font-weight: 800;
    background: transparent;
    color: #1b1b3e;
    letter-spacing: 0.5px;
}
.addr-body {
    font-size: 14px;
    color: #888;
    line-height: 1.6;
    margin-bottom: 20px;
}
.addr-body p {
    margin: 0 0 4px 0;
}
.addr-actions {
    display: flex;
    gap: 16px;
}
.btn-link {
    background: none;
    border: none;
    padding: 0;
    font-size: 13px;
    font-weight: 600;
    color: #1b5e38;
    cursor: pointer;
}
.btn-link:hover {
    text-decoration: underline;
}
.add-new-card {
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background: #fdfdfd;
    border: 2px dashed #e1e1e1;
    min-height: 220px;
}
.add-new-card:hover {
    border-color: #1b5e38;
    background: #f8fcf9;
}
.add-new-content {
    text-align: center;
}
.add-icon {
    font-size: 32px;
    color: #aaa;
    margin-bottom: 8px;
    font-weight: 300;
}
.add-title {
    font-size: 16px;
    font-weight: 600;
    color: #555;
}

.address-form-box {
    background: #ffffff;
    border-radius: 16px;
    padding: 32px;
    margin-bottom: 32px;
    box-shadow:
        0 2px 8px rgba(0, 0, 0, 0.04),
        0 4px 16px rgba(0, 0, 0, 0.04);
    border: 1px solid #e8ecef;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.address-form-box h4 {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 24px 0;
    padding-bottom: 16px;
    border-bottom: 1px solid #eef2f6;
    display: flex;
    align-items: center;
}

.address-form-box h4::before {
    content: '📍';
    margin-right: 8px;
    font-size: 18px;
}

.form-row-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px 16px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group.full-width {
    grid-column: span 2;
}

.form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #34495e;
    letter-spacing: 0.3px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.form-group label.required::after {
    content: '*';
    color: #e74c3c;
    margin-left: 4px;
    font-size: 14px;
}

.grocery-input {
    padding: 12px 14px;
    font-size: 14px;
    border: 1.5px solid #dfe6e9;
    border-radius: 10px;
    background: #ffffff;
    color: #2c3e50;
    transition: all 0.2s ease;
    font-family: 'Public Sans', sans-serif;
}

.grocery-input:hover {
    border-color: #b2bec3;
}

.grocery-input:focus {
    outline: none;
    border-color: #1b5e38;
    box-shadow: 0 0 0 4px rgba(27, 94, 56, 0.08);
}

.grocery-input::placeholder {
    color: #b2bec3;
    font-size: 13px;
}

.grocery-input:invalid:not(:placeholder-shown) {
    border-color: #e74c3c;
}

.grocery-input:invalid:not(:placeholder-shown):focus {
    box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.08);
}

select.grocery-input {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23636e72' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 16px;
    padding-right: 40px;
}

select.grocery-input:disabled {
    background-color: #f8f9fa;
    cursor: not-allowed;
    opacity: 0.7;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #eef2f6;
}

.btn-cancel {
    padding: 12px 24px;
    border: 1.5px solid #dfe6e9;
    background: white;
    color: #636e72;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Public Sans', sans-serif;
}

.btn-cancel:hover {
    background: #f8f9fa;
    border-color: #b2bec3;
    color: #2c3e50;
}

.btn-save {
    padding: 12px 28px;
    border: none;
    background: linear-gradient(135deg, #1b5e38 0%, #14632f 100%);
    color: white;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Public Sans', sans-serif;
    box-shadow: 0 2px 4px rgba(27, 94, 56, 0.1);
}

.btn-save:hover:not(:disabled) {
    background: linear-gradient(135deg, #14632f 0%, #0f5227 100%);
    box-shadow: 0 4px 12px rgba(27, 94, 56, 0.2);
    transform: translateY(-1px);
}

.btn-save:active:not(:disabled) {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(27, 94, 56, 0.1);
}

.btn-save:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background: #95a5a6;
    box-shadow: none;
}

/* Loading state for button */
.btn-save.processing {
    position: relative;
    color: transparent;
}

.btn-save.processing::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .address-form-box {
        padding: 20px;
    }

    .form-row-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .form-group.full-width {
        grid-column: span 1;
    }

    .form-actions {
        flex-direction: column-reverse;
        gap: 8px;
    }

    .btn-cancel,
    .btn-save {
        width: 100%;
        text-align: center;
    }

    .grocery-input {
        font-size: 16px; /* Prevents zoom on mobile */
        padding: 14px;
    }
}

/* Address card enhancements to match */
.address-card {
    background: #ffffff;
    border: 1.5px solid #e8ecef;
    border-radius: 16px;
    padding: 24px;
    transition: all 0.25s ease;
}

.address-card:hover {
    border-color: #1b5e38;
    box-shadow: 0 8px 24px rgba(27, 94, 56, 0.08);
    transform: translateY(-2px);
}

.add-new-card {
    background: linear-gradient(135deg, #fafbfc 0%, #f5f7f9 100%);
    border: 2px dashed #cbd5e1;
    border-radius: 16px;
    min-height: 240px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.25s ease;
}

.add-new-card:hover {
    border-color: #1b5e38;
    background: linear-gradient(135deg, #f0f9f4 0%, #e8f5ed 100%);
    transform: translateY(-2px);
}

.add-icon {
    font-size: 36px;
    color: #1b5e38;
    margin-bottom: 12px;
    font-weight: 300;
    transition: transform 0.2s ease;
}

.add-new-card:hover .add-icon {
    transform: scale(1.1);
}

.add-title {
    font-size: 16px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 4px;
}

.add-subtitle {
    font-size: 12px;
    color: #64748b;
}

/* Address card actions enhancement */
.addr-actions {
    display: flex;
    gap: 12px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #eef2f6;
}

.btn-link {
    background: none;
    border: none;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    color: #1b5e38;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.15s ease;
}

.btn-link:hover {
    background: #e8f5ed;
    text-decoration: none;
}

.btn-link:active {
    background: #d4edda;
}
</style>
