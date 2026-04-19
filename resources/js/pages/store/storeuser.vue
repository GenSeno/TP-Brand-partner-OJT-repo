<template>
    <Head title="My Account" />

    <div class="account-page">
        <div class="account-container">
            <!-- Breadcrumb -->
            <div class="account-breadcrumb">
                <Link
                    :href="
                        route(
                            'store.brand-partner.index',
                            $page.props.brandPartner?.slug,
                        )
                    "
                >Shop</Link>
                <span> &rsaquo; </span>
                <span>My Profile</span>
            </div>

            <div class="account-wrapper">
                <!-- Sidebar -->
                <aside class="account-sidebar">
                    <h3 class="sidebar-title">My Account</h3>
                    <ul class="sidebar-menu">
                        <li :class="{ active: activeTab === 'profile' }">
                            <a href="#" @click.prevent="activeTab = 'profile'; selectedOrder = null">My Profile</a>
                        </li>
                        <li :class="{ active: activeTab === 'orders' }">
                            <a href="#" @click.prevent="activeTab = 'orders'; selectedOrder = null">My Orders</a>
                        </li>
                        <li :class="{ active: activeTab === 'addresses' }">
                            <a href="#" @click.prevent="activeTab = 'addresses'; selectedOrder = null">My Addresses</a>
                        </li>
                        <li>
                            <Link
                                :href="
                                    route(
                                        'store.brand-partner.cart',
                                        $page.props.brandPartner?.slug,
                                    )
                                "
                            >
                                My Cart
                            </Link>
                        </li>
                        <li><a href="#">My Wishlist</a></li>
                        <li>
                            <Link
                                :href="route('store.brand-partner.logout')"
                                method="post"
                                as="button"
                                class="logout-btn"
                            >
                                Logout
                            </Link>
                        </li>
                    </ul>
                </aside>

                <!-- Main Content -->
                <div class="account-content">
                    
                    <!-- ============================================== -->
                    <!-- PROFILE TAB                                    -->
                    <!-- ============================================== -->
                    <template v-if="activeTab === 'profile'">
                        <h2 class="account-page-title">My Profile</h2>
                        <p class="account-page-subtitle">
                            Manage and protect your account
                        </p>

                        <!-- My Details -->
                        <div class="account-section">
                            <div class="section-header">
                                <h4>My Details</h4>
                            </div>

                            <div class="detail-field">
                                <label>FULL NAME</label>
                                <div class="detail-value">{{ user.name }}</div>
                            </div>

                            <div class="detail-field">
                                <label>EMAIL ADDRESS</label>
                                <div class="detail-value">{{ user.email }}</div>
                            </div>

                            <div class="detail-field">
                                <label>MEMBER SINCE</label>
                                <div class="detail-value">
                                    {{ formatDateLong(user.created_at) }}
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- ============================================== -->
                    <!-- ORDERS TAB                                     -->
                    <!-- ============================================== -->
                    <template v-else-if="activeTab === 'orders'">
                        <div v-if="!selectedOrder">
                            <h2 class="account-page-title">My Orders</h2>
                            <p class="account-page-subtitle">Manage your orders, track shipments, and view order history</p>

                            <!-- Tabs -->
                            <div class="orders-tabs">
                                <button :class="{ active: orderTab === 'all' }" @click="orderTab = 'all'">All</button>
                                <button :class="{ active: orderTab === 'shipping' }" @click="orderTab = 'shipping'">On Shipping</button>
                                <button :class="{ active: orderTab === 'arrived' }" @click="orderTab = 'arrived'">Arrived</button>
                                <button :class="{ active: orderTab === 'cancelled' }" @click="orderTab = 'cancelled'">Cancelled</button>
                            </div>

                            <!-- List -->
                            <div class="orders-list">
                                <div v-for="order in filteredOrders" :key="order.id" class="order-card">
                                    <div class="order-card-header">
                                        <span class="font-bold">Order NO. {{ order.reference }}</span>
                                        <span class="font-bold">{{ formatDateShort(order.created_at) }}</span>
                                    </div>
                                    <div class="order-card-body">
                                        <div v-for="line in order.lines" :key="line.id" class="order-line-item">
                                            <div class="line-img-wrap">
                                                <img :src="line.product?.images?.[0]?.url || '/img/tshirt-placeholder.svg'" alt="">
                                            </div>
                                            <div class="line-details">
                                                <div class="line-title">{{ line.product_name }}</div>
                                                <div class="line-variants">
                                                    <div v-if="line.meta?.size">Size: {{ line.meta.size }}</div>
                                                    <div v-if="line.meta?.color">Color: {{ line.meta.color }}</div>
                                                    <div>Quantity: {{ line.quantity }}</div>
                                                </div>
                                            </div>
                                            <div class="line-price-col">
                                                <div class="price-current text-red">PHP {{ (line.unit_price / 100).toFixed(2) }}</div>
                                                <div v-if="line.product?.compare_price > line.unit_price" class="price-old text-strike">PHP {{ (line.product.compare_price / 100).toFixed(2) }}</div>
                                            </div>
                                            <div class="status-col">
                                                <div class="status-badge" :class="getStatusBadgeClass(order.status)">
                                                    <span class="status-dot"></span>
                                                    {{ getStatusLabel(order.status) }}
                                                </div>
                                                <div class="status-text mt-2 text-muted" style="font-size: 11px;">
                                                    <span v-if="order.status === 'completed'">Delivered on {{ formatDateLong(order.updated_at) }}</span>
                                                    <span v-else-if="order.status === 'pending'">Order is being prepared</span>
                                                    <span v-else-if="order.status === 'cancelled'">Order has been cancelled</span>
                                                    <span v-else>Estimated Delivery: Pending</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="order-card-footer">
                                            <button class="btn-cancel-order" v-if="['pending', 'confirmed'].includes(order.status)" @click="cancelOrder(order)">CANCEL ORDER</button>
                                            <button class="btn-view-details" @click="viewDetails(order)">VIEW DETAILS</button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="filteredOrders.length === 0" class="no-orders py-4 text-center text-muted">
                                    <p>No orders found in this category.</p>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================== -->
                        <!-- ORDER DETAILS VIEW                             -->
                        <!-- ============================================== -->
                        <div v-else class="order-details-view">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h2 class="account-page-title">My Orders</h2>
                                    <p class="account-page-subtitle">Manage your orders, track shipments, and view order history</p>
                                </div>
                                <button @click="backToList" class="btn-back mt-2" style="background:none; border:none; color:#1b5e38; font-weight:bold; cursor:pointer;">
                                    &larr; Back
                                </button>
                            </div>

                            <div class="order-details-header">
                                <div class="detail-h-item" style="width: 25%;">
                                    <label>ORDER DATE</label>
                                    <div>{{ formatDateShort(selectedOrder.created_at) }}</div>
                                </div>
                                <div class="detail-h-item" style="width: 25%;">
                                    <label>PAYMENT STATUS</label>
                                    <div>{{ selectedOrder.payment_status || 'Paid' }}</div>
                                </div>
                                <div class="detail-h-item" style="width: 25%;">
                                    <label>FULFILLMENT STATUS</label>
                                    <div>{{ getStatusLabel(selectedOrder.status) }}</div>
                                </div>
                                <div class="detail-h-item" style="width: 25%;">
                                    <label>ORDER NO.</label>
                                    <div>Order NO. {{ selectedOrder.reference }}</div>
                                </div>
                            </div>

                            <div class="order-table-container">
                                <table class="order-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;">PRODUCT</th>
                                            <th style="width: 20%;">PRICE</th>
                                            <th style="width: 15%;">QUANTITY</th>
                                            <th style="width: 15%; text-align: right;">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="line in selectedOrder.lines" :key="line.id">
                                            <td>
                                                <div class="d-flex" style="gap: 16px;">
                                                    <div style="width: 80px; height: 80px; background: #f5f5f5; display:flex; align-items:center; justify-content:center;">
                                                        <img :src="line.product?.images?.[0]?.url || '/img/tshirt-placeholder.svg'" style="max-width:100%; max-height:100%; object-fit:contain;" alt="">
                                                    </div>
                                                    <div>
                                                        <div class="font-bold" style="font-size: 14px; margin-bottom: 4px; color: #333;">{{ line.product_name }}</div>
                                                        <div class="text-muted" style="font-size: 12px; line-height: 1.6;">
                                                            <div v-if="line.product?.category">Garment: {{ line.product.category.name }}</div>
                                                            <div v-if="line.meta?.color">Color: {{ line.meta.color }}</div>
                                                            <div v-if="line.meta?.size">Size: {{ line.meta.size }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="vertical-align: top; padding-top: 24px; color: #555;">PHP {{ (line.unit_price / 100).toFixed(2) }}</td>
                                            <td style="vertical-align: top; padding-top: 24px; color: #555;">{{ line.quantity }}</td>
                                            <td style="vertical-align: top; padding-top: 24px; color: #555; text-align: right;">PHP {{ (line.total / 100).toFixed(2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="order-summary-box">
                                <div class="summary-line">
                                    <span>Subtotal</span>
                                    <span>PHP {{ (selectedOrder.sub_total / 100).toFixed(2) }}</span>
                                </div>
                                <div class="summary-line">
                                    <span>Shipping</span>
                                    <span>PHP {{ ((selectedOrder.tax_total || 0) / 100).toFixed(2) }}</span> 
                                </div>
                                <div class="summary-line summary-total">
                                    <span class="font-bold">Total</span>
                                    <span class="font-bold font-lg" style="font-size: 18px;">PHP {{ (selectedOrder.total / 100).toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- ============================================== -->
                    <!-- ADDRESSES TAB                                  -->
                    <!-- ============================================== -->
                    <template v-else-if="activeTab === 'addresses'">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h2 class="account-page-title">My Addresses</h2>
                                <p class="account-page-subtitle">Manage and protect your account</p>
                            </div>
                        </div>

                        <div v-if="isAddressFormVisible" class="address-form-box">
                            <h4 style="margin-bottom: 20px;">{{ editingAddressId ? 'Edit Address' : 'Add New Address' }}</h4>
                            <form @submit.prevent="submitAddress">
                                <div class="form-row-grid">
                                    <div class="form-group full-width">
                                        <label class="required">Full Name</label>
                                        <input v-model="addressForm.name" type="text" class="grocery-input" required />
                                    </div>
                                    <div class="form-group full-width">
                                        <label class="required">Street Address</label>
                                        <input v-model="addressForm.line1" type="text" class="grocery-input" placeholder="House number and street name" required />
                                    </div>
                                    <div class="form-group full-width">
                                        <label>Apartment, suite, unit, etc.</label>
                                        <input v-model="addressForm.line2" type="text" class="grocery-input" />
                                    </div>
                                    <div class="form-group">
                                        <label class="required">Country/Region</label>
                                        <select v-model="addressForm.country_id" class="grocery-input" required>
                                            <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Province</label>
                                        <select v-if="addressForm.country_id === defaultCountryId" v-model="addressForm.province" class="grocery-input">
                                            <option value="">Select a province</option>
                                            <option v-for="p in provinces" :key="p.id" :value="p.province_name">{{ p.province_name }}</option>
                                        </select>
                                        <input v-else v-model="addressForm.province" type="text" class="grocery-input" />
                                    </div>
                                    <div class="form-group">
                                        <label class="required">City</label>
                                        <select v-if="addressForm.country_id === defaultCountryId && cities.length > 0" v-model="addressForm.city" class="grocery-input" required>
                                            <option value="">Select a city</option>
                                            <option v-for="c in cities" :key="c.id" :value="c.city_name">{{ c.city_name }}</option>
                                        </select>
                                        <input v-else v-model="addressForm.city" type="text" class="grocery-input" required />
                                    </div>
                                    <div class="form-group">
                                        <label>Barangay</label>
                                        <input v-model="addressForm.barangay" type="text" class="grocery-input" />
                                    </div>
                                    <div class="form-group full-width">
                                        <label class="required">Postal / Zip Code</label>
                                        <input v-model="addressForm.postcode" type="text" class="grocery-input" required />
                                    </div>
                                </div>
                                <div class="form-actions mt-4 d-flex" style="gap: 12px; justify-content: flex-end;">
                                    <button type="button" @click="cancelAddressForm" class="btn-cancel" style="padding: 10px 20px; border: 1px solid #ccc; background: white; border-radius: 6px; cursor: pointer;">Cancel</button>
                                    <button type="submit" class="btn-save" style="padding: 10px 20px; border: none; background: #1b5e38; color: white; border-radius: 6px; cursor: pointer;" :disabled="addressForm.processing">Save Address</button>
                                </div>
                            </form>
                        </div>

                        <div v-else class="address-grid">
                            <!-- Existing Addresses -->
                            <div v-for="addr in user.addresses" :key="addr.id" class="address-card">
                                <div class="address-card-content">
                                    <div class="address-card-header">
                                        <span class="addr-name">{{ addr.first_name }} {{ addr.last_name }}</span>
                                        <span v-if="addr.default" class="addr-default-badge">DEFAULT</span>
                                    </div>
                                    <div class="addr-body">
                                        <p>{{ addr.line1 }}</p>
                                        <p v-if="addr.line2">{{ addr.line2 }}</p>
                                        <p>{{ [addr.barangay, addr.city, addr.province].filter(Boolean).join(', ') }}</p>
                                        <p>{{ addr.postcode }}</p>
                                        <p>{{ addr.country?.name || 'Philippines' }}</p>
                                    </div>
                                    <div class="addr-actions">
                                        <button @click="editAddress(addr)" class="btn-link">Edit</button>
                                        <button @click="removeAddress(addr.id)" class="btn-link">Remove</button>
                                        <button v-if="!addr.default" @click="setDefault(addr.id)" class="btn-link">Set Default</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Add New Address Card -->
                            <div class="address-card add-new-card" @click="openAddressForm()">
                                <div class="add-new-content">
                                    <div class="add-icon">+</div>
                                    <div class="add-title">Add New Address</div>
                                    <div class="add-subtitle" style="font-size:12px; color:#888; margin-top:8px;">Manage and protect your account</div>
                                </div>
                            </div>
                        </div>
                    </template>

                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    user: Object,
    orders: {
        type: Array,
        default: () => []
    },
    countries: Array,
    defaultCountryId: Number,
});

const activeTab = ref('orders');
const orderTab = ref('all');
const selectedOrder = ref(null);

const filteredOrders = computed(() => {
    if (!props.orders) return [];
    if (orderTab.value === 'all') return props.orders;
    if (orderTab.value === 'shipping') return props.orders.filter(o => ['sent_to_tpinklab', 'confirmed'].includes(o.status));
    if (orderTab.value === 'arrived') return props.orders.filter(o => o.status === 'completed');
    if (orderTab.value === 'cancelled') return props.orders.filter(o => o.status === 'cancelled');
    return props.orders;
});

const getStatusBadgeClass = (status) => {
    if (status === 'completed') return 'badge-delivered';
    if (status === 'cancelled') return 'badge-processing';
    if (status === 'sent_to_tpinklab') return 'badge-shipping';
    return 'badge-processing';
};

const getStatusLabel = (status) => {
    if (status === 'completed') return 'Delivered';
    if (status === 'cancelled') return 'Cancelled';
    if (status === 'sent_to_tpinklab') return 'Shipping';
    return 'PROCESSING';
};

const viewDetails = (order) => {
    selectedOrder.value = order;
};

const backToList = () => {
    selectedOrder.value = null;
};

const cancelOrder = (order) => {
    if (confirm('Are you sure you want to cancel this order?')) {
        router.patch(route('store.brand-partner.order.cancel', { reference: order.reference }), {}, {
            preserveScroll: true,
        });
    }
};

const formatDateShort = (date) => {
    if (!date) return '';
    const d = new Date(date);
    return `${String(d.getMonth() + 1).padStart(2, '0')}/${String(d.getDate()).padStart(2, '0')}/${d.getFullYear()}`;
};

const formatDateLong = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

/* ADDRESSES LOGIC */
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
            axios.post(route('store.address.states'), { country_id: value }).then(({ data }) => {
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
        axios.get(route('store.address.cities'), {
            params: { province_id: province.id },
        }).then(({ data }) => {
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
    
    // trigger watch correctly for cities before setting city
    if (addr.province && addressForm.country_id === props.defaultCountryId) {
        const province = provinces.value.find((p) => p.province_name === addr.province);
        if (province) {
            axios.get(route('store.address.cities'), { params: { province_id: province.id } }).then(({ data }) => {
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
        addressForm.patch(route('store.brand-partner.addresses.update', editingAddressId.value), {
            preserveScroll: true,
            onSuccess: () => { isAddressFormVisible.value = false; }
        });
    } else {
        if (props.user.addresses?.length === 0) {
            addressForm.default = true;
        }
        addressForm.post(route('store.brand-partner.addresses.store'), {
            preserveScroll: true,
            onSuccess: () => { isAddressFormVisible.value = false; }
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
    const addr = props.user.addresses.find(a => a.id === id);
    router.patch(route('store.brand-partner.addresses.update', id), {
        name: (addr.first_name + ' ' + addr.last_name).trim() || 'Tim Lim',
        line1: addr.line1,
        line2: addr.line2,
        country_id: addr.country_id,
        province: addr.province,
        city: addr.city,
        barangay: addr.barangay,
        postcode: addr.postcode,
        default: true
    }, { preserveScroll: true });
};

</script>

<style scoped>
.account-page {
    min-height: 60vh;
    padding: 40px 0 60px;
    background: #f9f9f9;
    font-family: 'Public Sans', sans-serif;
    margin-top: 80px;
}

.account-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 24px;
}

.account-breadcrumb {
    font-size: 13px;
    color: #888;
    margin-bottom: 24px;
}

.account-breadcrumb a {
    color: #888;
    text-decoration: none;
}

.account-breadcrumb a:hover {
    color: #ff9505;
}

.account-wrapper {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 32px;
    align-items: start;
}

/* Sidebar */
.account-sidebar {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #eee;
    padding: 24px;
}

.sidebar-title {
    font-size: 18px;
    font-weight: 800;
    color: #1b1b3e;
    margin: 0 0 20px;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sidebar-menu li a,
.sidebar-menu li .logout-btn {
    display: block;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #555;
    text-decoration: none;
    cursor: pointer;
    background: none;
    border: none;
    width: 100%;
    text-align: left;
    font-family: 'Public Sans', sans-serif;
    transition: background 0.15s, color 0.15s;
}

.sidebar-menu li a:hover,
.sidebar-menu li .logout-btn:hover {
    background: #fff8ee;
    color: #ff9505;
}

.sidebar-menu li.active a {
    background: #1b5e38;
    color: #fff;
    font-weight: 700;
}

/* Main content */
.account-content {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #eee;
    padding: 32px;
}

.account-page-title {
    font-size: 22px;
    font-weight: 800;
    color: #1b1b3e;
    margin: 0 0 4px;
}

.account-page-subtitle {
    font-size: 13px;
    color: #888;
    margin: 0 0 28px;
}

/* Profile Tab Defaults */
.account-section {
    border-top: 1px solid #f0f0f0;
    padding-top: 20px;
}
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.section-header h4 {
    font-size: 15px;
    font-weight: 700;
    color: #1b1b3e;
    margin: 0;
}
.detail-field {
    margin-bottom: 16px;
}
.detail-field label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.8px;
    color: #aaa;
    margin-bottom: 6px;
}
.detail-value {
    background: #f5f5f5;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 500;
    color: #333;
}


/* Orders Tab Defaults */
.orders-tabs {
    display: flex;
    border-bottom: 1px solid #eee;
    margin-bottom: 24px;
}
.orders-tabs button {
    flex: 1;
    background: none;
    border: none;
    color: #888;
    padding: 12px 16px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
}
.orders-tabs button.active {
    color: #333;
    border-bottom-color: #333;
    font-weight: 700;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.order-card {
    border: 1px solid #eee;
}
.order-card-header {
    background: #e9e9e9;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #555;
    text-transform: uppercase;
}
.font-bold {
    font-weight: 800;
}
.order-card-body {
    padding: 24px;
}

.order-line-item {
    display: flex;
    justify-content: space-between;
    gap: 24px;
    padding-bottom: 24px;
    margin-bottom: 24px;
    border-bottom: 1px dashed #eee;
}
.order-line-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
}

.line-img-wrap {
    width: 80px;
    height: 80px;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.line-img-wrap img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
}

.line-details {
    flex: 1;
}
.line-title {
    font-weight: 800;
    font-size: 13px;
    margin-bottom: 6px;
    color: #333;
}
.line-variants {
    font-size: 12px;
    color: #777;
    line-height: 1.6;
}

.line-price-col {
    width: 120px;
    text-align: left;
}
.price-current {
    font-size: 12px;
    font-weight: 800;
    padding-top: 2px;
}
.text-red {
    color: #ff5e5e;
}
.price-old {
    font-size: 12px;
    color: #aaa;
    margin-top: 4px;
}
.text-strike {
    text-decoration: line-through;
}

.status-col {
    width: 200px;
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
}
.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    margin-right: 8px;
}
.badge-delivered {
    background: #c8e6c9;
    color: #1b5e38;
}
.badge-delivered .status-dot {
    background: #1b5e38;
}
.badge-shipping {
    background: #ffe0b2;
    color: #e65100;
}
.badge-shipping .status-dot {
    background: #e65100;
}
.badge-processing {
    background: #c1c1c1;
    color: #000000;
}
.badge-processing .status-dot {
    background: #fe0000;
}

.order-card-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 20px;
}
.btn-view-details {
    background: #0b6630;
    color: #fff;
    border: none;
    padding: 10px 24px;
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
    border-radius: 2px;
    transition: background 0.2s;
}
.btn-view-details:hover {
    background: #084822;
}
.btn-cancel-order {
    background: transparent;
    color: #ff5e5e;
    border: 1px solid #ff5e5e;
    padding: 10px 24px;
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
    border-radius: 2px;
    transition: all 0.2s;
}
.btn-cancel-order:hover {
    background: #ff5e5e;
    color: #fff;
}


/* Order Details Layout */
.order-details-header {
    background: #e9e9e9;
    display: flex;
    padding: 24px;
    margin-bottom: 24px;
}
.detail-h-item label {
    font-size: 10px;
    font-weight: 800;
    color: #555;
    margin-bottom: 8px;
    display: block;
}
.detail-h-item div {
    font-size: 13px;
    color: #333;
    font-weight: 600;
}

.order-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 24px;
}
.order-table th {
    text-align: left;
    padding: 12px 12px 12px 0;
    font-size: 10px;
    font-weight: 800;
    color: #888;
    border-bottom: 1px solid #eee;
}
.order-table td {
    padding: 24px 12px 24px 0;
    border-bottom: 1px solid #f9f9f9;
}

.order-summary-box {
    width: 380px;
    margin-left: auto;
    border-top: 1px solid #eee;
    padding-top: 24px;
}
.summary-line {
    display: flex;
    justify-content: space-between;
    margin-bottom: 16px;
    font-size: 13px;
    color: #555;
    font-weight: 500;
}
.summary-total {
    border-top: 1px solid #eee;
    padding-top: 16px;
    color: #333;
}


@media (max-width: 768px) {
    .account-wrapper {
        grid-template-columns: 1fr;
    }
    .account-page {
        margin-top: 60px;
    }
    .order-line-item {
        flex-direction: column;
        gap: 12px;
    }
    .line-price-col, .status-col {
        width: 100%;
        text-align: left;
    }
    .order-details-header {
        flex-direction: column;
        gap: 16px;
    }
    .detail-h-item {
        width: 100% !important;
    }
    .order-table-container {
        overflow-x: auto;
    }
    .order-table th, .order-table td {
        min-width: 100px;
    }
    .order-summary-box {
        width: 100%;
    }
}

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
    transition: transform 0.2s, box-shadow 0.2s;
}
.address-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
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
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.04);
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
    to { transform: translate(-50%, -50%) rotate(360deg); }
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
    
    .btn-cancel, .btn-save {
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
