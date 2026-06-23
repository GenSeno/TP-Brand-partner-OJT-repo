<template>
  <Head :title="`Checkout - ${brandPartner.name}`" />

  <div class="grocery-checkout-section">
    <!-- Header -->
    <div class="grocery-header">
      <div class="grocery-container">
        <div class="header-inner">
          <Link :href="route('store.brand-partner.cart')" class="header-back">
            <i class="ri-arrow-left-s-line"></i>
          </Link>
          <h2 class="header-title">Checkout</h2>
        </div>
      </div>
    </div>

    <div class="grocery-container">
      <form @submit.prevent="submitOrder" class="checkout-grid">
        <!-- Left: Form -->
        <div class="form-column">
          <!-- Contact Information -->
          <div class="grocery-card">
            <div class="card-header">
              <i class="ri-user-3-line" style="color: #ff9505"></i>
              <h5>Contact Information</h5>
            </div>
            <div class="form-row-grid">
              <div class="form-group">
                <label class="required">Full Name</label>
                <input
                  v-model="form.customer_name"
                  type="text"
                  class="grocery-input"
                  :class="{
                    'input-error': form.errors.customer_name,
                  }"
                  placeholder="Enter your full name"
                  required
                />
                <span class="error-text" v-if="form.errors.customer_name">
                  {{ form.errors.customer_name }}
                </span>
              </div>
              <div class="form-group">
                <label class="required">Email</label>
                <input
                  v-model="form.customer_email"
                  type="email"
                  class="grocery-input"
                  :class="{
                    'input-error': form.errors.customer_email || emailError,
                  }"
                  placeholder="Enter your email"
                  @input="emailTouched = true"
                  required
                />
                <span class="error-text" v-if="form.errors.customer_email">
                  {{ form.errors.customer_email }}
                </span>
                <span class="error-text" v-else-if="emailError">
                  {{ emailError }}
                </span>
              </div>
              <div class="form-group full-width">
                <label class="required">Phone</label>
                <input
                  v-model="form.customer_phone"
                  type="tel"
                  class="grocery-input"
                  :class="{
                    'input-error': form.errors.customer_phone,
                  }"
                  placeholder="Enter your phone number"
                  required
                />
                <span class="error-text" v-if="form.errors.customer_phone">
                  {{ form.errors.customer_phone }}
                </span>
              </div>
            </div>
          </div>

          <!-- Shipping Address -->
          <div class="grocery-card">
            <div
              class="card-header d-flex justify-content-between align-items-center"
            >
              <div>
                <i class="ri-map-pin-line" style="color: #ff9505"></i>
                <h5 style="display: inline-block; margin-left: 10px">
                  Shipping Address
                </h5>
              </div>
              <!-- Address Selector -->
              <div v-if="$page.props.userAddresses?.length > 0">
                <select
                  @change="applySavedAddress($event)"
                  class="grocery-input"
                  style="padding: 6px 12px; width: 200px; font-size: 13px"
                >
                  <option value="">Select a saved address...</option>
                  <option
                    v-for="addr in $page.props.userAddresses"
                    :key="addr.id"
                    :value="addr.id"
                  >
                    {{ addr.first_name }} {{ addr.last_name }}
                    {{ addr.default ? '(Default)' : '' }} - {{ addr.line1 }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Street Address -->
            <div class="form-group" style="margin-bottom: 16px">
              <label class="required">Street Address</label>
              <input
                v-model="form.shipping_line1"
                type="text"
                class="grocery-input"
                :class="{
                  'input-error': form.errors.shipping_line1,
                }"
                placeholder="House number and street name"
                required
              />
              <span class="error-text" v-if="form.errors.shipping_line1">
                {{ form.errors.shipping_line1 }}
              </span>
              <input
                v-model="form.shipping_line2"
                type="text"
                class="grocery-input"
                :class="{
                  'input-error': form.errors.shipping_line2,
                }"
                placeholder="Apartment, suite, unit, etc."
                style="margin-top: 8px"
              />
              <span class="error-text" v-if="form.errors.shipping_line2">
                {{ form.errors.shipping_line2 }}
              </span>
            </div>

            <!-- Country / Province -->
            <div class="form-row-grid" style="margin-bottom: 16px">
              <div class="form-group">
                <label class="required">Country/Region</label>
                <select
                  v-model="form.shipping_country_id"
                  class="grocery-input"
                  :class="{
                    'input-error': form.errors.shipping_country_id,
                  }"
                  required
                >
                  <option value="">Select a country</option>
                  <option
                    v-for="country in countries"
                    :key="country.id"
                    :value="country.id"
                  >
                    {{ country.emoji }} {{ country.name }}
                  </option>
                </select>
                <span class="error-text" v-if="form.errors.shipping_country_id">
                  {{ form.errors.shipping_country_id }}
                </span>
              </div>
              <div class="form-group">
                <label class="required">Province</label>
                <select
                  v-if="isShippingPH"
                  v-model="form.shipping_province"
                  class="grocery-input"
                  :class="{
                    'input-error': form.errors.shipping_province,
                  }"
                  required
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
                  v-model="form.shipping_province"
                  type="text"
                  class="grocery-input"
                  :class="{
                    'input-error': form.errors.shipping_province,
                  }"
                  placeholder="e.g. N/A"
                  required
                />
                <span class="error-text" v-if="form.errors.shipping_province">
                  {{ form.errors.shipping_province }}
                </span>
              </div>
            </div>

            <!-- City / Barangay -->
            <div class="form-row-grid" style="margin-bottom: 16px">
              <div class="form-group">
                <label class="required">City</label>
                <select
                  v-if="isShippingPH && cities.length > 0"
                  v-model="form.shipping_city"
                  class="grocery-input"
                  :class="{
                    'input-error': form.errors.shipping_city,
                  }"
                  required
                >
                  <option value="">Select a city</option>
                  <option v-for="c in cities" :key="c.id" :value="c.city_name">
                    {{ c.city_name }}
                  </option>
                </select>
                <select
                  v-else-if="!isShippingPH && cities.length > 0"
                  v-model="form.shipping_city"
                  class="grocery-input"
                  :class="{
                    'input-error': form.errors.shipping_city,
                  }"
                  required
                >
                  <option value="">Select a city</option>
                  <option v-for="c in cities" :key="c.value" :value="c.value">
                    {{ c.label }}
                  </option>
                </select>
                <input
                  v-else
                  v-model="form.shipping_city"
                  type="text"
                  class="grocery-input"
                  :class="{
                    'input-error': form.errors.shipping_city,
                  }"
                  placeholder="Enter city"
                  required
                />
                <span class="error-text" v-if="form.errors.shipping_city">
                  {{ form.errors.shipping_city }}
                </span>
              </div>
              <div class="form-group">
                <label class="required">Barangay</label>
                <input
                  v-model="form.shipping_barangay"
                  type="text"
                  class="grocery-input"
                  :class="{
                    'input-error': form.errors.shipping_barangay,
                  }"
                  required
                />
                <span class="error-text" v-if="form.errors.shipping_barangay">
                  {{ form.errors.shipping_barangay }}
                </span>
              </div>
            </div>

            <!-- Postal / Zip Code -->
            <div class="form-group">
              <label class="required">Postal / Zip Code</label>
              <input
                v-model="form.shipping_postcode"
                type="text"
                class="grocery-input"
                :class="{
                  'input-error': form.errors.shipping_postcode,
                }"
                required
              />
              <span class="error-text" v-if="form.errors.shipping_postcode">
                {{ form.errors.shipping_postcode }}
              </span>
            </div>
          </div>

          <!-- Additional Notes -->
          <div class="grocery-card">
            <div class="card-header">
              <i class="ri-sticky-note-line" style="color: #ff9505"></i>
              <h5>Additional Notes</h5>
            </div>
            <div class="form-group">
              <textarea
                v-model="form.notes"
                rows="3"
                class="grocery-input"
                placeholder="Any special instructions or notes for your order..."
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Right: Order Summary -->
        <div class="summary-column">
          <div class="grocery-card summary-card">
            <h4 class="summary-heading">Order Summary</h4>

            <div class="summary-items">
              <div
                class="summary-item"
                v-for="item in cart.items"
                :key="item.id"
              >
                <div class="si-image-wrap">
                  <img
                    :src="
                      item.product.image_url || '/img/tshirt-placeholder.svg'
                    "
                    :alt="item.product.name"
                  />
                  <span class="si-qty-badge">{{ item.quantity }}</span>
                </div>
                <div class="si-details">
                  <span class="si-name">{{ item.product.name }}</span>
                  <span
                    v-if="item.color || item.size"
                    style="font-size: 11px; color: #888"
                  >
                    <span v-if="item.color">{{ item.color }}</span>
                    <span v-if="item.color && item.size"> / </span>
                    <span v-if="item.size">{{ item.size }}</span>
                  </span>
                  <span class="si-each"
                    >{{ formatCurrency(item.price) }} each</span
                  >
                </div>
                <span class="si-total">{{ formatCurrency(item.total) }}</span>
              </div>
            </div>

            <div class="summary-divider"></div>

            <ul class="summary-totals">
              <li>
                <span>Subtotal</span>
                <span>{{ formatCurrency(cart.subtotal) }}</span>
              </li>
              <li v-if="cart.discount > 0">
                <span>Discount</span>
                <span class="discount-amount"
                  >-{{ formatCurrency(cart.discount) }}</span
                >
              </li>
            </ul>

            <div class="summary-divider"></div>

            <div class="grand-total-row">
              <span>Total</span>
              <span>{{ formatCurrency(cart.total) }}</span>
            </div>

            <label class="checkout-terms">
              <input type="checkbox" v-model="form.terms_accepted" />
              <span>
                I agree to the
                <a :href="route('store.brand-partner.terms', brandPartner?.slug)" target="_blank" rel="noopener" class="checkout-terms-link">Terms &amp; Conditions</a>,
                <a :href="route('store.brand-partner.refund', brandPartner?.slug)" target="_blank" rel="noopener" class="checkout-terms-link">Refund Policy</a>,
                <a :href="route('store.brand-partner.shipping', brandPartner?.slug)" target="_blank" rel="noopener" class="checkout-terms-link">Shipping Policy</a>,
                and
                <a :href="route('store.brand-partner.cancellation', brandPartner?.slug)" target="_blank" rel="noopener" class="checkout-terms-link">Cancellation Policy</a>.
              </span>
            </label>

            <button
              type="submit"
              class="grocery-btn theme-btn place-order-btn"
              :disabled="form.processing || !form.terms_accepted || (emailTouched && !!emailError)"
            >
              <span v-if="form.processing" class="btn-loading">
                <i class="ri-loader-4-line spin"></i>
                Processing...
              </span>
              <span v-else>
                <i class="ri-check-double-line"></i> Place Order
              </span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  brandPartner: Object,
  cart: Object,
  countries: Array,
  defaultCountryId: Number,
  userAddresses: Array,
  order: Object,
});

const PHILIPPINES_ID = props.defaultCountryId ?? 175;

const provinces = ref([]);
const cities = ref([]);

axios.get(route('store.address.provinces')).then(({ data }) => {
  provinces.value = data;
});

const form = useForm({
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  shipping_line1: '',
  shipping_line2: '',
  shipping_province: '',
  shipping_city: '',
  shipping_barangay: '',
  shipping_postcode: '',
  shipping_country_id: PHILIPPINES_ID,
  notes: '',
  terms_accepted: false,
});

const emailTouched = ref(false);

const emailError = computed(() => {
  if (!emailTouched.value || !form.customer_email) return '';
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(form.customer_email) ? '' : 'Please enter a valid email address.';
});

watch(() => form.customer_email, () => {
  if (!emailTouched.value) emailTouched.value = true;
  if (form.errors.customer_email) {
    form.errors.customer_email = '';
  }
});

const applySavedAddress = (event) => {
  const addressId = event.target.value;
  if (!addressId) return;

  const addr = props.userAddresses.find((a) => a.id == addressId);
  if (!addr) return;

  form.customer_name = (addr.first_name + ' ' + addr.last_name).trim();
  form.shipping_line1 = addr.line1;
  form.shipping_line2 = addr.line2;
  form.shipping_country_id = addr.country_id || PHILIPPINES_ID;

  // Assigning province will trigger the watcher which clears the city
  form.shipping_province = addr.province;

  // After province watcher completes data fetching, apply city
  setTimeout(() => {
    form.shipping_city = addr.city;
  }, 500);

  form.shipping_barangay = addr.barangay;
  form.shipping_postcode = addr.postcode;
};

const isShippingPH = computed(
  () => form.shipping_country_id === PHILIPPINES_ID,
);

watch(
  () => form.shipping_country_id,
  (value) => {
    form.shipping_province = '';
    form.shipping_city = '';
    cities.value = [];
    if (value && value !== PHILIPPINES_ID) {
      axios
        .post(route('store.address.states'), { country_id: value })
        .then(({ data }) => {
          cities.value = data.map((s) => ({
            label: s.name,
            value: s.name,
          }));
        });
    }
  },
);

watch(
  () => form.shipping_province,
  (value) => {
    if (!isShippingPH.value) return;
    form.shipping_city = '';
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

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
  }).format(amount / 100);
};

const submitOrder = () => {
  form.post(route('store.brand-partner.checkout.store'));
};

//Payment Gateway
function pay() {
  router.post(route('store.payment.invoice', props.order.reference));
}
</script>

<style scoped>
.grocery-checkout-section {
  min-height: 60vh;
  padding-bottom: 40px;
  font-family: 'Public Sans', sans-serif;
  background: rgb(var(--grocery-light-bg));
  /* Grocery Theme Color Variables */
  --grocery-theme: 60, 133, 153; /* Main teal/cyan color: rgb(60, 133, 153) */
  --grocery-content: 143, 143, 178; /* Light gray-blue content text */
  --grocery-title: 27, 27, 62; /* Dark blue-gray for titles */
  --grocery-border: 232, 232, 232; /* Light gray borders */
  --grocery-primary: 254, 175, 24; /* Yellow/orange accent */
  --grocery-light-bg: 247, 247, 247; /* Light gray background */
  --grocery-rating: 255, 191, 19; /* Gold/yellow for ratings */
}

/* Header */
.grocery-header {
  background: #fff;
  padding: 16px 0;
  border-bottom: 1px solid rgb(var(--grocery-border));
  margin-bottom: 24px;
  padding-top: 100px;
}

.grocery-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 16px;
}

.header-inner {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-back {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #f5f5f5;
  border: 1.5px solid #ff9505;
  color: #ff9505;
  text-decoration: none;
  font-size: 20px;
  transition: background 0.2s;
}

.header-back:hover {
  background: #fff3e0;
}

.header-title {
  font-size: 20px;
  font-weight: 800;
  color: rgb(var(--grocery-title));
  margin: 0;
  flex: 1;
  display: flex;
  align-items: center;
  gap: 8px;
}

.header-back:hover {
  background: rgb(var(--grocery-border));
}

.header-title {
  font-size: 20px;
  font-weight: 800;
  color: rgb(var(--grocery-title));
  margin: 0;
}

/* Grid Layout */
.checkout-grid {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 24px;
  align-items: start;
}

/* Form Column */
.form-column {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* Grocery Card */
.grocery-card {
  background: #fff;
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.card-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}

.card-header i {
  font-size: 22px;
  color: rgb(var(--grocery-theme));
}

.card-header h5 {
  font-size: 16px;
  font-weight: 700;
  color: rgb(var(--grocery-title));
  margin: 0;
}

/* Form Row Grid */
.form-row-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-size: 13px;
  font-weight: 700;
  color: #555;
}

.form-group label.required::after {
  content: ' *';
  color: #ff4757;
}

/* Grocery Input */
.grocery-input {
  padding: 12px 14px;
  border: 1.5px solid #e8e8e8;
  border-radius: 10px;
  font-size: 14px;
  font-family: 'Public Sans', sans-serif;
  color: #333;
  background: #fff;
  transition:
    border-color 0.2s,
    box-shadow 0.2s;
  outline: none;
  width: 100%;
  box-sizing: border-box;
}

.grocery-input:focus {
  border-color: rgb(var(--grocery-theme));
  box-shadow: 0 0 0 3px rgba(255, 141, 47, 0.1);
}

.grocery-input.input-error {
  border-color: #ff4757;
}

.grocery-input.input-error:focus {
  box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1);
}

textarea.grocery-input {
  resize: vertical;
  min-height: 80px;
}

.error-text {
  font-size: 12px;
  color: #ff4757;
  font-weight: 500;
}

/* Summary Column */
.summary-column {
  position: sticky;
  top: 80px;
}

.summary-card {
  padding: 24px;
}

.summary-heading {
  font-size: 18px;
  font-weight: 800;
  color: rgb(var(--grocery-title));
  margin: 0 0 18px;
}

/* Summary Items */
.summary-items {
  max-height: 260px;
  overflow-y: auto;
  overflow-x: visible;
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 16px;
  padding-top: 6px;
  padding-right: 6px;
}

.summary-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.si-image-wrap {
  position: relative;
  flex-shrink: 0;
}

.si-image-wrap img {
  width: 48px;
  height: 48px;
  object-fit: cover;
  border-radius: 10px;
  background: #f5f5f5;
}

.si-qty-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  background: #ff9505;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #fff;
}

.si-details {
  flex: 1;
  min-width: 0;
}

.si-name {
  font-size: 13px;
  font-weight: 700;
  color: rgb(var(--grocery-title));
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.si-each {
  font-size: 11px;
  color: #bbb;
}

.si-total {
  font-size: 14px;
  font-weight: 700;
  color: #555;
  flex-shrink: 0;
}

/* Summary Totals */
.summary-divider {
  height: 1px;
  background: rgb(var(--grocery-border));
  margin: 12px 0;
}

.summary-totals {
  list-style: none;
  padding: 0;
  margin: 0;
}

.summary-totals li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 6px 0;
  font-size: 14px;
  color: #777;
}

.discount-amount {
  color: #2ed573;
  font-weight: 600;
}

.grand-total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 0;
}

.grand-total-row span:first-child {
  font-size: 16px;
  font-weight: 800;
  color: rgb(var(--grocery-title));
}

.grand-total-row span:last-child {
  font-size: 20px;
  font-weight: 900;
  color: #ff9505;
}

/* Checkout Terms */
.checkout-terms {
  display: flex;
  gap: 10px;
  align-items: flex-start;
  margin-top: 16px;
  padding: 14px;
  background: #fefcf5;
  border: 1px solid #f5edd6;
  border-radius: 10px;
  font-size: 12px;
  color: #555;
  line-height: 1.7;
}

.checkout-terms input[type="checkbox"] {
  margin-top: 3px;
  width: 15px;
  height: 15px;
  flex-shrink: 0;
  accent-color: #ff9505;
}

.checkout-terms-link {
  color: #ff9505;
  text-decoration: none;
  font-weight: 600;
}

.checkout-terms-link:hover {
  text-decoration: underline;
}

.grocery-btn.theme-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 14px 24px;
  background: #ff9505;
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 700;
  font-family: 'Public Sans', sans-serif;
  text-decoration: none;
  cursor: pointer;
  transition:
    background 0.2s,
    transform 0.15s;
}

.grocery-btn.theme-btn:hover {
  background: #005523;
  color: #fff;
  transform: translateY(-1px);
}

.grocery-btn.theme-btn:disabled {
  background: #ddd;
  color: #999;
  cursor: not-allowed;
  transform: none;
}

.place-order-btn {
  margin-top: 18px;
}

.place-order-btn i {
  font-size: 18px;
}

.btn-loading {
  display: flex;
  align-items: center;
  gap: 8px;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.spin {
  animation: spin 1s linear infinite;
  display: inline-block;
}

/* Responsive */
@media (max-width: 768px) {
  .checkout-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .summary-column {
    position: static;
  }

  .form-row-grid {
    grid-template-columns: 1fr;
  }

  .grocery-card {
    padding: 18px;
  }
}
</style>
