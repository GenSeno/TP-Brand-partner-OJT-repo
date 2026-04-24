<template>
  <Head title="My Account" />

  <div class="account-page">
    <div class="account-container">
      <!-- Breadcrumb -->
      <Breadcrumb :items="breadcrumbItems" />
      <div class="account-wrapper">
        <!-- Sidebar -->
        <aside class="account-sidebar">
          <h3 class="sidebar-title">My Account</h3>
          <ul class="sidebar-menu">
            <li :class="{ active: activeTab === 'profile' }">
              <a href="#" @click.prevent="activeTab = 'profile'">My Profile</a>
            </li>
            <li :class="{ active: activeTab === 'orders' }">
              <a href="#" @click.prevent="activeTab = 'orders'">My Orders</a>
            </li>
            <li :class="{ active: activeTab === 'addresses' }">
              <a href="#" @click.prevent="activeTab = 'addresses'"
                >My Addresses</a
              >
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
            <li :class="{ active: activeTab === 'wishlist' }">
              <a href="#" @click.prevent="activeTab = 'wishlist'"
                >My Wishlist</a
              >
            </li>
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
            <ProfileTab
              :user="user"
              @update-details="handleUpdateDetails"
              @update-email="handleUpdateEmail"
              @update-password="handleUpdatePassword"
            />
          </template>

          <!-- ============================================== -->
          <!-- ORDERS TAB                                     -->
          <!-- ============================================== -->
          <template v-else-if="activeTab === 'orders'">
            <OrdersTab :orders="orders" />
          </template>

          <!-- ============================================== -->
          <!-- ADDRESSES TAB                                  -->
          <!-- ============================================== -->
          <template v-else-if="activeTab === 'addresses'">
            <AddressesTab
              :user="user"
              :countries="countries"
              :default-country-id="defaultCountryId"
            />
          </template>
          <!-- ============================================== -->
          <!-- WISHLIST TAB                                -->
          <!-- ============================================== -->
          <template v-else-if="activeTab === 'wishlist'">
            <Wishlist
              :brandPartner="$page.props.brandPartner"
              :items="$props.wishlistItems || []"
            />
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import ProfileTab from './useraccount/ProfileTab.vue';
import OrdersTab from './useraccount/OrdersTab.vue';
import AddressesTab from './useraccount/AddressesTab.vue';
import Wishlist from './useraccount/wishlist.vue';
import Breadcrumb from '@/components/breadcrumb/layout-breadcrumb.vue';

const breadcrumbItems = [{ label: 'My Account' }];

const props = defineProps({
  user: Object,
  orders: {
    type: Array,
    default: () => [],
  },
  countries: Array,
  defaultCountryId: Number,
  wishlistItems: {
    type: Array,
    default: () => [],
  },
});

const activeTab = ref(usePage().props.tab || 'profile');

function handleUpdateDetails(data) {
  router.patch(route('store.brand-partner.account.update'), {
    firstName: data.firstName,
    lastName: data.lastName,
    gender: data.gender || null,
    dobDay: data.dobDay || '',
    dobMonth: data.dobMonth || '',
    dobYear: data.dobYear || '',
  });
}

function handleUpdateEmail(data) {
  router.patch(route('store.brand-partner.account.email'), {
    email: data.email,
  });
}

function handleUpdatePassword(data) {
  router.patch(route('store.brand-partner.account.password'), {
    oldPassword: data.oldPassword,
    newPassword: data.newPassword,
    confirmPassword: data.confirmPassword,
  });
}
</script>

<style>
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
  height: fit-content;
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
  transition:
    background 0.15s,
    color 0.15s;
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
/* ========================================
   PROFILE TAB STYLES
   ======================================== */
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
.edit-btn {
  background: none;
  border: none;
  color: #2d6a4f;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.8px;
  cursor: pointer;
  padding: 0;
  flex-shrink: 0;
  font-family: 'Public Sans', sans-serif;
}
.edit-btn:hover {
  text-decoration: underline;
}
.login-field {
  display: flex;
  align-items: center;
  gap: 16px;
}
.login-field-inner {
  flex: 1;
}
.login-field-inner label {
  display: block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.8px;
  color: #aaa;
  margin-bottom: 6px;
}

/* ========================================
   PROFILE MODALS
   ======================================== */
.profile-modal-overlay {
  position: fixed !important;
  inset: 0 !important;
  background: rgba(0, 0, 0, 0.45) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  z-index: 99999 !important;
}
.profile-modal {
  background: #fff;
  border-radius: 12px;
  padding: 36px 32px 28px;
  width: 100%;
  max-width: 560px;
  position: relative;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.18);
}
.profile-modal-close {
  position: absolute;
  top: -16px;
  right: -16px;
  background: #1f4e30;
  border: none;
  border-radius: 6px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.profile-modal-title {
  font-size: 22px;
  font-weight: 700;
  color: #1b1b3e;
  margin: 0 0 24px;
}
.profile-modal-field {
  margin-bottom: 16px;
}
.profile-modal-field > label {
  display: block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.8px;
  color: #aaa;
  margin-bottom: 6px;
}
.profile-modal-input {
  width: 100%;
  border: 1.5px solid #ddd;
  border-radius: 8px;
  padding: 12px 14px;
  font-size: 14px;
  color: #333;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.2s;
  font-family: 'Public Sans', sans-serif;
}
.profile-modal-input:focus {
  border-color: #2d6a4f;
}
.profile-dob-row {
  display: flex;
  gap: 12px;
}
.profile-dob-group {
  flex: 1;
}
.profile-dob-label {
  display: block;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.7px;
  color: #aaa;
  margin-bottom: 4px;
}
.profile-dob-input {
  text-align: center;
}
.profile-gender-row {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.profile-gender-option {
  display: flex;
  align-items: center;
  gap: 8px;
  border: 1.5px solid #ddd;
  border-radius: 50px;
  padding: 10px 20px;
  cursor: pointer;
  transition: border-color 0.2s;
}
.profile-gender-option:has(.profile-gender-radio:checked) {
  border-color: #2d6a4f;
}
.profile-gender-radio {
  accent-color: #2d6a4f;
  width: 16px;
  height: 16px;
}
.profile-gender-label {
  font-size: 13px;
  font-weight: 500;
  color: #333;
}
.profile-btn-primary {
  width: 100%;
  background: #1f4e30;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 16px;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 1px;
  cursor: pointer;
  margin-top: 8px;
  transition: background 0.2s;
  font-family: 'Public Sans', sans-serif;
}
.profile-btn-primary:hover {
  background: #174023;
}
.profile-btn-secondary {
  width: 100%;
  background: #fff;
  color: #1f4e30;
  border: 1.5px solid #ddd;
  border-radius: 8px;
  padding: 15px;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 1px;
  cursor: pointer;
  margin-top: 10px;
  transition: border-color 0.2s;
  font-family: 'Public Sans', sans-serif;
}
.profile-btn-secondary:hover {
  border-color: #1f4e30;
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
  .line-price-col,
  .status-col {
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
  .order-table th,
  .order-table td {
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
