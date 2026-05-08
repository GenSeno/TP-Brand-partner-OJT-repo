<template>
  <Head :title="`Cart - ${brandPartner.name}`" />

  <div class="grocery-cart-section">
    <div class="grocery-container">
      <!-- BreadCrumb -->
      <Breadcrumb :items="breadcrumbItems" />

      <!-- Cart with items -->
      <div v-if="localCart.items.length > 0">
        <!-- Table Header -->
        <div class="cart-table-header">
          <span class="col-item">Item</span>
          <span class="col-price">Price</span>
          <span class="col-qty">Quantity</span>
          <span class="col-total">Total</span>
          <span class="col-action"></span>
        </div>

        <!-- Cart Rows -->
        <div class="cart-table-body">
          <div class="cart-row" v-for="item in localCart.items" :key="item.id">
            <!-- Item -->
            <div class="col-item cart-item-info">
              <Link
                :href="route('store.brand-partner.product', item.product.slug)"
                class="cart-item-img-link"
              >
                <img
                  :src="item.product.image_url || '/img/tshirt-placeholder.svg'"
                  :alt="item.product.name"
                  class="cart-item-img"
                />
              </Link>
              <div class="cart-item-details">
                <Link
                  :href="
                    route('store.brand-partner.product', item.product.slug)
                  "
                  class="cart-item-name"
                >
                  {{ item.product.name }}
                </Link>
                <span v-if="item.product.collection" class="cart-item-collection">
                  {{ item.product.collection.label }}
                </span>
                <span class="cart-item-badge">PRE-ORDER</span>
                <div class="cart-item-meta">
                  <span v-if="item.product.short_description"
                    >Garment: {{ item.product.short_description }}</span
                  >
                  <span v-if="item.color">Color: {{ item.color }}</span>
                  <span v-if="item.size">Size: {{ item.size }}</span>
                  <span v-if="item.product.sku"
                    >SKU: {{ item.product.sku }}</span
                  >
                </div>
              </div>
            </div>

            <!-- Price -->
            <div class="col-price">
              <span class="cart-price">{{ formatCurrency(item.price) }}</span>
              <span
                class="cart-old-price"
                v-if="
                  item.product.compare_price &&
                  item.product.compare_price > item.price
                "
              >
                {{ formatCurrency(item.product.compare_price) }}
              </span>
            </div>

            <!-- Quantity -->
            <div class="col-qty">
              <div class="qty-controls">
                <button
                  class="qty-btn"
                  @click="updateQuantity(item.id, item.quantity - 1)"
                  :disabled="item.quantity <= 1"
                >
                  <i class="ri-subtract-line"></i>
                </button>
                <input
                  type="number"
                  class="qty-input"
                  :value="item.quantity"
                  min="1"
                  @change="updateQuantity(item.id, $event.target.value)"
                />
                <button
                  class="qty-btn"
                  @click="updateQuantity(item.id, item.quantity + 1)"
                >
                  <i class="ri-add-line"></i>
                </button>
              </div>
            </div>

            <!-- Total -->
            <div class="col-total">
              <span class="cart-total-price">{{
                formatCurrency(item.total)
              }}</span>
            </div>

            <!-- Delete -->
            <div class="col-action">
              <button
                class="delete-btn"
                @click="removeItem(item.id)"
                title="Remove item"
              >
                DELETE
              </button>
            </div>
          </div>
        </div>

        <!-- Clear Cart -->
        <div class="cart-actions-row">
          <button class="clear-cart-btn" @click="clearCart">
            <i class="ri-delete-bin-line"></i> Clear Cart
          </button>
        </div>

        <!-- Summary -->
        <div class="cart-summary-section">
          <div class="cart-summary-box">
            <div class="summary-row">
              <span class="summary-label">Subtotal</span>
              <span class="summary-value">{{
                formatCurrency(localCart.subtotal)
              }}</span>
            </div>
            <div class="summary-row" v-if="localCart.discount > 0">
              <span class="summary-label">Savings</span>
              <span class="summary-value savings-val"
                >-{{ formatCurrency(localCart.discount) }}</span
              >
            </div>
            <div class="delivery-row summary-row">
              <span>Delivery</span>
              <span class="delivery-val">To be determined</span>
            </div>
            <div class="summary-row grand-total-row">
              <span class="grand-total-label">Grand Total</span>
              <span class="grand-total-value">{{
                formatCurrency(localCart.total)
              }}</span>
            </div>
            <Link
              :href="route('store.brand-partner.checkout', brandPartner.slug)"
              class="checkout-btn"
            >
              Proceed to Checkout
              <i class="ri-arrow-right-s-line"></i>
            </Link>
          </div>
        </div>
      </div>

      <!-- Empty Cart State -->
      <div v-else class="empty-cart-state">
        <div class="empty-cart-icon">
          <i class="ri-shopping-cart-2-line"></i>
        </div>
        <h3>Your cart is empty</h3>
        <p>Looks like you haven't added any products yet.</p>
        <Link
          :href="route('store.brand-partner.index')"
          class="checkout-btn"
          style="display: inline-flex; width: auto; text-decoration: none"
        >
          <i class="ri-store-2-line"></i> Start Shopping
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Breadcrumb from '@/components/breadcrumb/layout-breadcrumb.vue';

const props = defineProps({
  brandPartner: Object,
  cart: {
    type: Object,
    default: () => ({
      items: [],
      subtotal: 0,
      discount: 0,
      total: 0,
    }),
  },
});

const localCart = ref({ ...props.cart, items: [...props.cart.items] });

const breadcrumbItems = [
  { label: 'My Account', link: '/account' },
  { label: 'Cart', link: '#' },
];

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
  }).format(amount / 100);
};

const updateQuantity = (itemId, quantity) => {
  if (quantity < 1) return;
  const qty = parseInt(quantity);

  const item = localCart.value.items.find(i => i.id === itemId);
  if (!item) return;

  const diff = qty - item.quantity;
  item.quantity = qty;
  item.total = item.price * qty;
  recalcTotals();

  fetch(route('store.brand-partner.cart.update', itemId), {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({ quantity: qty }),
  });
};

const removeItem = (itemId) => {
  localCart.value.items = localCart.value.items.filter(i => i.id !== itemId);
  recalcTotals();

  router.delete(route('store.brand-partner.cart.remove', itemId), {
    preserveScroll: true,
  });
};

const clearCart = () => {
  if (confirm('Are you sure you want to clear your cart?')) {
    router.delete(route('store.brand-partner.cart.clear'), {
      preserveScroll: true,
    });
  }
};

function recalcTotals() {
  const subtotal = localCart.value.items.reduce((sum, i) => sum + i.total, 0);
  localCart.value.subtotal = subtotal;
  localCart.value.total = subtotal;
}
</script>

<style scoped>
.grocery-cart-section {
  min-height: 60vh;
  padding-bottom: 60px;
  font-family: 'Public Sans', sans-serif;
  background: #f9f9f9;
  --grocery-theme: 60, 133, 153;
  --grocery-content: 143, 143, 178;
  --grocery-title: 27, 27, 62;
  --grocery-border: 232, 232, 232;
  --grocery-primary: 254, 175, 24;
  --grocery-light-bg: 247, 247, 247;
  --grocery-rating: 255, 191, 19;
}

/* ===== Header ===== */
.grocery-header {
  background: #fff;
  padding: 16px 0;
  border-bottom: 1px solid #f0f0f0;
  margin-bottom: 32px;
  margin-top: 80px;
}

.grocery-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 18vh 24px;
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
}

.continue-link {
  font-size: 13px;
  font-weight: 600;
  color: #ff9505;
  text-decoration: none;
}

.continue-link:hover {
  text-decoration: underline;
}

/* ===== Table Header ===== */
.cart-table-header {
  display: grid;
  grid-template-columns: 2fr 1fr 1.2fr 1fr 80px;
  gap: 16px;
  padding: 12px 20px;
  background: #fff;
  border: 1px solid #e8e8e8;
  margin-bottom: 0;
  font-size: 13px;
  font-weight: 700;
  color: #333;
  text-transform: capitalize;
  letter-spacing: 0.3px;
}

/* ===== Cart Rows ===== */
.cart-table-body {
  display: flex;
  flex-direction: column;
  background: #fff;
  border: 1px solid #e8e8e8;
  border-top: none;
}

.cart-row {
  display: grid;
  grid-template-columns: 2fr 1fr 1.2fr 1fr 80px;
  gap: 16px;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #f0f0f0;
  transition: background 0.15s;
}

.cart-row:last-child {
  border-bottom: none;
}

.cart-row:hover {
  background: #fafafa;
}

/* Item column */
.cart-item-info {
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.cart-item-img-link {
  flex-shrink: 0;
}

.cart-item-img {
  width: 72px;
  height: 72px;
  object-fit: cover;
  border-radius: 6px;
  background: #f5f5f5;
  border: 1px solid #eee;
}

.cart-item-details {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}

.cart-item-badge {
  display: inline-block;
  background: #ff9505;
  color: #fff;
  font-size: 9px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 10px;
  letter-spacing: 0.5px;
  width: fit-content;
}

.cart-item-name {
  font-size: 14px;
  font-weight: 700;
  color: rgb(var(--grocery-title));
  text-decoration: none;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  transition: color 0.2s;
}

.cart-item-name:hover {
  color: #ff9505;
}

.cart-item-collection {
  font-size: 11px;
  font-weight: 700;
  color: rgb(var(--grocery-theme));
  text-transform: uppercase;
  letter-spacing: 0.5px;
  line-height: 1.3;
}

.cart-item-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.cart-item-meta span {
  font-size: 11px;
  color: #888;
  line-height: 1.4;
}

/* Price column */
.col-price {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.cart-price {
  font-size: 14px;
  font-weight: 700;
  color: #ff9505;
}

.cart-old-price {
  font-size: 11px;
  color: #bbb;
  text-decoration: line-through;
}

/* Quantity column */
.qty-controls {
  display: inline-flex;
  align-items: center;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  overflow: hidden;
  background: #fff;
}

.qty-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: none;
  color: #555;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s;
}

.qty-btn:hover:not(:disabled) {
  background: #f5f5f5;
}

.qty-btn:disabled {
  color: #ccc;
  cursor: not-allowed;
}

.qty-input {
  width: 40px;
  height: 32px;
  border: none;
  border-left: 1px solid #e0e0e0;
  border-right: 1px solid #e0e0e0;
  background: #fff;
  text-align: center;
  font-weight: 700;
  font-size: 13px;
  font-family: 'Public Sans', sans-serif;
  color: rgb(var(--grocery-title));
  outline: none;
  -moz-appearance: textfield;
}

.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Total column */
.cart-total-price {
  font-size: 14px;
  font-weight: 700;
  color: rgb(var(--grocery-title));
}

/* Delete column */
.delete-btn {
  background: none;
  border: none;
  color: #aaa;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.5px;
  cursor: pointer;
  padding: 4px 0;
  transition: color 0.2s;
  font-family: 'Public Sans', sans-serif;
  text-transform: uppercase;
}

.delete-btn:hover {
  color: #e74c3c;
}

/* ===== Cart Actions Row ===== */
.cart-actions-row {
  display: flex;
  justify-content: flex-start;
  padding: 16px 0;
}

.clear-cart-btn {
  background: none;
  border: 1.5px solid #ffcdd2;
  color: #e74c3c;
  border-radius: 8px;
  padding: 8px 20px;
  font-size: 12px;
  font-weight: 600;
  font-family: 'Public Sans', sans-serif;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: background 0.2s;
}

.clear-cart-btn:hover {
  background: #fff5f5;
}

/* ===== Summary Section ===== */
.cart-summary-section {
  display: flex;
  justify-content: flex-end;
  margin-top: 8px;
}

.cart-summary-box {
  width: 100%;
  max-width: 440px;
  display: flex;
  flex-direction: column;
  gap: 0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
  font-size: 14px;
  color: #555;
}

.summary-row:first-child {
  border-top: 1px solid #f0f0f0;
}

.summary-label {
  font-weight: 500;
  color: #555;
}

.summary-value {
  font-weight: 600;
  color: rgb(var(--grocery-title));
}

.savings-val {
  color: #27ae60;
}

.grand-total-row {
  border-bottom: none;
  padding: 16px 0 20px;
  margin-top: 4px;
}

.grand-total-label {
  font-size: 16px;
  font-weight: 800;
  color: rgb(var(--grocery-title));
}

.grand-total-value {
  font-size: 20px;
  font-weight: 900;
  color: #ff9505;
}

/* ===== Checkout Button ===== */
.checkout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 15px 24px;
  background: #ff9505;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 800;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  font-family: 'Public Sans', sans-serif;
  text-decoration: none;
  cursor: pointer;
  transition:
    background 0.2s,
    transform 0.15s;
}

.checkout-btn:hover {
  background: #144d30;
  color: #fff;
  transform: translateY(-1px);
}

/* ===== Empty Cart ===== */
.empty-cart-state {
  text-align: center;
  padding: 80px 20px;
  background: #fff;
  border-radius: 14px;
  border: 1px solid #eee;
}

.empty-cart-icon {
  width: 110px;
  height: 110px;
  background: rgba(var(--grocery-theme), 0.08);
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
}

.empty-cart-icon i {
  font-size: 48px;
  color: #ff9505;
}

.empty-cart-state h3 {
  font-size: 22px;
  font-weight: 800;
  color: rgb(var(--grocery-title));
  margin: 0 0 8px;
}

.empty-cart-state p {
  font-size: 14px;
  color: rgb(var(--grocery-content));
  margin: 0 0 28px;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .cart-table-header {
    display: none;
  }

  .cart-row {
    grid-template-columns: 1fr;
    gap: 12px;
    padding: 16px;
    position: relative;
  }

  .col-item {
    grid-column: 1;
  }

  .col-price,
  .col-qty,
  .col-total {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .col-price::before {
    content: 'Price: ';
    font-size: 11px;
    color: #aaa;
    font-weight: 600;
  }
  .col-qty::before {
    content: 'Qty: ';
    font-size: 11px;
    color: #aaa;
    font-weight: 600;
  }
  .col-total::before {
    content: 'Total: ';
    font-size: 11px;
    color: #aaa;
    font-weight: 600;
  }

  .col-action {
    position: absolute;
    top: 16px;
    right: 16px;
  }

  .cart-summary-box {
    max-width: 100%;
  }

  .grocery-header {
    margin-top: 60px;
  }
}

@media (max-width: 480px) {
  .cart-item-img {
    width: 56px;
    height: 56px;
  }
}
</style>
